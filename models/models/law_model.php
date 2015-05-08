<?php
/**
 * 法律法规
 * @author cx
 *
 */
class law_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}	
	
	/**
	 * 最新法律法规
	 */
	function newlaw($l,$type){
		$or =empty($type)?'promulgation_date':'effective_date';
		$sql = 'select id,name,promulgation_date,promulgation from df_law where is_show=1 '.$type.' order by '.$or.' desc limit 0,'.$l;
		$total = 'select count(id) total  from df_law where is_show=1 '.$type;
		$re = array();
		$re['list'] = $this->db->query($sql)->result_array();
		$re['total'] = $this->db->query($total)->result_array();
		return $re;
	}
	
	/**
	 * 常用法律
	 */
	function everydayLaw($ids){
		$sql = 'select id,name,effective_date,promulgation from df_law where is_show=1 and id in('.$ids.')';
		return  $this->db->query($sql)->result_array();
	}
	
	/**
	 * 添加法律法规意见
	 */
	function sdd_suggest($p){
		return $this->db->insert('df_law_suggest',$p);
	}
	
	/**
	 * 最新法律法规
	 */
	function newlawtotal($type){
		$total = 'select count(id) total  from df_law where is_show=1 '.$type;
		$re = array();
		$re['total'] = $this->db->query($total)->result_array();
		return $re;
	}
	
	/**
	 * 法律法规详情
	 */
	function lawinfo($id){
		return $this->db->get_where('df_law', array('id' => $id,'is_show'=>1), 1, 0)->result_array();
	}
	
	/**
	 * 
	 */
	function lawlist($p){
		$where = $p['name'].$p['range'].$p['promulgation'].$p['time_effect'].$p['effect_level'].$p['ps_date'].$p['pe_date'].$p['es_date'].$p['ee_date'];
		$re = array();
		$s = "select * from df_law where 1=1 ".$where.'  order by promulgation_date desc limit '.$p['start'].','.$p['limit'];
		$t = "select count(id) total from df_law where 1=1 ".$where;
		$re['list'] = $this->db->query($s)->result_array();
		$re['total'] = $this->db->query($t)->result_array();
		return $re;
	}
	/*
	 * 获得相关裁判文书
	*/
public function getJudgement($title=null){
 		//var_dump($title);exit;
 		if($title==null){
 			$sql = ' select id ,title from df_judgement  order by id desc limit 15';
 		}else{
 			$sql = ' select id ,title from df_judgement where title like "%'.$title.'%" limit 15 ';
 		}
 		//var_dump($sql);
 		
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
	/*
	 * 获得相关律师
	*/
public function getLawyers($ids=null){
 		
 		if($ids==null){
 			$sql = ' select ucode,name,remark,contract,rerritory,secre,icon from df_lawyer order by createtime desc limit 5 ';
 		}else{
 			$sql = ' select ucode,name,remark,contract,rerritory,secre,icon from df_lawyer  where ucode in' .$ids.' limit 5';
 		}
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
	/*
	 * 获得相关title律师
	*/
public function getJudgementlawyers($title){
 		$sql = ' select laywer_id from df_judgement where laywer_id!="0" and title like "%'.$title.'%" limit 5 ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
} 