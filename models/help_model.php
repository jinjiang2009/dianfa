<?php
class help_model extends CI_Model{

	/**
	 * 
	 */
	function __construct(){
		parent::__construct();
		
	}	
	
	function getHelpType(){
		$sql = ' select * from df_helplaw where is_show=1 ' ;
		$result = $this->db->query($sql);
		$arr = $result->fetchAll('pid|array|id');
		//var_dump($arr);exit;
		return $arr;
	}
	function getOptions($pid){
		$sql = ' select * from df_helplaw where is_show=1 and pid='.$pid;
		$rs = $this->db->query($sql)->result_array();
		return $rs;
	}
	public function insertTable($table,$arr){
		 $this->db->insert($table,$arr);
		 return $this->db->insert_id();
	}
	/*
	 * 后台获取法援下载数据
	 */
	function getLawHelp($id){
		$sql = ' select upload from df_help_detail where id= '.$id;
		$rs = $this->db->query($sql)->row_array();
		return $rs;
	}
	
	
	
	function getCaseshow($wh=null,$page=null,$perpage=null){
		//$con = ' where 1 ';
		$con = '';
		if($wh){
			if(isset($wh['type3'])&&!empty($wh['type3'])){
 				$con .= ' and l.type3= "'.$wh['type3'].'"';
 			}
			if(isset($wh['content']) && !empty($wh['content'])){
 				$con .= ' and m.content like "%'.$wh['content'].'%"';
 			}
		}
		//var_dump($wh['type3']);exit;
		$con .= ' and name !="'.'"';
		$con .= ' order by m.createtime desc ';
		if($page){
			$nu = ($page-1)*$perpage;
			$con.=' limit '.$nu.','.$perpage;
		}
		
		$sql = ' select  m.id,m.name,m.content,m.createtime,m.is_help,l.* from df_help_detail as m,df_help_type as l where l.id=m.type  ' .$con;
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		//var_dump($arr);exit;
		return $arr;
	}
	
	function getCase(){
		$sql = 'select id,name from df_helplaw  ';
		$result = $this->db->query($sql);
		$arr = $result->fetchAll('id');
		return $arr;
	}
	
	function getShow(){
		$sql = ' select  count(l.id) as total from df_help_detail l left join df_help_type m on l.type=m.id  ';
		
		$result = $this->db->query($sql);
		$arr = $result->row_array();
		return $arr;
	}
	
	
} 