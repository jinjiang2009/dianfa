<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 *
 *
 * 招标
 * @date 2014/09/09
 * 
 * @author chenxiao
 */
class case_bid extends base {
	private $jdata = array ();
	private $max_size = 2097152;
	private $limit = 20;
	function __construct() {
		parent::__construct ();
		
		$this->jdata ["success"] = 0;
		$this->jdata ["message"] = "";
		$this->load->model ( "login_model", 'm_l' );
		$this->load->model ( "case_model", 'm_c' );
		$this->load->model ( "base_model", 'm_b' );
		$this->load->model ( "contract_model", 'm_co' );
		$this->load->library ( 'pagination' );
	
	}
	
	/**
	 * 案件招标
	 */
	function index() {
		$this->pv_monitor ();
		$p = array ();
		$data = array ();
		$data = $this->data;
		$caselist = $this->m_c->caselist ( array (
				'limit' => 12,
				'start' => 0,
				'status' => ' and status =1 ',
				'deadline_date' => ' and deadline_date >' . time () 
		) ); // 未完成招标
		$over = $this->m_c->caselist ( array (
				'limit' => 12,
				'start' => 0,
				'status' => ' and status =2 ',
				'deadline_date' => '' 
		) ); // 完成招标
		$data ['over'] = $over ['list'];
		$data ['type'] = $this->get_type ();
		$data ['region'] = $this->m_b->region ();
		$data ['lawyer'] = $this->recomand_lawyer (7,3); // 推荐律师
		
		$data ['rerritory'] = $this->m_c->territory (); // rerritory擅长领域
		$data ['case'] = $caselist ['list']; // 案件
		$data ['params'] = $this->get_params (); // 案件
		$data ['help'] = $this->m_co->recommend ( array (
				'type' => 7,
				'limit' => 7 
		) ); // 帮助中心
		$data['zbt']=array();
		if (! empty ($data ['case'] )&& !empty($this->data['user'])) {
			$c_ids = array();
			foreach ($data['case'] as $k =>$v){
				$c_ids []=$v['id'];
			}
			if(!empty($c_ids)){
				$l= $this->m_c->case_deliver(join(',', $c_ids),$this->data['user']['ucode']);
				$data['zbt'] = $l;
			}
		}
		$this->load->view ( 'default/caseadd', $data );
	}
	
	/**
	 * 添加案件招标
	 */
	function caseadd() {
		$p = array ();
		$p = $this->input->post ();
		$p ['name'] = htmlspecialchars ( urldecode ( $p ['name'] ) );
		$p ['content'] = htmlspecialchars ( urldecode ( $p ['content'] ) );
		$p ['nickname'] = htmlspecialchars ( urldecode ( $p ['nickname'] ) );
		$p ['search'] = htmlspecialchars ( urldecode ( $p ['search'] ) );
		if (empty ( $p ['mobile'] )) {
			$this->jdata ['success'] = 2; // 没有手机号
			$this->json_out ( $this->jdata );
		}
		/*
		 * if(!$this->judge_code($p['mobile'],$p['code'])){
		 * $this->jdata['success'] = 4;//验证码不正确 $this->json_out($this->jdata); }
		 */
		$p ['deadline_date'] = time () + 60 * 60 * 24 * $p ['deadline'];
		unset ( $p ['code'] );
		// $u = $this->mobile_exist($p['mobile']);
		$u = $this->getUserLoginId ( true );
		// $u='462D13F83BC63622D9B8F3302A203661';
		if (! empty ( $u )) { // 手机号存在对应账号
			$p ['ucode'] = $u;
			$c_r = $this->m_c->add_case ( $p );
			if (empty ( $c_r )) {
				$this->jdata ['success'] = 2; // 失败
				$this->json_out ( $this->jdata );
			}
			$inte = $this->integral_config ( 14 );
			$inte ['ucode'] = $u;
			$inte ['cause'] = 14;
			//$this->integral_change ( $inte ); // 添加积分及积分记录
			$this->jdata ['success'] = 1; // Ok
			$this->json_out ( $this->jdata );
		} else {
			$this->jdata ['success'] = 5; // 未登录
			$this->json_out ( $this->jdata );
		}
		$ucode = $this->create_guid ();
		$pwd = $this->random ( 6, 1 );
		$r_m = $this->mobile_login ( $p ['mobile'], $pwd, $p ['nickname'], $ucode );
		if (empty ( $r_m )) {
			$this->jdata ['success'] = 3; // 生成账号失败
			$this->json_out ( $this->jdata );
		}
		$p ['ucode'] = $ucode;
		$c_r = $this->m_c->add_case ( $p );
		if (empty ( $c_r )) {
			$this->jdata ['success'] = 2; // 失败
			$this->json_out ( $this->jdata );
		}
		$p ['ucode'] = $ucode;
		$inte = $this->integral_config ( 14 );
		$inte ['ucode'] = $ucode;
		$inte ['cause'] = 14;
		//$this->integral_change ( $inte ); // 添加积分及积分记录
		$this->send_code ( array (
				'mobile' => $p ['mobile'],
				'code' => $pwd,
				'type' => 1 
		) ); // 发送手机号密码
		$this->jdata ['success'] = 1; // Ok
	    echo json_encode($this->jdata);
		
		
		
	}
	
