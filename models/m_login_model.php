<?php
class m_login_model extends CI_Model{

	/**
	 * 后台登陆
	 */

	function __construct(){
		parent::__construct();
		$this->load->database();
	}
     
	/**
	 * 获取登陆账号
	 */
	function getUser($n,$p=''){
	    $sql="select * from acl_user where name='{$n}' and pwd='{$p}'  limit 0,1";
		return $this->db->query($sql)->result_array();
	}
	
	/**
	 * 登陆次数
	 */
	function loginnum($id){
		$d = date('Y-m-d h:i:s');
		$sql="update acl_user set `lasttime`='{$d}',`loginnum`=`loginnum`+1 where id='{$id}'";
		return $this->db->query($sql);
	}
	
	/**
	 * 登陆日志
	 */
	function loginLog($p){
		return $this->db->insert('acl_loginlog',$p);
	}
	
	/**
	 * 获得角色相关信息
	 */
	function getRole($id){
		 $s = 'select id from acl_roles where id = "'.$id.'" and status = 1 limit 0,1';
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 判断菜单
	 */
	function judgeMenu($p){
		 $s ="select id from acl_menus where c='{$p['class']}' and a='{$p['method']}' and dispaly =1 limit 0,1 ";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 判断对该请求是否有相关权限
	 */
	function judgeAu($rid,$mid){
		$s = "select id from acl_authority where role_id='{$rid}' and menus_id = '{$mid}' limit 0,1";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 获得左侧菜单
	 */
	function getLeftMenu($p,$rId){
		 $s = "select a.* from acl_menus a 
		 left join acl_authority b on a.id = b.menus_id
		 where a.parent_id in (select id from acl_menus where c='{$p['class']}' and a='{$p['method']}') and a.dispaly=1 and b.role_id ={$rId}
		  ORDER BY a.list_order ASC";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 获得菜单导航
	 */
	function getmenu($id){
		$s = "select * from acl_menus where parent_id =0 and id in (select menus_id from acl_authority where role_id='{$id}') ORDER BY list_order ASC";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 获得角色列表
	 */
	function roleLIst($w=''){
		$w = empty($w)?'':'where status = 1 ';
		$s = "select * from acl_roles {$w} ORDER BY id ASC";
		$r = $this->db->query($s)->result_array();
		$d = array();
		if (!empty($r)){
			foreach ($r as $k=>$v){
				$d[$v['id']] = $v;
			}
		}
		return $d;
	}
	
	/**
	 * 获得用户列表
	 */
	function userLIst(){
		$s = "select * from acl_user ORDER BY id ASC";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 获得所有目录列表
	 */
	function getMenuList(){
		$s = "select * from acl_menus  ORDER BY list_order ASC";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 获得操作
	 */
	function getOpt($p,$rId){
			return $s = "select a.* from acl_menus a
			left join acl_authority b on a.id = b.menus_id
			where a.parent_id in (select id from acl_menus where c='{$p['class']}' and a='{$p['method']}') and a.dispaly=1 and a.is_menus=2 and b.role_id ={$rId}
			ORDER BY a.list_order ASC";
			return $this->db->query($s)->result_array();
	}
	
	/**
	 * judge登陆账号
	 */
	function judgeUser($n){
		$sql="select * from acl_user where name='{$n}' limit 0,1";
		return $this->db->query($sql)->result_array();
	}
	
	/**
	 * 添加账号信息
	 */
	function adduser($p){
		return $this->db->insert('acl_user',$p);
	}
	
	/**
	 * 修改账号信息
	 */
	function edituser($p){
		return $this->db->update('acl_user',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 添加角色信息
	 */
	function addrole($p){
		return $this->db->insert('acl_roles',$p);
	}
	
	/**
	 * 修改角色信息
	 */
	function editrole($p){
		return $this->db->update('acl_roles',$p,array('id'=>$p['id']));
	}

	/**
	 * 添加目录信息
	 */
	function addmenu($p){

		return $this->db->insert('acl_menus',$p);
	}
	
	/**
	 * 修改目录信息
	 */
	function editmenu($p){
		return $this->db->update('acl_menus',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 获取目录
	 */
	function getMenuAll(){
		$s ="select * from acl_menus where is_menus=1 and dispaly =1 ";
		$r = $this->db->query($s)->result_array();
		$d = array();
		if(!empty($r)){
			foreach ($r as $k=>$v){
				$d[$v['id']]=$v;
			}
		}
		return $d;
	}
	
	/**
	 * 获得用户详细信息
	 */
	function userInfo($id){
		$s = "select * from acl_user where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 获得角色信息
	 */
	function roleInfo($id){
		$s = "select * from acl_roles where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 获得目录详细信息
	 */
	function menuInfo($id){
		$s = "select * from acl_menus where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 获得角色信息
	 */
	function getaut(){
		$s = "select * from acl_menus where  dispaly =1";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 通过角色获得角色的相关权限
	 */
	function byRoleAut($id){
		 $s = "select menus_id from acl_authority where  role_id ='{$id}'";
		$r = $this->db->query($s)->result_array();
		$d = array();
		if(!empty($r)){
			foreach ($r as $k=>$v){
				$d[]=$v['menus_id'];
			}
		}
		return $d;
	}
	
	/**
	 * 权限
	 */
	function authority($p='',$id){
		$this->db->delete('acl_authority', array('role_id' => $id)); 
		if(!empty($p)){
			return $this->db->insert_batch('acl_authority',$p);
		}else{
			return true;
		}
	}
	
	/**
	 * 通过id获取用户详细信息
	 */
	function byIdInfo($id){
		$s = "select * from acl_user where id='{$id}'  limit 0,1";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 通过id修改密码
	 */
	function byIdpwd($p){
		return $this->db->update('acl_user',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 获得后台用户
	 */
	function getUsers(){
		$s = "select id,realname from acl_user ";
		$r = $this->db->query($s)->result_array();
		$d = array();
		if(!empty($r)){
			foreach ($r as $k=>$v){
				$d[$v['id']]=$v;
			}
		}
		return $d;
	}
	
	
}