<?php
class base_model extends CI_Model{

	/**
	 * 
	 */
	function __construct(){
		parent::__construct();
		$this->load->database();
	}	
	
	/**
	 * 浏览记录
	 * @param array $d
	 */
	function pv_monitor($d){
		$p=$d;
		$p['class'] = !empty($p['class'])?' and c="'.$p['class'].'"':'';
		$p['method'] = !empty($p['method'])?' and a="'.$p['method'].'"':'';
		$p['date'] = !empty($p['date'])?' and create_time ="'.$p['date'].'"':'';
		$s = 'select id from df_pv where 1=1'.$p['class'].$p['method'].$p['date'].' limit 0,1';
		$_r = $this->db->query($s)->result_array();
		if(!empty($_r)){
			$s1 = 'update df_pv set `num`=`num`+1 where 1=1'.$p['class'].$p['method'].$p['date'];
			$this->db->query($s1);
		}else{
			$par = array('c'=>$d['class'],'a'=>$d['method'],'create_time'=>$d['date']);
			$this->db->insert('df_pv',$par);
		}
	}
	
	/**
	 * 验证验证码
	 */
	function judge_code($p,$t){
		 $sql ="select code,id,st from df_identifying where st<='{$t}' and et>='{$t}' and mobile ='{$p['mobile']}' limit 0,1";
		return $this->db->query($sql)->result_array();
	}
	
	/**
	 * 添加验证码
	 */
	function add_code($p){
		return $this->db->insert('df_identifying',$p);
	}
	
	/**
	 * 修改验证码
	 */
	function edit_code($p){
		return $this->db->update('df_identifying',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 验证手机号是否绑定账号后注册账号
	 * @param str $p
	 */
	function mobile_exist($p){
		$s = "select id,ucode from df_member where mobile ='{$p}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 增加或减少积分插入日志
	 */
	function integral_change($p){
		
		if($p['type']==1)
			$c = '+';
		else 
			$c = '-';
		 $u = 'update df_member set `integral`=`integral`'.$c.$p['num'].' where ucode="'.$p['ucode'].'"';
		$r = $this->db->query($u);
		if(!empty($r))
			return $this->db->insert('df_integral_log',$p);
		else 
			return false;	
	}
	
	/**
	 * 地区范围联动
	 */
	function region($id=''){
		if (empty($id)){
			$s = "select id,uuid,name,pid,type,abbreviation from df_region where type =1";
		}else{
			$s = "select id,uuid,name,pid,type,abbreviation from df_region where pid = '{$id}'";
		}
		return $this->db->query($s)->result_array();
	}
	
	
	
} 