<?php
class m_web_model extends CI_Model{

	/**
	 * 数据处理
	 */
	function __construct(){
		
		parent::__construct();
		$this->load->database();
	}
		
	/**
	 * 推荐列表
	 */
	function recommend($p){
		$s = 'select * from acl_recommend where 1=1'.$p['type'].$p['name'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(*) total  from acl_recommend where 1=1 '.$p['type'].$p['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 添加推荐
	 */
	function recommendadd($p){
		return $this->db->insert('acl_recommend',$p);
	}
	
	
	/**
	 * 推荐详情
	 */
	function recommendinfo($id){
		$s = "select * from acl_recommend where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	
	
	function messageinfo($id){
		$s = "select * from df_mess where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	/**
	 * 修改推荐信息
	 */
	function recommendedit($p){
		return $this->db->update('acl_recommend',$p,array('id'=>$p['id']));
	}
	
	
	function messageedit($p){
		return $this->db->update('df_mess',$p,array('id'=>$p['id']));
	}
	/**
	 * 通过code查询详情
	 */
	function bycodeinfo($id){
		$s = "select * from df_lawyer where ucode='{$id}' limit 0,1";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 律师
	 */
	function lawyer($u=''){
		$w =empty($u)?' where status in(1,2,3,4)  ': ' where ucode in('.$u.') ';
	    $s = "select * from df_lawyer ".$w;
		$r =  $this->db->query($s)->result_array();
		$d = array();
		if(!empty($r)){
			foreach ($r as $k=>$v){
				if(!empty($v['ucode']))
				$d[$v['ucode']]=$v;
			}
		}
		return $d;
	}
	
	/**
	 * 推荐列表
	 */
	function video($p){
		$s = 'select * from df_video where 1=1'.$p['type'].$p['name'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(*) total  from df_video where 1=1 '.$p['type'].$p['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 添加推荐
	 */
	function videoadd($p){
		return $this->db->insert('df_video',$p);
	}
	
	/**
	 * 推荐详情
	 */
	function videoinfo($id){
		$s = "select * from df_video where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 修改推荐信息
	 */
	function videoedit($p){
		return $this->db->update('df_video',$p,array('id'=>$p['id']));
	}
} 