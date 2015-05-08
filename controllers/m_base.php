<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 *
 * @author chenxiao
 * @date 2014/08/20
 * @desc 后台管理
 */
class m_base extends CI_Controller {
	function __construct() {
		parent::__construct (); 
	}
	
	/**
	 * 获取后台用户ID
	 */
	function getMLId($s=false) {
		if (isset ( $_COOKIE ['DF_USER'] )) {
			$tmp = explode ( '|', urldecode ( $_COOKIE ["DF_USER"] ) );
			if($s){
				return $tmp;
			}
			return $tmp [0];
		}
		return 0;
	}
	
	/**
	 * 判断用户是否登陆未登录跳转登陆页面
	 */
	function judgeUser($s=false){
		$id = $this->getMLId($s);
		if(empty($id)){
			common_location( base_url().'m_login');
			exit;
		}
		return $id;
		
	}
	
	/**
	 * 没权限跳转页面
	 */
	function exitLogin(){
		//setcookie ( 'DF_USER', '', 0, '/', H_COOKIE_DOMAIN );
	//common_location( base_url().'m_login');
	echo '没有相关权限';
		exit;
	}
	
	/**
	 * 判断权限
	 */
	function judgeAuthority(){
		$desc = $this->judgeUser(true);
		if($this->input->is_ajax_request()&&empty($desc[2])){//ajax 请求没有相关权限
			$_r = array('success' =>7);
			$this->json_out($_r);
		}
 		if(empty($desc[2])){//没角色属性注销登陆
			$this->exitLogin();
		} 
		
		$this->load->model ( "m_login_model",'m_l' );
		$r_desc = $this->m_l->getRole($desc[2]);//验证角色是否正常使用
		if($this->input->is_ajax_request()&&empty($r_desc)){//ajax 请求没有相关权限
			$_r = array('success' =>7);
			$this->json_out($_r);
		}
 		if(empty($r_desc)){
			$this->exitLogin();
		} 
		
		
		$menu = $this->m_l->judgeMenu($this->data);//验证是否有该菜单
		
		if($this->input->is_ajax_request()&&empty($menu)){//ajax 请求没有相关权限
			$_r = array('success' =>7);
			$this->json_out($_r);
		}
		if(empty($menu)){
			$this->exitLogin();
		}
		
		
		$_r = $this->m_l->judgeAu($r_desc[0]['id'],$menu[0]['id']);//验证角色对菜单是否有操作权限
		if($this->input->is_ajax_request()&&empty($_r)){//ajax 请求没有相关权限
			$_r = array('success' =>7);
			$this->json_out($_r);
		}
		if(empty($_r)){
			$this->exitLogin();
		}	
	}
	
	/**
	 * 获得左侧列表
	 */
	function getLeftMenu(){
		$this->load->model ( "m_login_model",'m_l' );
		$desc = $this->getMLId(true);
		return $this->m_l->getLeftMenu($this->data,$desc[2]);
	}
	
	/**
	 * 获得列表下的操作权限
	 */
	function getOptAut(){
		$this->load->model ( "m_login_model",'m_l' );
		$desc = $this->getMLId(true);
		return $this->m_l->getLeftMenu($this->data,$desc[2]);
	}
	
	/**
	 * 获得用户列表
	 */
	function getUsers(){
		$this->load->model ( "m_login_model",'m_l' );
		return $this->m_l->getUsers();
	}
	

}