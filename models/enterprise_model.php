<?php
/**
 * 法律法规
 * @author cx
 *
 */
class enterprise_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}	
	
	/**
	 * 产品数据
	 */
	function productlist($type){
		$sql = 'select * from df_privatelaw where is_show=1 '.$type.'   order by sort asc ';
		$re= $this->db->query($sql)->result_array();
		return $re;
	}
	/**
	 * 研究报告数据
	 */
	function getResearch($type){
		$sql = 'select * from df_privatelaw where is_show=1 and type=3  order by sort asc ';
		$re= $this->db->query($sql)->result_array();
		return $re;
	}
	
	/**
	 * 查询产品详情
	 * @param unknown_type $id
	 */
	function product($id){
		return $this->db->get_where('df_privatelaw', array('id' => $id,'is_show'=>1), 1, 0)->result_array();
	}
	
	/**
	 * 添加产品订单
	 */
	function add_order($p){
		$this->db->insert('df_order',$p);
		return $this->db->insert_id();
	}
	/*
	 * 获得某个产品的价格
	 */
	function getCor($id){
		$sql = ' select name,unit_price from df_privatelaw where id= '.$id;
		$re= $this->db->query($sql)->row_array();
		return $re;
	}
	/**
	 * 公共的产品
	 */
	function public_product($p){
		return $this->db->get_where('df_privatelaw', array('type' => $p,'infotype'=>'0','is_show'=>1), 10, 0)->result_array();
	}
	public function updateTable($table,$id,$type_arr){
		$this->db->where('id', $id);
		$this->db->update($table, $type_arr);
	}
	public function insertTable($table,$arr){
		$this->db->insert($table,$arr);
		return $this->db->insert_id();
	}
	
} 