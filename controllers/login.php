<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class login extends base {
	
	private $qq_config = array ( // qq第三方登陆验证等待申请
			'app_id' => "101162431",
			'app_secret' => "f6563e1f33d7bb4f69624d40b482f2fe",
			'my_url' => "http://www.idianfa.com/login/qq_login" 
	);
	private $sina_config = array ( // 新浪第三方登陆等待申请
			"wb_akey" => '347553969',
			"wb_skey" => '77c24a197aea08c6a557a1fb08fd8a3c',
			'callbace_url' => "http://www.idianfa.com/login/sina_login" 
	);
	function __construct() {
		parent::__construct ();
		$this->load->model ( "login_model", 'login' );
	
	}
	
	/**
	 * 登录页
	 */
	public function index() {
		$data = $this->data; // p($this->session->userdata('login_type'));die;
		
		if ($data ['login_type'] != 4) {
			header_output ( '您已登陆,请退出后再登录！' );
		}
		$this->load->view ( 'default/login.php', $data );
	}
	/*
	 * 异步验证咨询中心的数据
	 */
	public function verify() {
		session_start ();
		$post = $this->input->post ();
		if ($_SESSION ["helloweba_char"] == $post ['vcode']) {
			
			$info = $this->login->verifyLogin ( $post ['uname'], $post ['pass'] );
			if (! empty ( $info )) {
				$msg = 1;
				$this->session->set_userdata ( 'user', $info );
				
				$this->session->set_userdata ( 'login_type', $info ['identity'] );
			} else {
				$msg = 2;
			}
		} else {
			$msg = 3;
		}
		echo $msg;
	}
	/*
	 * 找回密码验证
	 */
	public function veri(){
		$data = $this->data;
		$post = $this->input->post();
		$data['email'] = $post['email'];
		$rs = $this->login->verifyFind($post);
		if(!empty($rs)){
			#发邮件
			session_start ();
			$_SESSION["findlaw"] = rand(0,999999);
			$this->load->library('email');//加载邮件类
			$config['protocol'] = 'smtp';//邮件发送协议
			$config['smtp_host'] = 'smtp.126.com';//SMTP服务器地址
			$config['smtp_user'] = 'jinjiang2009@126.com';
			$config['smtp_pass'] = 'qwe2092223';//smtp密码
			$this->email->initialize($config);
			$this->email->from('jinjiang2009@126.com','北京点法网信息技术有限公司');//来自什么邮箱
			$this->email->to($post['email']);//发到什么邮箱
			$this->email->subject('北京点法网信息技术有限公司找回密码');//邮件主题
			$this->email->message("请点击下面的链接找回密码".STATIC_URL.'login/newpwd?verfifycode='. base64_encode($_SESSION["findlaw"]).'&username='. base64_encode($post['username']));//邮件内容
			$this->email->print_debugger();//返回包含邮件内容的字符串，包括EMAIL正文。用于调试
			if($this->email->send()){//发送email，根据发送结果，成功返回true,失败返回false,就可以用它判断局域
				$this->load->view ( 'default/pwdsuccess.php', $data );
			}else{
				echo '1111';
			}
			die;
			
		}else{
			echo 1111;
			
		}
	}
	public function suc(){
		$data = $this->data;
		$post = $this->input->get();
		$data['email'] = $post['email'];
		$this->load->view ( 'default/pwdsuccess.php', $data );
	}
	/*
	 * 修改密码页面
	 */
	public function newpwd(){
		session_start ();
		$data = $this->data;
		$get = $this->input->get();
		if(base64_decode($get['verfifycode'])==$_SESSION["findlaw"]){
			$data['username'] = base64_decode($get['username']);
			$this->load->view ( 'default/modifypwd.php', $data );
		}else{
			$this->load->view ( 'default/wrong.php', $data );
		}
		
	}
	/*
	 * 邮箱修改密码
	 */
	public function verifypwd(){
		$post = $this->input->post();
		$rs = $this->login->verifyUsername($post['username']);
		if(!empty($rs)){
			$this->login->updatePwd($post['username'],$post['pwd']);
			echo 9999;
			
		}
	}
	/*
	public function test(){
		$this->load->library('email');//加载邮件类
		$config['protocol']= 'smtp';//邮件发送协议
		//$config['smtp_port'] = '25';
		
		//$config['smtp_host'] = 'smtp.exmail.qq.com';
		//$config['smtp_port'] = '465';
		//$config['smtp_user'] = 'dianfawang@idianfa.com';//smtp用户账号
		//$config['smtp_pass'] = 'dfwcj528';//smtp密码
		//$config['_encoding'] = 'base64';
		//$config['_smtp_auth	'] = true;
		$config['smtp_host'] = 'smtp.126.com';//SMTP服务器地址
		$config['smtp_user'] = 'jinjiang2009@126.com';
		$config['smtp_pass'] = 'qwe19890202';//smtp密码
		
		$this->email->initialize($config);
		
		
		$this->email->from('jinjiang2009@126.com');//来自什么邮箱
		$this->email->to('dianfawang@idianfa.com');//发到什么邮箱
		//$this->email->from('dianfawang@idianfa.com');//来自什么邮箱
		//$this->email->to('jinjiang2009@126.com');
		$this->email->subject('发送邮件测试');//邮件主题
		$this->email->message("城市:");//邮件内容
		$this->email->print_debugger();//返回包含邮件内容的字符串，包括EMAIL正文。用于调试
		if($this->email->send()){//发送email，根据发送结果，成功返回true,失败返回false,就可以用它判断局域
			echo '发送成功';
		}else{
			echo '发送失败';
		}
	}
	*/
	/**
	 * 主页异步登陆
	 */
	public function home_login() {
		$post = $this->input->post ();
		$info = $this->login->verifyLogin ( $post ['uname'], $post ['pass'] );
		if (! empty ( $info )) {
			$total=$this->login->m_num($info['ucode']);
			$msg = 1;
			$this->session->set_userdata ( 'user', $info );
			$this->session->set_userdata ( 'login_type', $info ['identity'] );
		} else {
			$msg = 2;
			$info = array ();
		}
		$this->json_out ( array (
				'msg' => $msg,
				'user' => $info,
				'total'=> empty($total[0]['total'])?'':$total[0]['total']
		) );
	}
	/*
	 * 退出登录
	 */
	public function longout() {
		$this->session->sess_destroy ();
		header ( "Location:" . STATIC_URL );
	}
	/*
	 * 找回密码
	 */
	public function findpwd(){
		$data = $this->data;
		if($data['login_type']==4){
			$this->load->view ( 'default/findpwd.php', $data );
		}else{
			$this->load->view ( 'default/wrong.php', $data );
		}
	}
	
	/**
	 * qq登陆
	 */
	function qq_login() {
		$url = $this->input->get('refer',0);
		$app_id = $this->qq_config ['app_id'];
		// 应用的APPKEY
		$app_secret = $this->qq_config ['app_secret'];
		// 成功授权后的回调地址
		$my_url = $this->qq_config ['my_url'].'?refer='.$url;
		// Step1：获取Authorization Code
		session_start ();
		$code =empty($_REQUEST ["code"])?'':$_REQUEST ["code"] ;
		if (empty ( $code )) {
			// state参数用于防止CSRF攻击，成功授权后回调时会原样带回
			$_SESSION ['state'] = md5 ( uniqid ( rand (), TRUE ) );
			// 拼接URL
			$dialog_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" . $app_id . "&redirect_uri=" . urlencode ( $my_url ) . "&state=" . $_SESSION ['state'];
			echo ("<script> top.location.href='" . $dialog_url . "'</script>");
		}
		// Step2：通过Authorization Code获取Access Token
		if ($_REQUEST ['state'] == $_SESSION ['state']) {
			// 拼接URL
			$token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&" . "client_id=" . $app_id . "&redirect_uri=" . urlencode ( $my_url ) . "&client_secret=" . $app_secret . "&code=" . $code;
			$response = file_get_contents ( $token_url );
			if (strpos ( $response, "callback" ) !== false) {
				$lpos = strpos ( $response, "(" );
				$rpos = strrpos ( $response, ")" );
				$response = substr ( $response, $lpos + 1, $rpos - $lpos - 1 );
				$msg = json_decode ( $response );
				if (isset ( $msg->error )) {
					$this->index ();
					return false;
				}
			}
			// Step3：使用Access Token来获取用户的OpenID
			$params = array ();
			parse_str ( $response, $params );
			$graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $params ['access_token'];
			$access_token = $params ['access_token'];
			$str = file_get_contents ( $graph_url );
			if (strpos ( $str, "callback" ) !== false) {
				$lpos = strpos ( $str, "(" );
				$rpos = strrpos ( $str, ")" );
				$str = substr ( $str, $lpos + 1, $rpos - $lpos - 1 );
			}
			$user = json_decode ( $str );
			if (isset ( $user->error )) {
				$this->index ();
				return false;
			}

			$detail = $this->login->getqqlogin ( $user->openid );
			if (empty ( $detail )) {
				$user_detail = file_get_contents ( 'https://graph.qq.com/user/get_user_info?access_token=' . $access_token . '&oauth_consumer_key=' . $app_id . '&openid=' . $user->openid );
				$user_detail = json_decode ( $user_detail );
				if (isset ( $user_detail->error )) {
					$this->index ();
					return false;
				}
				$login = array (
						'userkey' => $user->openid,
						'regip' => $this->getIP (),
						'loginfrom' => 1,
						//'integral' => 30,
						'username' => $this->getNickname ( $user_detail->nickname ),
						'ucode' => $this->create_guid () 
				);
				$re = $this->login->insertTable ( 'df_member', $login );
				
				if (! empty ( $re )) {
					
					$int = $this->integral_config(26);
					$ic = array(
							'ucode'=>$login['ucode'],
							'cause'=>26,
							'num'=>$int['num'],
							'type'=>1,
					);
					$this->integral_change($ic);
					
					$info = $this->login->getqqlogin ( $user->openid );
					$this->session->set_userdata ( 'user', $info [0] );
					$this->session->set_userdata ( 'login_type', $info [0] ['identity'] );
					header ( "Location:".js_unescape($url) );
					return false;
				} else {
					$this->index ();
					return false;
				}
			
			} else {
				$this->session->set_userdata ( 'user', $detail [0] );
				$this->session->set_userdata ( 'login_type', $detail [0] ['identity'] );
				header ( "Location:".js_unescape($url) );
				return false;
			}
		
		} else {
			$this->index ();
			return false;
		}
	}
	
	/**
	 * 新浪微博登陆
	 */
	function sina_login() {
		$url = $this->input->get('refer',0);
		$url = js_unescape($url);
		include_once ('saetv2.ex.class.php');
		$o = new SaeTOAuthV2 ( $this->sina_config ['wb_akey'], $this->sina_config ['wb_skey'] );
		if (isset ( $_REQUEST ['code'] )) {
			if (empty ( $_COOKIE ['SINA_CODE'] ) || $_COOKIE ['SINA_CODE'] != $_REQUEST ['code']) {
				setcookie ( "SINA_CODE", $_REQUEST ['code'], time () + time (), '/', H_COOKIE_DOMAIN );
			} else {
				$this->index ();
				exit ();
			}
			$keys = array ();
			$keys ['code'] = $_REQUEST ['code'];
			$keys ['redirect_uri'] = $this->sina_config ['callbace_url'];
			$token = $o->getAccessToken ( 'code', $keys );
			$u = $this->uri->segment(3);
			$u = base64_decode($u);
			if (! empty ( $token ['uid'] )) {
				$detail = $this->login->getqqlogin ( $token ['uid'] );
				if (empty ( $detail )) {
					$user_detail = file_get_contents ( 'https://api.weibo.com/2/users/show.json?access_token=' . $token ['access_token'] . '&uid=' . $token ['uid'] . '&source=' . $this->sina_config ['wb_akey'] );
					$user_detail = json_decode ( $user_detail );
					if (empty ( $user_detail->id )) {
						$this->index ();
						exit ();
					}
					$login = array (
							'userkey' => $token ['uid'],
							'regip' => $this->getIP (),
							'username' => $this->getNickname ( $user_detail->screen_name ),
							'loginfrom' => 2,
							//'integral' => 30,
							'ucode' => $this->create_guid () 
					);
			      $re = $this->login->insertTable ( 'df_member', $login );
					if (! empty ( $re )) {
						$int = $this->integral_config(26);
						$ic = array(
								'ucode'=>$login['ucode'],
								'cause'=>26,
								'num'=>$int['num'],
								'type'=>1,
						);
						$this->integral_change($ic);
						
						$info = $this->login->getqqlogin ( $user->openid );
						$this->session->set_userdata ( 'user', $info [0] );
						$this->session->set_userdata ( 'login_type', $info [0] ['identity'] );
						header ( "Location:".$u );
						return false;
					} else {
						$this->index ();
						return false;
					}
				
				} else {
					$this->session->set_userdata ( 'user', $detail [0] );
					$this->session->set_userdata ( 'login_type', $detail [0] ['identity'] );
					header ( "Location:".$u );
					return false;
				}
			
			} else {
				$this->index ();
				return false;
			}
			// fclose($handle);
		} else {
			$code_url = $o->getAuthorizeURL ( $this->sina_config ['callbace_url'].'/'.base64_encode($url) );
			header ( "Location:" . $code_url  );
		
		}
	}
	
	/**
	 * 根据昵称获得符合规格的昵称
	 *
	 * @param str $nickname        	
	 * @return str
	 */
	function getNickname($nickname) {
		if (! empty ( $nickname )) {
			$preg = "/[^x00-xff]/";
			$j = 0;
			$name = '';
			for($i = 0; $i < mb_strlen ( $nickname ); $i ++) {
				if ($j >= 8) {
					return $name;
				}
				$int = preg_match ( $preg, mb_substr ( $nickname, $i, 1 ) );
				if ($int) {
					if ($j >= 7) {
						return $name;
					}
					$name .= mb_substr ( $nickname, $i, 1 );
					$j += 2;
				} else {
					$name .= mb_substr ( $nickname, $i, 1 );
					++ $j;
				}
			}
			return $name;
		}
	}
	
	function test($ids){
		$c = $this->login->exc($ids);
		if(empty($c)){
			return false;
		}
		$t =$c[0]['content'];
		if(empty($t)){
			return false;
		}
		//$t = explode('<div class="hr1" style="width:100%; border-bottom:2px solid #F00; height:2px;"></div>', $t);
		//$t1 = preg_replace("/<div id=\"wsTime\">(.*?)<\/div>/", ' ', $t);
		//var_dump($t);
		$t = preg_replace("/<div class=\"hr1\" style=\"width:100%; border-bottom:2px solid #F00; height:2px;\"><\/div>/", '', $t);
		$t = preg_replace("/<span>提交时间：([\d-]*?)<\/span>/", '', $t);
		$t = preg_replace("/<input name=\"PrintPage\" type=\"button\" id=\"PrintPage\" value=\"打印预览\" class=\"btn\" \/>/", '', $t);
		$t = preg_replace("/<input name=\"DownPDF\" type=\"button\" id=\"DownPDF\" value=\"文书下载\" class=\"btn\" \/>/", '', $t);
		//法院
		preg_match_all ("/<div style='TEXT-ALIGN: center; LINE-HEIGHT: 25pt; MARGIN: 0.5pt 0cm; FONT-FAMILY: 黑体; FONT-SIZE: 18pt;'>(.*?)<\/div>/", $t, $m);
		//案号
		preg_match_all ("/<div style='TEXT-ALIGN: right; LINE-HEIGHT: 25pt; MARGIN: 0.5pt 0cm;  FONT-FAMILY: 宋体;FONT-SIZE: 15pt; '>(.*?)<\/div>/", $t, $s);
		//时间
		preg_match_all ("/<div style='TEXT-ALIGN: right; LINE-HEIGHT: 25pt; MARGIN: 0.5pt 36pt 0.5pt 0cm;FONT-FAMILY: 宋体; FONT-SIZE: 15pt;'>(.*?)<\/div>/", $t, $d);
		if(empty($m[1][0])&&empty($s[1][0])&&empty($d[1][(count($d[1])-2)])){
			//法院
			preg_match_all ("/<div style=\"text-align: center; line-height: 25pt; margin: 0.5pt 0cm; font-family: 黑体; font-size: 18pt\">([^x00-xff]+)<\/div>/", $t, $m);//[u4e00-u9fa5] 
			//return $m;
			//案号
			preg_match_all ("/<div style=\"text-align: right; line-height: 25pt; margin: 0.5pt 0cm; font-family: 宋体; font-size: 15pt\">([\s\S]+)<\/div>/", $t, $s);
			if(!empty($s[1][0])){
				$st = explode('</div>', $s[1][0]);
				$s[1][0] = $st[0];
			}
			//return $s;
			//时间
			preg_match_all ("/<div style=\"text-align: right; line-height: 25pt; margin: 0.5pt 36pt 0.5pt 0cm; font-family: 宋体; font-size: 15pt\">([^x00-xff]+)<\/div>/", $t, $d);
			//return $d;
		}
		
		if(empty($m[1][0])&&empty($s[1][0])&&empty($d[1][(count($d[1])-2)])){
			//法院
			preg_match_all ("/<div style=\"font-size: 18pt; font-family: 黑体; text-align: center; margin: 0.5pt 0cm; line-height: 25pt\">([^x00-xff]+)<\/div>/", $t, $m);//[u4e00-u9fa5]
			//return $m;
			//案号
			preg_match_all ("/<div style=\"font-size: 15pt; font-family: 宋体; text-align: right; margin: 0.5pt 0cm; line-height: 25pt\">([\s\S]+)<\/div>/", $t, $s);
			if(!empty($s[1][0])){
				$st = explode('</div>', $s[1][0]);
				$s[1][0] = $st[0];
			}
			//return $s;
			//时间
			preg_match_all ("/<div style=\"font-size: 15pt; font-family: 宋体; text-align: right; margin: 0.5pt 36pt 0.5pt 0cm; line-height: 25pt\">([^x00-xff]+)<\/div>/", $t, $d);
			//return $d;
		}
		
		if(empty($m[1][0])&&empty($s[1][0])&&empty($d[1][(count($d[1])-2)])){
			return false;
		}
		$p=array();
		preg_match_all ('/委托代理人([^x00-xff]+)。/', $t, $wtlawer);
		if(!empty($wtlawer[1][0])){
			$p['lawer'] = $wtlawer[1][0];
		}
		if(!empty($wtlawer[1][1])){
			$p['lawer'] .=' '.$wtlawer[1][1];
		}
		if(!empty($p['lawer'])){
			$p['lawer'] = join('', explode('：', $p['lawer']));
		}
		
		$p['court']=empty($m[1][0])?'':trim($m[1][0]);
		$p['serial']=empty($s[1][0])?'':trim($s[1][0]);
		$it = empty($d[1][(count($d[1])-2)])?'':$d[1][(count($d[1])-2)];

		if(!empty($it)){
			$p['conclude'] = $this->date_shift($it);
		}
		$court_id = '';
		if(!empty($p['court'])){
			//mb_strpos ( string $haystack , string $needle [, int $offset = 0 [, string $encoding = mb_internal_encoding() ]] )
			$crou_r = mb_strpos ($p['court'] , '法院'  );
			if($crou_r){
				$r = $this->login->court($p['court']);
				if(empty($r)){
					$court_id = $this->login->addcourt(array('name'=>$p['court']));
				}else{
					$court_id = $r[0]['id'];
				}
			}
			
		}
		$p['content'] = $t;
		$p['category'] = $c[0]['category'];
		$p['title'] = $c[0]['title'];
		$p['court']=$court_id;
		$p['cor']=$c[0]['id'];

		
		 $re = $this->login->addju($p);
		 unset($p);
		 unset($t);
		return $re;
	}
	function forin(){
		set_time_limit(0);
	
		for ($i=1;$i<=9857;$i++){
			$rs = $this->test($i);
			if($rs>0){
				echo '插入第'. $rs.'条'.'<br/>';
			}
		}
	
	}
	function date_shift($s){
		 $l = mb_strlen($s,'utf8');
		 $config = array(
		 		'〇'=>0,
		 		'一'=>1,
		 		'二'=>2,
		 		'三'=>3,
		 		'四'=>4,
		 		'五'=>5,
		 		'六'=>6,
		 		'七'=>7,
		 		'八'=>8,
		 		'九'=>9,
		 		'十'=>'10',
		 		'年'=>'-',
		 		'月'=>'-',
		 		'日'=>'',);
		 $r = '';
		 for ($i=0;$i<$l;$i++){
		 	$t = mb_substr($s, $i,1,'utf8');
		 	$tq = mb_substr($s, $i-1,1,'utf8');
		 	$th = mb_substr($s, $i+1,1,'utf8');
		 	
		 	if($tq=='年'&&$t=='十'&& $th=='月'){
		 		$t='十';
		 	}else if($tq=='年'&&$t=='十'&& $th!='月'){
		 		$t='一';
		 	}else if($tq!='月'&&$t=='十'&& $th!='日'){
		 		$t='日';
		 	}else if($tq=='月'&&$t=='十'&& $th!='日'){
		 		$t='一';
		 	}else if($tq=='月'&&$t=='十'&& $th=='日'){
		 		$t='十';
		 	}  
		  @$r.=$config[$t];
		 }
		 return date('Y-m-d',strtotime($r));
	}
	
	function data_transfer() {
		$this->load->view ( 'default/data_transfer' );
	}
	
	
	
	
	/**
	 * 地方法律法规
	 */
	function place($cid=1){
		//45398
		$p=array();
		$p['createid']=$cid;
		$r = $this->login->exc($cid);
		
		if (empty($r)){
			return false;
		}
		$r[0]['name'] = str_replace("&nbsp;","",$r[0]['name']);
		$r[0]['name'] = str_replace("<span class=STitle>","",$r[0]['name']);
		$c = explode('<BR>', $r[0]['name']);
		$n = count($c);
		if($n>3){
			preg_match_all ("/<strong>([^x00-xff]+)<\/strong>/", $r[0]['content'], $sa);
			
			if(empty($sa[1][0])){
				preg_match_all ("/<font class='MTitle'>([^x00-xff]+)<br><br \/>/", $r[0]['content'], $sa);
				if(empty($sa[1][0])){
					return false;
				}
			}
			$p['name'] = $sa[1][0];
			$p['effect_level'] = 8;
			$p['promulgation'] = 9;
	
			$p['content'] = str_replace("<p>&nbsp;</p>","",$r[0]['content']);
			$p['content'] = str_replace("<p style=\"text-align: right;\">&nbsp;</p>","",$p['content']);
			$p['content'] = str_replace("<br />\n<br />","<br />",$p['content'] );
			$p['promulgation_date'] = str_replace( "【发布日期】","",$c [2]) ;
			$p['effective_date'] =str_replace( "【生效日期】","",$c [3]);
			$s_d =str_replace( "【失效日期】","",$c [4]);
			if(empty($s_d)){
				$p['time_effect']=1;
			}else{
				$d =date('Y-m-d',time());
				if($d>$s_d){
					$p['time_effect']=2;
					
				}
			}

			$this->login->addexc($p);
		}
		return false;
	}
	
	function ssdd($cid=1){
		$this->load->model ( "contract_model",'m_c' );
		$c = $this->m_c->get_type();
		$p=array();
		$r = $this->login->exc($cid);
		if(!empty($r[0]['name'])){
			$r[0]['search'] = $r[0]['name'].','.$c[$r[0]['tid']]['name'];
		    $r[0]['content'] ='<h2 style="text-align: center;">'.$r[0]['name'].'</h2>'.$r[0]['content'] ;
			unset($r[0]['id']);
			$this->login->addexc($r[0]);
			return ;
		}

		
	}
	function filtert(){
		set_time_limit(0);
		$arr = $this->login->getJcopy();
		foreach($arr as $k=>$v){
			$this->login->updateTable('df_judgement_copy',$v['id'],array('serial'=>trim($v['serial'])));
		}
		echo 22;
	}
}
