<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 *
 * @author chenxiao
 * @date 2014/08/20
 * @desc 后台管理
 */
class m_home extends m_base {
	private $jdata = array();
	function __construct() {
		parent::__construct ();
		$this->judgeUser();//用户登陆
		$this->judgeAuthority();
		$this->jdata["success"] = 0;
		$this->jdata["message"] = "";
		$this->load->model ( "m_login_model",'m_l' );
	}
	

	
	/**
	 * 主页
	 */
	function homa_page(){
		$data = array();
		$data['left'] = $this->getLeftMenu();
		$this->load->view('manage/left',$data);
		
	}
	
	/**
	 * 角色管理
	 */
	function role(){
		$data['list'] = $this->m_l->roleLIst();
		$data['opt'] = $this->getOptAut();
		$this->load->view('manage/role',$data);
	}
	
	/**
	 * 用户管理
	 */
	function user(){
		$data['list'] = $this->m_l->userLIst();
		$data['opt'] = $this->getOptAut();
		$data['role'] = $this->m_l->roleLIst();
		$this->load->view('manage/user',$data);
	}
	
	/**
	 * 目录管理
	 */
	function menu(){
		$data['list'] = $this->m_l->getMenuList();
		$data['opt'] = $this->getOptAut();
		$this->load->view('manage/menu',$data);
	}
	
	/**
	 * 添加管理账户
	 */
	function adduser(){
		if(!$_POST){
			$data['role'] = $this->m_l->roleLIst(true);
			$this->load->view('manage/adduser',$data);
		}else{
			$p = array();
			$p = $_POST;
			$p['pwd'] = md5($p['pwd']);
			$_r = $this->m_l->judgeUser($p['name']);
			if(!empty($_r)){//该账号已存在
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_l->adduser($p);
			if(!empty($_r2)){//添加OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 编辑用户
	 */
	function edituser(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['info'] = $this->m_l->userInfo($id);
			$data['role'] = $this->m_l->roleLIst(true);
			$this->load->view('manage/adduser',$data);
		}else{
			$p = array();
			$p = $_POST;
			if(empty($p['id'])){
				$this->jdata["success"] = 3;
				$this->json_out($this->jdata);
			}
			if(empty($p['pwd'])){
				unset($p['pwd']);
			}else{
				$p['pwd'] =md5($p['pwd']);
			}
			$_r = $this->m_l->judgeUser($p['name']);
			if($_r[0]['id'] != $p['id'] && !empty($_r)){//该账号已存在
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_l->edituser($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 启用或禁用账号用户啊
	 */
	function statususer(){
		$id = intval($this->input->get('id'));
		if($id){
			$p = array();
			$p['status'] = $this->input->get('status')==1?2:1;
			$p['id'] = $id;
			$_r2 = $this->m_l->edituser($p);
			$this->jdata["success"] = 1;
		}else{
			$this->jdata["success"] = 2;
		}
		$this->json_out($this->jdata);
	}
	
	
	
	/**
	 * 添加角色
	 */
	function addrole(){
		if(!$_POST){
			$this->load->view('manage/addrole');
		}else{
			$p = array();
			$p = $_POST;
			$_r2 = $this->m_l->addrole($p);
			if(!empty($_r2)){//添加OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}	
		}
	}
	

	/**
	 * 编辑角色
	 */
	function editrole(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['info'] = $this->m_l->roleInfo($id);
			$this->load->view('manage/addrole',$data);
		}else{
			$p = array();
			$p = $_POST;
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_l->editrole($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	
	/**
	 * 启用或禁用角色
	 */
	function statusrole(){
		$id = intval($this->input->get('id'));
		if($id){
			$p = array();
			$p['status'] = $this->input->get('status')==1?2:1;
			$p['id'] = $id;
			$_r2 = $this->m_l->editrole($p);
			$this->jdata["success"] = 1;
		}else{
			$this->jdata["success"] = 2;
		}
		$this->json_out($this->jdata);
	}

	
	/**
	 * 添加目录
	 */
	function addmenu(){
		if(!$_POST){
			$data['mlist'] = $this->m_l->getMenuAll();
			$this->load->view('manage/addmenu',$data);
		}else{
			$p = array();
			$p = $_POST;

			$_r2 = $this->m_l->addmenu($p);
			if(!empty($_r2)){//添加OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 编辑目录
	 */
	function editmenu(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['info'] = $this->m_l->menuInfo($id);
			$data['mlist'] = $this->m_l->getMenuAll();
			$this->load->view('manage/addmenu',$data);
		}else{
			
			$p = array();
			$p = $_POST;
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_l->editmenu($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 启用或禁用角色
	 */
	function dispalymenu(){
		$id = intval($this->input->get('id'));
		if($id){
			$p = array();
			$p['dispaly'] = $this->input->get('dispaly')==1?2:1;
			$p['id'] = $id;
			$_r2 = $this->m_l->editmenu($p);
			$this->jdata["success"] = 1;
		}else{
			$this->jdata["success"] = 2;
		}
		$this->json_out($this->jdata);
	}
	
	/**
	 * 权限管理
	 */
	function authority(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['list'] = $this->m_l->getaut();
			$data['aut'] = $this->m_l->byRoleAut($id);
			$data['info'] = $id;
			$this->load->view('manage/sysPerm',$data);
		}else{
			$p=array();
			$da=array();
			$p['menus_id'] = explode(',', $_POST['ids']);
			$p['id'] =intval($_POST['id']);
			if(empty($p['menus_id'])||empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			if(!empty($p['menus_id']))
			foreach ($p['menus_id'] as $k=>$v){
				$da[$k]['menus_id']=$v;
				$da[$k]['role_id']=$p['id'];
			}
			$_r = $this->m_l->authority($da,$p['id']); 
			if (!empty($_r)){
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
}