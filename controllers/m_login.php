<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 *
 * @author chenxiao
 * @date 2014/08/20
 * @desc 后台登陆
 */
class m_login extends m_base {
	private $jdata = array();
	function __construct() {
		parent::__construct ();
		$this->jdata["success"] = 0;
		$this->jdata["message"] = "";
		$this->load->model ( "m_login_model",'m_l' );
		$this->load->model ( "m_config_model",'m_c' );
	}
	
	/**
	 * 登陆验证
	 */
	public function index(){
		$this->load->view('manage/loginshow');
	}
	
	/**
	 * 登陆验证
	 */
	public function login(){
		session_start();
		$checkcode = $_SESSION['helloweba_char'];
		if($checkcode != $this->input->post('vcode',TRUE)){
			$this->jdata["success"] = 3;
			$this->json_out($this->jdata);
		}
		$result = $this->m_l->getUser( $this->input->post('uname',TRUE), md5($this->input->post('pass',TRUE)));
		if(empty($result)){
			$this->jdata["success"] = 2;
			$this->json_out($this->jdata);
		}
		if(empty($result [0] ['role_id'])){
			$this->jdata["success"] = 4;
			$this->json_out($this->jdata);
		}
		if($result [0] ['status']==2){
			$this->jdata["success"] = 5;
			$this->json_out($this->jdata);
		}
		if($result[0]['id']){
			//登陆次数
			$this->m_l->loginnum($result[0]['id']);
			//登陆日志
			$this->m_l->loginLog(array('user_id'=>$result[0]['id']));
			
			$user_info = array (
					$result [0] ['id'],
					$result [0] ['realname'],
					$result [0] ['role_id']
			);
			setcookie ( "DF_USER", urlencode ( implode ( '|', $user_info ) ), 0, '/', H_COOKIE_DOMAIN );
			
			$this->jdata["success"] = 1;
		}
		$this->json_out($this->jdata);
	}
	
	/**
	 * 退出登录
	 */
	public function loginout(){
		setcookie ( 'DF_USER', '', 0, '/', H_COOKIE_DOMAIN );
		common_location( base_url().'m_login');
		exit;
	} 
	
	
	
}