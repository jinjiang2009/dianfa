<?php
class case_model extends CI_Model{

	/**
	 * 案件委托
	 */
	function __construct(){
		parent::__construct();
		$this->load->database();
	}	
	
	/**
	 * 添加新用户账号
	 */
	function add_case($p){
		return $this->db->insert('df_case',$p);
	}
	
	/**
	 * 案件招标列表
	 */
	function caselist($p){//id,pricetype,name,citynames,deadline,bid,createtime,deadline_date,status
		$s = 'select id,pricetype,name,citynames,deadline,bid,createtime,deadline_date,nickname,status from df_case where 1=1 '.$p ['status'].$p ['deadline_date'].' and is_show=1 and is_audit=1  order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_case where 1=1 '.$p ['status'].$p ['deadline_date'].' and is_show=1 and is_audit=1 ';
		$r['list'] = $this->db->query($s)->result_array();
// 		/$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 推荐律师
	 */
	function lawyer_uc($p){
		$s = 'select ucode from acl_recommend where is_show = 1 and type='.$p['type'].' order by sort asc limit 0,'.$p['limit'];
		$_r = $this->db->query($s)->result_array();
		$d=array();
		if(!empty($_r)){
			foreach ($_r as $k=>$v){
				if(!empty($v['ucode']))
				$d[$k]=$v['ucode'];
			}
		}
		return $d;
	}
	
	/**
	 * 推荐律师
	 */
	function lawyer_uc2($p){
		$s = 'select ucode,img,url from acl_recommend where is_show = 1 and type='.$p['type'].' order by sort asc limit 0,'.$p['limit'];
		$_r = $this->db->query($s)->result_array();
		$d=array();
		if(!empty($_r)){
			foreach ($_r as $k=>$v){
				if(!empty($v['ucode']))
					$d[$v['ucode']]=$v;
			}
		}
		return $d;
	}
	
	function recomand_lawyer($p){
		 $s = 'select ucode,name,remark,contract,rerritory,icon,citynames,descript from df_lawyer where ucode in("'.$p.'") ';
		 $_r = $this->db->query($s)->result_array();
		 $d=array();
		 if(!empty($_r)){
		 	foreach ($_r as $k=>$v){
		 		if(!empty($v))
		 			$d[$v['ucode']]=$v;
		 	}
		 }
		 return $d;
	}
	
	/**
	 * 案件招标列表
	 */
	function casel($p){
		$s = 'select id,pricetype,name,citynames,deadline,bid,createtime,deadline_date,status from df_case where 1=1 '.$p ['type'].$p ['city'].$p ['province'].$p ['date'].$p ['status'].' and is_show=1 and is_audit=1  order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_case where 1=1 '.$p ['type'].$p ['city'].$p ['province'].$p ['date'].$p ['status'].' and is_show=1 and is_audit=1 ';
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 律师所在领域
	 */
	function territory(){
		 $s = "select id,name from df_territory where is_show =1";
		return $this->db->query($s)->fetchAll('id');
	}
	
	/**
	 * 添加合同意见
	 */
	function sdd_suggest($p){
		return $this->db->insert('df_contract_suggest',$p);
	}
	
	/**
	 * 获取案件招标的性情
	 */
	function case_info($id){
		return $this->db->get_where('df_case', array('id' => $id,'is_audit'=>1,'is_show'=>1), 1, 0)->result_array();
	}
	
	/**
	 * 案件投标
	 */
	function case_bid($id){
		return $this->db->get_where('df_bid', array('tid' => $id))->result_array();
	}
	
	/**
	 * 投标律师
	 */
	function case_lawyer($p){
		$s = 'select * from df_lawyer where 1=1 '.$p ['ids'].'  order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_lawyer where 1=1 '.$p ['ids'].'  ';
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 律师投标
	 * @param unknown_type $p
	 */
	function lawyer_bid($p){
		$s = 'update df_case set `bid`=`bid`+1 where id="'.$p['tid'].'"';
		$this->db->query($s);
		$this->db->insert('df_bid',$p);
		return $this->db->insert_id();
	}
	
	/**
	 * 列表页判断投标信息
	 */
	function case_deliver($ids,$ucode){
		$s = ' select id,ucode,tid,status from df_bid where tid in ('.$ids.') and is_show = 1 and ucode = "'.$ucode.'"';
		$_r= $this->db->query($s)->result_array();
				$d=array();
		if(!empty($_r)){
			foreach ($_r as $k=>$v){
				if(!empty($v['tid']))
				$d[$v['tid']]=$v;
			}
		}
		return $d;
	}
	
	
} 