<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 class app_model extends CI_Model{
 	function __construct(){
 		
 		parent::__construct();
 		
 	}
 	public function getGroup($type=0){
 		$this->db->cache_on();
 		$sql = ' select * from df_group  where pid= '.$type ;
     	$result = $this->db->query($sql);
          $arr = $result->result_array();
 		return $arr;
 	}
 	/**
	 * 获得分类
	 */
	function get_type(){
		$s = "select id,type,name from df_type where is_show =1 order by sort asc";
		return $this->db->query($s)->fetchAll('id');
		
	}
	
	/**
	 * 合同法律文书文类
	 */
	function contractlist($p){
		$s = 'select id,tid,name,type,createtime from df_contract where 1=1 '.$p ['type'].$p['tid'].$p['name'].' and is_show=1 and is_audit=1  order by id desc limit '.$p['start'].','.$p['limit'];
		//$t = 'select count(id) total  from df_contract where 1=1 '.$p ['type'].$p['tid'].$p['name'].' and is_show=1 and is_audit=1 ';
		$r = $this->db->query($s)->result_array();
		//$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
 	function getLawById($id){
 		$sql = ' select name,promulgation,effect_level,time_effect,promulgation_date,effective_date,content from df_law where is_show=1 and id='. $id;
 		$r = $this->db->query($sql)->row_array();
 		return $r;
 	}
 	function getLawList($p){
		$where = $p['name'].$p['catid'].$p['promulgationid'].$p['effect_levelid'];
		$s = "select id,name,promulgation,effect_level from df_law where 1=1 ".$where.'  order by promulgation_date desc limit '.$p['start'].','.$p['limit'];
		$re = $this->db->query($s)->result_array();
		return $re;
	}
	/*function productlist($p){
		$sql = 'select * from df_privatelaw where 1=1 '.$p ['type'].' and is_show=1 order by sort asc ';
		$re= $this->db->query($sql)->result_array();
		return $re;
	}*/
	
	function privatelawlist($p){
		$where = $p ['type'].$p['name'];
		$s = 'select id,img,name,type,infotype,content,unit_price from df_privatelaw where 1=1 '.$where.' and is_show=1 order by createtime desc limit '.$p['start'].','.$p['limit'];
		$r = $this->db->query($s)->result_array();
		return $r;
	}
 }
