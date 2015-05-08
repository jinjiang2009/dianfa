<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class register extends base{

	
  function __construct(){
    parent::__construct();
    $this->load->model ( "login_model",'login' );
    
  }
  /**
   * 注册页
   */
  public function index(){
  	$data = $this->data;
  	if($data['login_type']!=4){
  		header_output('您已登陆,请退出后再注册！');
  	}
  	session_start();
  	$_SESSION['seral'] = $data['userverify'] = $this->create_guid();
  	$get = $this->input->get();
  	
  	if(isset($get['type'])){
  		$data['type'] = $get['type'];
  	}else{
  		$data['type'] = 1;
  	}
  	$this->load->model ( "judgement_model",'judge' );
  	$data['region'] = $this->judge->getRegion();
  	unset($data['region'][31]);
  	unset($data['region'][32]);
  	unset($data['region'][33]);
  	if($get['type']==3){
  		$this->load->view('default/register2.php',$data);
  	}else{
  		$this->load->view('default/register.php',$data);
  	}
  }
 /*
  * 异步验证:验证码
  */
  public function verifyCode(){
  	session_start();
  	$post = $this->input->post();
  	if($_SESSION["helloweba_char"]==$post['vcode']){
	  $msg = 1111;	
  	}else{
  		$msg = 3;
  	}
  	echo $msg;
  }
  /*
   * 异步验证:email,mobile,username
  */
  public function verify(){
  	$get = $this->input->get();
  	if(!empty($get)){
  		foreach($get as $k=>$v){
  			$con = $get['type'].'="'.$get[$get['type']].'"';
  		}
  		$arr = $this->login->verifyRegCon($con);
  		if(empty($arr)){
  			$msg = 9999;
  		}else{
  			$msg = 1111;
  		}
  	}else{
  		$msg = 1111;
  	}
  	
  	echo $msg;
  }
  /*
   * 异步验证:df_lawyer
  */
  public function verifylince(){
  	$get = $this->input->get();
  	if(!empty($get)){
  		foreach($get as $k=>$v){
  			$con = $get['type'].'="'.$get[$get['type']].'"';
  		}
  		$arr = $this->login->verifyLaw($con);
  		if(empty($arr)){
  			$msg = 9999;
  		}else{
  			$msg = 1111;
  		}
  	}else{
  		$msg = 1111;
  	}
  
  	echo $msg;
  }
  /*
   * 注册成功
   */
  public function success(){
  	$post = $this->input->post();
  	if(!empty($post)){
  		session_start();
  		if(	$_SESSION['seral']==$post['seral']){
  			$arr['identity'] = $post['judgelaw'];
  			$arr['username'] = $post['us'];
  			$arr['email'] = $post['email'];
  			$arr['mobile'] = $post['tel'];
  			$arr['pwd'] = md5($post['password2']);
  			$arr['ucode'] = $this->create_guid();
  			$arr['create_time'] = date('Y-m-d H:i:s');
  			$arr['sex'] = 0;
  			$arr['region'] = $post['region'];
  			$arr['integral'] = 30;
  			$rrver = $this->login->verifyJs($arr);
  			if(empty($rrver)){
	  			if($arr['identity']==2){
	  				$law['office'] = $post['office'];
	  				$law['ucode'] = $arr['ucode'];
	  				$law['license'] = $post['license'];
	  				$law['court'] = $post['court'];
	  				$this->login->insertTable('df_lawyer',$law);
	  			}
	  			$id = $this->login->insertTable('df_member',$arr);
	  			if($id){
	  				$data = $this->data;
	  				$_SESSION['seral'] = $this->create_guid();
	  				$this->session->set_userdata('user',$arr);
	  				$this->session->set_userdata('login_type',$arr['identity']);
	  				$data['login_type'] = $arr['identity'];
	  				
	  				$data['user'] = $arr;
	  				$this->login->insertTable('df_integral_log',array('ucode'=>$arr['ucode'],'num'=>30,'cause'=>26,'desc'=>'完成基本注册'));
	  				$this->login->insertTable('df_message',array('ucode'=>$arr['ucode'],'message'=>'恭喜您成为点法网会员'));
	  				$this->load->view('default/success.php',$data);
	
	  			}
  			}else{
  				header_output('对不起，您的浏览器没有打开JavaScript脚本支持！');
  			}
  		}else{
  			header_output('请不要重复提交！');
  		}
  	}else{
  		header_output('非法提交！');
  	}
  }
  public function test(){
  	$data = $this->data;
  	$this->load->view('default/success.php',$data);
  }
  /*
   * 律师注册协议
   */
  public function lawagreement(){
  	$data = $this->data;
  	$this->load->view('default/lawagreement.php',$data);
  }
  /*
   * 用户注册协议
  */
  public function agreement(){
  	$data = $this->data;
  	$this->load->view('default/agreement.php',$data);
  }
}