	/**
	 * 案件招标列表
	 */
	function caselist() {
		$this->pv_monitor ();
		$p = array ();
		$p = $this->get_params ();
		$get = $p;
		$data = array ();
		$data = $this->data;
		$p ['page'] = ! empty ( $p ['page'] ) ? intval ( $p ['page'] ) : 1;
		$p ['type'] = ! empty ( $p ['type'] ) ? intval ( $p ['type'] ) : 0; // 案件类型
		$p ['date'] = ! empty ( $p ['date'] ) ? intval ( $p ['date'] ) : 0; // 发布日期对应七类
		$p ['status'] = ! empty ( $p ['status'] ) ? intval ( $p ['status'] ) : 0; // 案源状态
		$p ['province'] = ! empty ( $p ['region'] ) ? $p ['region'] : 0; // 省市
		$p ['city'] = ! empty ( $p ['city'] ) ? intval ( $p ['city'] ) : 0; // 区县市
		
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['type'] = empty ( $p ['type'] ) ? '' : ' and type=' . $p ['type'];
		$p ['city'] = empty ( $p ['city'] ) ? '' : ' and city=' . $p ['city'];
		$p ['province'] = empty ( $p ['province'] ) ? '' : ' and province="' . $p ['province'] . '" ';
		switch ($p ['date']) {
			case 1 :
				$today = date ( 'Y-m-d h:i:s', time () - 60 * 60 * 24 * 1 );
				$p ['date'] = ' and createtime >= "' . $today . '" ';
				
				break;
			case 2 :
				$today = date ( 'Y-m-d h:i:s', time () - 60 * 60 * 24 * 2 );
				$p ['date'] = ' and createtime >= "' . $today . '" ';
				break;
			case 3 :
				$today = date ( 'Y-m-d h:i:s', time () - 60 * 60 * 24 * 7 );
				$p ['date'] = ' and createtime >= "' . $today . '" ';
				break;
			case 4 :
				$today = date ( 'Y-m-d h:i:s', time () - 60 * 60 * 24 * 15 );
				$p ['date'] = ' and createtime >= "' . $today . '" ';
				break;
			case 5 :
				$today = date ( 'Y-m-d h:i:s', time () - 60 * 60 * 24 * 30 );
				$p ['date'] = ' and createtime >= "' . $today . '" ';
				break;
			case 6 :
				$today = date ( 'Y-m-d h:i:s', time () - 60 * 60 * 24 * 60 );
				$p ['date'] = ' and createtime >= "' . $today . '" ';
				break;
			default :
				$p ['date'] = '';
				break;
		}
		switch ($p ['status']) {
			case 1 :
				$p ['status'] = ' and bid = 0 ';
				break;
			case 2 :
				$p ['status'] = ' and status in(1,2) and deadline_date>' . time () . ' ';
				break;
			case 3 :
				$p ['status'] = ' and status = 3 or deadline_date<' . time () . ' ';
				break;
			case 4 :
				$p ['status'] = ' and deadline =3 and deadline_date>' . time () . ' ';
				break;
			case 0 :
				$p ['status'] = '';
				break;
		}
		
		$re = $this->m_c->casel ( $p );
		
		$data ['list'] = $re ['list'];
		$data ['type'] = $this->get_type ();
		$conmodel = '';
		$url = '';
		$data ['regionurl'] = '';
		$data ['amanuensisurl'] = '';
		$data ['institutionurl'] = '';
		$data ['categoryurl'] = '';
		if (! empty ( $get )) {
			unset ( $get ['page'] );
			foreach ( $get as $k => $v ) {
				if (! empty ( $v ) && $v != 0 && $v != '') {
					$url .= $k . '=' . $v . '&';
				}
			}
			$conmodel = $get;
			$data ['regionurl'] = ! empty ( $get ['region'] ) ? $get ['region'] : 0;
			$data ['amanuensisurl'] = ! empty ( $get ['amanuensis'] ) ? $get ['amanuensis'] : 0;
			$data ['institutionurl'] = ! empty ( $get ['institution'] ) ? $get ['institution'] : 0;
			$data ['categoryurl'] = ! empty ( $get ['category'] ) ? $get ['category'] : 0;
		}
		
		$this->load->model ( "judgement_model", 'judge' );
		$data ['region'] = $this->judge->getRegion ();
		$data ['alpha'] = alpha ();
		$data ['para'] = $get;
		$config = $this->page_config ( $re ['total'] [0] ['total'], $this->limit, STATIC_URL . 'case_bid/caselist?' . $url );
		$this->pagination->initialize ( $config );
		$data ['page'] = $this->pagination->create_links ();
		$data['zbt']=array();
		if (! empty ( $data ['list'] )&& !empty($this->data['user'])) {
			$c_ids = array();
			foreach ($data['list'] as $k =>$v){
				$c_ids []=$v['id'];
			}	
			if(!empty($c_ids)){
				$l= $this->m_c->case_deliver(join(',', $c_ids),$this->data['user']['ucode']);
				$data['zbt'] = $l;
			}
		}
		$this->load->view ( 'default/caselist', $data );
	}
	
