<?php
class local_model extends CI_Model{
	function __construct(){
		parent::__construct();
	
	}
	
	function con_info($id){
		$s = "select id,name,content from df_local where is_show =1 and is_audit =1 and id='{$id}' ";
		return $this->db->query($s)->result_array();
	}
	
	function down_num($id){
		$s = "update df_local set `down_num`=`down_num`+1 where id='{$id}'";;
		return $this->db->query($s);
	}
	
	function contractlist($p){
		$s = 'select id,name,createtime from df_local where 1=1 '.$p['name'].' and is_show=1 and is_audit=1  order by id desc limit 5';
		//$t = 'select count(id) total  from df_contract where 1=1 '.$p ['type'].$p['tid'].$p['name'].' and is_show=1 and is_audit=1 ';
		$r['list'] = $this->db->query($s)->result_array();
		//$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	function getInfo($p){
		return $this->db->get_where('df_local',array('id'=>$p['id']),1,0)->result_array();
	}
}