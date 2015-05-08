<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 *
 * @author chenxiao
 * @date 2014/09/09
 */
class base extends CI_Controller {
	private $bank = array(18=>'BOCBTB',19=>'ICBCBTB',20=>'CMBBTB',21=>'CCBBTB',22=>'ABCBTB',23=>'SPDBB2B',
						  1=>'BOCB2C',2=>'ICBCB2C',3=>'CMB',4=>'CCB',5=>'ABC',6=>'SPDB',7=>'CIB',8=>'GDB',9=>'CMBC',
						  10=>'CITIC',11=>'HZCBB2C',12=>'SHBANK',13=>'NBBANK',14=>'SPABANK',15=>'BJBANK',16=>'POSTGC',
						  17=>'COMM'
							);
	function __construct() {
		parent::__construct ();
		define ( "PRE_MOBILE", "/^13[0-9]{9}$|15[0-9]{9}$|18[0-9]{9}$|14[57]{1}\d{8}$/" ); // 正则匹配手机号
		$this->load->model ( "base_model",'m_b' );
	}
	
	/**
	 * pv 监测
	 */
	function pv_monitor(){
		$p = array();
		$p = array_merge($p,$this->data);
		$p['date'] =  date('Y-m-d');
		$r = $this->m_b->pv_monitor($p);
	}
	
	/**
	 * 生成订单
	 */
	function c_order(){
		return time().rand(10000, 99999);
	}
	
	/**
	 * 用户唯一标识
	 * @return string
	 */
	function create_guid() {
		$charid = strtoupper(md5(uniqid(mt_rand(), true)));
		return $charid;
	}
	
	/**
	 * 获取前台用户ucode没 登陆跳转登陆页
	 * @author chengxiao
	 * @param  bool $type 是否跳转页面
	 */
	function getUserLoginId($type = false) {
		
		if (!empty( $this->data['user']['ucode'] )) {
			return $this->data['user']['ucode'];
		} else {
			if ($type) {
				return false;
			} else {
 				header ( "Location:".STATIC_URL.'login' );
				exit ();
			}
		}
	}
	
	
	function Post($curlPost,$url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
		$return_str = curl_exec($curl);
		curl_close($curl);
		return $return_str;
	}
	
