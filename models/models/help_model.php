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
	
} 