	/**
	 * 发布招标生成网站账号
	 * 
	 * @param str $mob
	 *        	手机号
	 * @param str $pwd
	 *        	密码
	 */
	function mobile_login($mob, $pwd, $nick, $ucode) {
		$r = $this->mobile_exist ( $mob, 1 );
		if ($r != 1)
			return false;
		$p = array (
				'mobile' => $mob,
				'pwd' => md5 ( $pwd ),
				'nickname' => $nick,
				'isvalida_mobile' => 1,
				'regip' => $this->getIP (),
				'ucode' => $ucode 
		);
		return $this->m_l->add_member ( $p );
	}
	
	/**
	 * 发送验证码
	 */
	function get_code() {
		$mobile = $this->get_params ();
		$r = $this->mobile_code ( $mobile ['mobile'] );
		$this->jdata ["success"] = $r;
		$this->json_out ( $this->jdata );
	}
	
	/**
	 * 上传文件
	 */
	function upload() {
		if ($_FILES ['fileImage'] ['error'] == 0) {
			$up_filename = $_FILES ['fileImage'] ['name'];
			$tmp = explode ( ".", $up_filename );
			$suffix = trim ( array_pop ( $tmp ) );
			$filesize = $_FILES ['fileImage'] ['size'];
			if ($filesize >= $this->max_size) {
				$this->jdata ["success"] = 6;
				$this->jdata ["message"] = "上传的附件能大于2M";
				$this->json_out ( $this->jdata );
			}
			
			$path = 'upload/affix/' . date ( "Ymd" );
			if (! file_exists ( $path )) {
				// 检查是否有该文件夹，如果没有就创建，并给予最高权限
				mkdir ( "$path", 0700 );
			} // END IF
			
			if ($_FILES ["fileImage"] ["name"]) {
				$today = date ( "YmdHis" ); // 获取时间并赋值给变量
				$file2 = $path . '/' . $today . rand ( 100000, 999999 ) . '.' . $suffix; // 图片的完整路径
				$flag = 1;
			} // END IF
			if ($flag)
				$result = move_uploaded_file ( $_FILES ["fileImage"] ["tmp_name"], $file2 );
			$oimg = $this->input->get ( 'oimg' );
			if (! empty ( $oimg ))
				unlink ( $this->input->get ( 'oimg' ) );
			$this->jdata ["success"] = 1;
			$this->jdata ["message"] = "上传成功";
			$this->jdata ["name"] = $file2;
			$this->json_out ( $this->jdata );
			// 特别注意这里传递给move_uploaded_file的第一个参数为上传到服务器上的临时文件
		} // END IFb
	}
	
	/**
	 * 添加成功
	 */
	function suc() {
		$data = $this->data;
		$data ['stype'] = 1;
		$get = $this->input->get('name');
		#发送邮件提醒
		$this->load->library('email');//加载邮件类
		$config['protocol'] = 'smtp';//邮件发送协议
		$config['smtp_host'] = 'smtp.126.com';//SMTP服务器地址
		$config['smtp_user'] = 'jinjiang2009@126.com';
		$config['smtp_pass'] = 'qwe2092223';//smtp密码
		$this->email->initialize($config);
		$this->email->from('jinjiang2009@126.com','北京点法网信息技术有限公司');//来自什么邮箱
		$this->email->to('2479307496@qq.com,zhouqianqian@idianfa.com');//发到什么邮箱
		$this->email->subject('点法网--案件招标,请注意查看');//邮件主题
		$this->email->message("请工作人员后台查看最新案件招标,案件标题:".$get);//邮件内容
		$this->email->print_debugger();//返回包含邮件内容的字符串，包括EMAIL正文。用于调试
		$this->email->send();
		$this->load->view ( 'default/case_ok', $data );
	}
	
