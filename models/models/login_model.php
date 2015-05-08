<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 class login_model extends CI_Model{
 	private $_df='';
 	function __construct(){
 		header("Content-type:text/html;charset=utf-8");
 		parent::__construct();
 		// $this->_df= $this->load->database('df',true);
 			
 		
 	}
 	/*
 	 * 验证登录
 	 */
 	public function verifyLogin($uname,$pass){
 		$pass=md5($pass);
		$sql=' SELECT * FROM `df_member` WHERE	( `mobile`="'.$uname.'" or `email`="'.$uname.'" or `username`="'.$uname.'" )  AND  `pwd`="'.$pass.'"' ;
		$result = $this->db->query($sql);
		$arr = $result->row_array();
		return $arr;
 	}
 	/*
 	 * 验证登录
 	*/
 	public function verifyLaw($con){
 		$sql = ' select id from df_lawyer where '.$con;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	
 	public function verifyLfrim($con){
 		$sql = 'select id from df_lfrim where '.$con;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 验证注册信息,是否重复
 	 */
 	public function verifyRegCon($con){
 		$sql = ' select id from df_member where '.$con;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 注册信息入库
 	 */
 	public function insertTable($table,$arr){
 		return $this->db->insert($table,$arr);
 	}
 	
 	/**
 	 * 通过获得用户详情userky
 	 */
 	function getqqlogin($userky){
 		return $this->db->get_where('df_member',array('userkey'=>$userky),1,0)->result_array();
 	}
 	/*
 	 * 验证用户名和邮箱
 	 */
 	function verifyFind($post){
 		$sql = ' select id from df_member where username="'.$post['username'].'" and email="'.$post['email'].'"' ;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 验证用户是否存在
 	 */
 	function verifyUsername($name){
 		$sql = ' select id from df_member where username="'.$name.'" ' ;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 		
 	}
 	/*
 	 * 更新密码
 	*/
 	function updatePwd($name,$pwd){
 		$sql = ' update df_member set pwd="'.md5($pwd).'" where username="'.$name.'"' ;
 		$result = $this->db->query($sql);
 		return $result;
 	
 	}
 	
 	/**
 	 * 计算未读消息数
 	 */
 	function m_num($ucode){
 		$s = " select count(id) total from df_message where is_read =1 and  ucode = '{$ucode}'";
 		$result = $this->db->query($s)->result_array();
 		return $result;
 	}
 	/*
 	 * 验证用户名,邮箱,手机号
 	 */
 	function verifyJs($arr){
 		$sql = ' select id from df_member where username="'.$arr['username'].'" or mobile="'.$arr['mobile'].'" or email= "'.$arr['email'].'"';
 		$result = $this->db->query($sql)->result_array();
 		return $result;
 	}
	
	/**
 	 *  查询
 	 */
 	function exc($id){
 		return $this->db->get_where('test',array('id'=>$id),1,0)->result_array();
 	}
 	
 	function addexc($p){
 		//return 	$this->_df->insert('df_law',$p);
 		return 	$this->_df->insert('df_contract',$p);
 		//return 	$this->db->insert('df_contract',$p);
 	}
 	
 	/**
 	 * 法院
 	 */
 	function court($name){
 		$sql = ' select id from df_court where name like "%'.$name.'%"  limit 0,1' ;
 		$result = $this->db->query($sql)->result_array();
 		return $result;
//      return $this->db->get_where('df_court',array('name'=>$name),1,0)->result_array();
 	}
 	
 	function addcourt($p){
 		$this->db->insert('df_court',$p);
 		return $this->db->insert_id();
 	}
 	
 	function addju($p){
 		$this->db->insert('df_judgement_copy2',$p);
 		return $this->db->insert_id();
 	}
 	public function updateTable($table,$id,$type_arr){
 		$this->db->where('id', $id);
 		$this->db->update($table, $type_arr);
 	}
 	public function getJcopy(){
 		$sql = ' select id,serial from df_judgement_copy where id>10000 and id<=306083 ' ;
 		$result = $this->db->query($sql)->result_array();
 		return $result;
 	}
 }
