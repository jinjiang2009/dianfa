<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 class header_model extends CI_Model{
 	function __construct(){
 		header("Content-type:text/html;charset=utf-8");
 		parent::__construct();
 		
 	}
 	/*
 	 * 获得擅长领域
 	*/
 	public function getTerritory(){
 		$sql = ' select * from df_territory where pid=0 and is_show =1  ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得二级擅长领域
 	*/
 	public function getSecondTerritory(){
 		$sql = ' select * from df_territory where  is_show =1 and pid<>0 order by id asc';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('pid|array|id');
 		return $arr;
 	}
 	/*
 	 * 获得消息
 	*/
 	public function getMessageNum($ucode){
 		$sql = ' select count(id) as total from df_message where is_read=1 and  ucode = "'.$ucode.'"';
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr['total'];
 	}
 }
