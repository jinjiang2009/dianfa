<?php
/**
 * 法律法规
 * @author cx
 *
 */
class order_model extends CI_Model{

	function __construct(){
 		header("Content-type:text/html;charset=utf-8");
 		parent::__construct();
 		
 	}	
	function verorder($id){
		$s =  'select cor_id,money,order_code,moneytype from df_order where id= '.$id;
		$_r = $this->db->query($s)->row_array();
		return $_r;
	}
	public function updateTable($table,$id,$type_arr){
 		$this->db->where('id', $id);
 		$this->db->update($table, $type_arr);
 	}
 	public function verorderCode($ordercode){
 		$sql = ' select id,money,status,cor_id from df_order where order_code = "'.$ordercode.'"';
 		$_r = $this->db->query($sql)->row_array();
 		return $_r;
 	}
 	public function getMoney($id){
 		$sql = ' select unit_price from df_privatelaw where id= '.$id;
 		$_r = $this->db->query($sql)->row_array();
 		return $_r;
 	}
 	public function insertTable($table,$arr){
 		$this->db->insert($table,$arr);
 		return $this->db->insert_id();
 	}
 	public function getSubject($corid){
 		$sql = ' select name from df_privatelaw where id='.$corid;
 		$_r = $this->db->query($sql)->row_array();
 		return $_r;
 	}

} 