	function xml_to_array($xml){
		$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches)){
			$count = count($matches[0]);
			for($i = 0; $i < $count; $i++){
				$subxml= $matches[2][$i];
				$key = $matches[1][$i];
				if(preg_match( $reg, $subxml )){
					$arr[$key] = $this->xml_to_array( $subxml );
				}else{
					$arr[$key] = $subxml;
				}
			}
		}
		return $arr;
	}
	
	/**
	 * 生成验证码 
	 * @param int $length 长度
	 * @param int $numeric 0、字符+数字 1、数字
	 * @return string
	 */
	function random($length = 6 , $numeric = 0) {
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		if($numeric) {
			$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
		} else {
			$hash = '';
			$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++) {
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		return $hash;
	}
	
	/**
	 * send code 
	 * $mobile =18911758995;
	 * $mobile_code = $this->random(4,1);
	 * @param str $mobile 手机号
	 * @param str $code 验证码
	 * @param str $type 类型
	 */
	function send_code($p){
		session_start();
		$target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
		if(empty($p['type'])){
			$content = "您的验证码是：".$p['code']."。请不要把验证码泄露给其他人。";
		}else if($p['type']==1){
			$content = "恭喜您生成点法网账号是：".$p['mobile']."，密码：".$p['code']."请不要泄露给其他人。";
		}
		$post_data = "account=cf_dianfawang&password=qwe19890202&mobile=".$p['mobile']."&content=".rawurlencode($content);
		//密码可以使用明文密码或使用32位MD5加密
		$gets =  $this->xml_to_array($this->Post($post_data, $target));
		return $gets;
	}
	
	/**
	 * 验证手机 
	 * @param str $mobile
	 * @return number 见注释
	 */
	
	function mobile_code($mobile){
		if(empty($mobile))return 2;//手机号为空
		if(!preg_match(PRE_MOBILE,$mobile)){
			return 3;//手机号不正确
		}
		$t = time();
		$p = array();
		$p['code'] = $this->random(4,1);
		$p['mobile'] = $mobile;
		$p['st'] = $t;
		$p['et'] = $t+60*10;
		$re = $this->m_b->judge_code($p,$t);
		if(empty($re[0]['code'])){
			$r = $this->send_code($p);
			if($r['SubmitResult']['code']==2){
				if($this->m_b->add_code($p))
				return 1;//ok
			}
		}else{
			if($re[0]['code']>($t+60)){
				$p['id'] = $re[0]['id'];
				$p['code'] = $re[0]['code'];
				$r = $this->send_code($p);
				if($r['SubmitResult']['code']==2){
					if($this->m_b->edit_code($p))
					return 1;//ok
				} 
			}
			return 4;//重复发送
		}
		return 5;//发送失败
	}
	
	/**
	 * 匹配验证码是否正确
	 * @param str $mobile 手机号
	 * @param str $code 验证码
	 * @return boolean true 验证码正确 FALSE 验证码错误
	 */
	function judge_code($mobile,$code){
		$p['mobile'] = $mobile;
		$t = time();
		$re = $this->m_b->judge_code($p,$t);
		if(!empty($re)){
			if($code==$re[0]['code']);
			return true;
		}
		return false;
	}
	
	/**
	 * 判断手机号是否注册或绑定账号
	 * @param str $mobile
	 */
	function mobile_exist($mobile,$t=0){
		if(!preg_match(PRE_MOBILE,$mobile)){
			return false;
		}
		$r = $this->m_b->mobile_exist ( $mobile);
		if(!empty($r)){
			return $r[0]['ucode'];
		}else{
			if($t==1){
				return 1;
			}
			return false;
		}
		
	}
	
	/**
	 * 获取用户真实 IP
	 */
	function getIP(){
		if (isset($_SERVER)){
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
				$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
				$realip = $_SERVER["HTTP_CLIENT_IP"];
			} else {
				$realip = $_SERVER["REMOTE_ADDR"];
			}
		} else {
			if (getenv("HTTP_X_FORWARDED_FOR")){
				$realip = getenv("HTTP_X_FORWARDED_FOR");
			} else if (getenv("HTTP_CLIENT_IP")) {
				$realip = getenv("HTTP_CLIENT_IP");
			} else {
				$realip = getenv("REMOTE_ADDR");
			}
		}
		return $realip;
	}
	
	/**
	 *  增加或减少积分插入日志
	 * @param arr $p 必须存在
	 * array(
	 * ucode=>'',用户唯一标示
	 * cause=>'',加减分类型
	 * cor_id=>'',相关id
	 * num='',分数
	 * type='',类型
	 * )
	 */
	function integral_change($p){
		if(empty($p['ucode'])||empty($p['cause'])||empty($p['num'])||empty($p['type'])){
			return false;
		}
		$re = $this->m_b->integral_change($p);
		return $re;
	}
	
	/*
	 * 统一分页配置
	*/
	function page_config($total,$perpage,$url){
		$config = array(
				
				'base_url'=>$url,
				'total_rows'=>$total,
				'per_page'=>$perpage,
				//'uri_segment'=>'4',//设为页面的参数，如果不添加这个参数分页用不了
				'num_links'=>5,
				'first_link'=>'首页',
				'prev_link'=>'上一页',
				'next_link'=>'下一页',
				'last_link'=>'最后一页',
				'use_page_numbers'=>TRUE,
				'anchor_class'=>"",
				'cur_tag_open'=>'<a class="now">',
			    'cur_tag_close'=>'</a>',
				'anchor_class'=>' ',
				'query_string_segment'=>'page'
		);
		return $config;
	}
	
	/**
	 * 推荐律师
	 */
	function recomand_lawyer($l=8,$t=1){
		$this->load->model ( "case_model",'m_ca' );
		$_uc = $this->m_ca->lawyer_uc(array('limit'=>$l,'type'=>$t));
		
		if($t == 3){
			$three = $this->m_ca->lawyer_uc2(array('limit'=>$l,'type'=>$t));
		}
	
		$uc_s = join('","', $_uc);
		if(!empty($_uc)){
			$lawyer = $this->m_ca->recomand_lawyer($uc_s);
			if(!empty($lawyer)){
				foreach ($_uc as $k=>$v){
					if(!empty($lawyer[$v])){
						$_uc[$k] = $lawyer[$v];
						if($t==3){
							/* $_uc[$k]['img'] = $three[$v]['img'];*/
							$_uc[$k]['url'] = $three[$v]['url'];
						} 
					}
				}
			}
			return $_uc;
		}
	}
	
	
	/**
	 * 发送邮件函数
	 * @author zhousheng
	 * @param string $to 接受者邮箱  arrray()
	 * @param string $subject 标题
	 * @param string $content 内容
	 * @param bool $ishtml 是否发送html 默认false
	 */
	public function send_email_method($to, $subject, $content, $ishtml = false) {
		$this->load->library ( 'phpmailer' );
		$config = array (
				// 'SMTPDebug'=>'2',
				// 'Debugoutput'=>'html',
				'Username'=>'dianfawang@idianfa.com',
				'Password'=>'dfwcj528',
				'Port'=>'465',
				'Host'=>'ssl://smtp.exmail.qq.com',
				'SMTPAuth'=>true,
				'Mailer'=>'smtp',
				'CharSet'=>'utf-8',
				'Encoding' => 'base64'
		);
		 $this->phpmailer->instance($config);
		 $this->phpmailer->SetFrom('dianfawang@idianfa.com','天天提升网');
		for($i=0;$i<count($to);$i++){
			$this->phpmailer->AddAddress($to[$i],$to[$i]);
		}
		$this->phpmailer->Subject = $subject;
		$this->phpmailer->Body = $content;
		if ($ishtml) {
			$this->phpmailer->IsHTML ( true );
			$this->phpmailer->AltBody = "text/html";
		}
		if ( $this->phpmailer->Send ()) {
			return true;
		} else {
			return $this->phpmailer->ErrorInfo;
		}
	}
	
	
	
	
	
	

}