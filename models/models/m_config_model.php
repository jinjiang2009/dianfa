<?php
class m_config_model extends CI_Model{

	/**
	 * 数据处理
	 */
	function __construct(){
		
		parent::__construct();
		$this->load->database();
	}	
	
	/**
	 * 案由
	 */
	function category($c = false){
		$c = empty($c)?'where pid=0  ':'where pid>0';
		$s="select * from df_category ".$c;
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
	 * 案由
	 */
	function categoryshow(){
		$s="select * from df_category where is_show = 1";
		$r = $this->db->query($s)->result_array();
		$d = array();
		if(!empty($r)){
			foreach ($r as $k=>$v){
				$d[$v['id']]=$v;
			}
		}
		return $d;
	}
	
	
	function messageshow(){
		$s="select * from df_mess where is_show = 1";
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
	 *  案件分类列表
	 */
	function court($p){
		$s = 'select * from df_court where 1=1'.$p['name'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(*) total  from df_court where 1=1 '.$p['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 添加 案件分类
	 */
	function courtadd($p){
		return $this->db->insert('df_court',$p);
	}
	
	/**
	 *  案件分类详情
	 */
	function courtinfo($id){
		$s = "select * from df_court where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 修改 案件分类
	 */
	function courtedit($p){
		return $this->db->update('df_court',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 地区范围查找
	 */
	function region($id=''){
		if (empty($id)){
			$s = "select * from df_region where type =1";
		}else{
			$s = "select * from df_region where pid = '{$id}'";
		}
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 通过uuid查找地区
	 */
	function byuuidName($id){
		$s = "select * from df_region where uuid = '{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 获得全部城市
	 */
	function getAllCity(){
		$s = "select * from df_region";
		$r = $this->db->query($s)->result_array();
				$d = array();
		if(!empty($r)){
			foreach ($r as $k=>$v){
				$d[$v['uuid']]=$v;
			}
		}
		return $d;
	}
	
	/**
	 * 
	 */
	function byidcategory(){
		$s = "select * from df_category";
		$r = $this->db->query($s)->result_array();
		$d = array();
		if(!empty($r)){
			foreach ($r as $k=>$v){
				$d[$v['id']]=$v;
			}
		}
		return $d;
	}
	
	function byidcourt($id=''){
		$s = "select * from df_court where id = {$id}";
		$r = $this->db->query($s)->result_array();
		return $r;
	}
	
	/**
	 * 关联查案由
	 */
	function categorys($id=''){
		$id = empty($id)?0:$id;
		$s = "select * from df_category where pid = '{$id}' and is_show =1";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 搜所查询法院
	 */
	function search_court($court){
		$s = "select * from df_court where name like '%{$court}%'";
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
	 * 搜索律师
	 */
	function search_lawyer($court){
		$s = "select * from df_lawyer where name like '%{$court}%'  limit 0,1000";
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