	/**
	 * 合同意见
	 */
	function suggest() {
		$p = array ();
		$p = $this->input->post ();
		$p ['name'] = htmlspecialchars ( urldecode ( $p ['name'] ) );
		$p ['content'] = htmlspecialchars ( urldecode ( $p ['content'] ) );
		$p ['ucode'] = $this->getUserLoginId ( true );
		$r = $this->m_c->sdd_suggest ( $p );
		if (! empty ( $r )) {
			$this->jdata ['success'] = 1;
			$this->json_out ( $this->jdata );
		}
	}
	
	/**
	 * 案件
	 */
	function case_info() {
		$data = array ();
		$p = array ();
		$data = $this->data;
		$id = $this->input->get ( 'id' );
		$id = intval ( $id );
		$data ['info'] = $this->m_c->case_info ( $id );
		$data ['type'] = $this->get_type ();
		$lawyer = $this->m_c->case_bid ( $id ); // 查询投标律师
		$data ['m_status'] = 0;
		$c = $this->getUserLoginId ( true );
		if (! empty ( $lawyer )) {
			$p ['ids'] = '';
			foreach ( $lawyer as $k => $v ) {
				if($data ['info'][0]['mobile_show']==1){
					if ($v ['ucode'] === $c){
						$data ['m_status'] = 1;
					}
				}else if($data ['info'][0]['mobile_show']==2){
					if ($v ['ucode'] === $c && $v ['status'] === 2){
						$data ['m_status'] = 1;
					}
				}
				
				$p ['ids'] .= empty ( $p ['ids'] ) ? $v ['ucode'] : '","' . $v ['ucode'];
			}
			$p ['page'] = ! empty ( $p ['page'] ) ? intval ( $p ['page'] ) : 1;
			if (! empty ( $p ['ids'] )) {
				$p ['ids'] = ' and ucode in ("' . $p ['ids'] . '") ';
				$p ['limit'] = $this->limit;
				$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
				$get = $this->get_params ();
				$url = '';
				if (! empty ( $get )) {
					unset ( $get ['page'] );
					foreach ( $get as $k => $v ) {
						if (! empty ( $v ) && $v != 0 && $v != '') {
							$url .= $k . '=' . $v . '&';
						}
					}
				}
				$re = $this->m_c->case_lawyer ( $p );
				$config = $this->page_config ( $re ['total'] [0] ['total'], $this->limit, STATIC_URL . 'case_bid/case_info?' . $url );
				$this->pagination->initialize ( $config );
				$data ['page'] = $this->pagination->create_links ();
				$data ['total'] = $re ['total'] [0] ['total'];
				$data ['list'] = $re ['list'];
				$data ['rerritory'] = $this->m_c->territory ();
			}
		}
		$this->load->view ( 'default/caseinfo', $data );
	}
	
	/**
	 * 律师投标
	 */
	function lawyer_bid() {
		$p = array ();
		$data = array ();
		$data = $this->data;
		$p = $this->get_params ();
		$p ['id'] = intval ( $p ['id'] );
		if (empty ( $p ['id'] )) {
			$data ['prompt'] = '无效';
			$this->load->view ( 'default/prompt', $data );
			return false;
		}
		$info = $this->m_c->case_info ( $p ['id'] );
		if (empty ( $info )) {
			$data ['prompt'] = '无案件';
			$this->load->view ( 'default/prompt', $data );
			return false;
		}
		if ($info [0] ['status'] == 3 || $info [0] ['status'] == 4 || time () > $info [0] ['deadline_date']) {
			$data ['prompt'] = '案件已截止';
			$this->load->view ( 'default/prompt', $data );
			return false;
		}
		$integral_config = $this->integral_config ( 21 );
		if (! empty ( $data ['user'] )) {
			if ($integral_config ['num'] > $data ['user'] ['integral']) {
				$data ['m_integral'] = 1; // 积分不足
				$data ['prompt'] = '你的相关积分不足';
				$this->load->view ( 'default/tip', $data );
				return false;
			}
		} else {
			$data ['m_integral'] = 2; // 未登录
			header ( "Location:" . STATIC_URL . 'login' );
		}
		$bid_p = array (
				'tid' => $p ['id'],
				'message' => $p ['message'],
				'ucode' => $this->getUserLoginId ( true ),
				'is_show' => 1 
		);
		$id = $this->m_c->lawyer_bid ( $bid_p );
		$integral_config ['ucode'] = $this->getUserLoginId ( true );
		$integral_config ['cor_id'] = $id;
		$integral_config ['cause'] = 21;
		$this->integral_change ( $integral_config );
		header ( "Location:" . STATIC_URL . 'case_bid/case_info?id='.$p ['id'] );
	}

}