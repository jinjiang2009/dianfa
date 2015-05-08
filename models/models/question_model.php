<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 class question_model extends CI_Model{
 	function __construct(){
 		header("Content-type:text/html;charset=utf-8");
 		parent::__construct();
 		
 	}
 	/*
 	 * 获得问题分类
 	 */
 	public function getGroup($type){
 		$sql = ' select * from df_group ' ;
     	$result = $this->db->query($sql);
     	if($type==1){
     		$arr = $result->fetchAll('pid|array|id');
     	}else{
     		$arr = $result->fetchAll('id');
     	}
 		
 		return $arr;
 	}
 	/*
 	 * 获得问题
 	*/
 	public function getQuestion($id='null',$solve=null,$order=null,$limit=null,$is_recom=null){
 		$con = ' where 1 ';
 		$ord = '';
 		$lim = '';
		$re = '';
 		if($solve)$con .= ' and is_solve= '.$solve;
 		if($is_recom)$con .= ' and is_recom= '.$is_recom;
 		if($order)$ord .= $order;
 		if($limit)$lim .= $limit;
 		$sql = ' select * from df_question  '.$con.$ord.$lim;
 		$result = $this->db->query($sql);
 		if($id){
 			$arr = $result->fetchAll($id);
 		}else{
 			$arr = $result->result_array();
 		}
 		
 		return $arr;
 	}
 	/*
 	 * 获得所有用户
 	 */
 	public function getAllUser(){
 		$sql = ' select username,ucode from df_member ' ;
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('ucode');
		return $arr;
 	}
 	/*
		获得资讯列表问题 	
	*/
 	public function getQuestionList($wh,$page=null,$perpage=null){
 		$con = ' where 1 ';
 		if($wh){
 			
 			if(isset($wh['group']) && $wh['group']>0 ){
 				$con .= ' and group_id= '.$wh['group'];
 			}
 			if(isset($wh['region']) && $wh['region']>0){
 				$con .= ' and (region= '.$wh['region'].' or region=0) ';
 			}
 			if(isset($wh['title']) && !empty($wh['title']) ){
 				$con .= ' and title LIKE "%'.$wh['title'].'%" ';
 			}
 			if(isset($wh['is_solve']) && !empty($wh['is_solve']) ){
 				$con .= ' and is_solve= '.$wh['is_solve'];
 			}
 		}
 		$con .= ' order by id desc ' ;
 		if($page){
 			$nu = ($page-1)*$perpage;
 			$con.=' limit '.$nu.','.$perpage;
 		}
 		$sql = ' select  id,uid,group_id,title,is_solve,createtime,times from df_question ' .$con;
 		
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 获得资讯列表问题总数
 	*/
 	public function getQuestionTotal($wh){
 		$con = ' where 1 ';
 		if($wh){
 	
 			if(isset($wh['group']) && $wh['group']>0 ){
 				$con .= ' and group_id= '.$wh['group'];
 			}
 			if(isset($wh['region']) && $wh['region']>0){
 				$con .= ' and (region= '.$wh['region'].' or region=0) ';
 			}
 			if(isset($wh['title']) && !empty($wh['title']) ){
 				$con .= ' and title LIKE "%'.$wh['title'].'%" ';
 			}
 			if(isset($wh['is_solve']) && !empty($wh['is_solve']) ){
 				$con .= ' and is_solve= '.$wh['is_solve'];
 			}
 		}
 		
 		$sql = ' select  count(id) as total  from df_question ' .$con;
 	
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 
 	 * 插入表df_question
 	 */
 	public function insertTable($table,$arr){
 		return $this->db->insert($table,$arr);
 	}
 	/*
 	 * 获得推荐律师
 	 */
 	public function getRecomLawyer(){
 		$sql = ' select  l.name,l.icon,l.rerritory,l.secre,m.integral from df_lawyer l inner join df_member m on l.ucode=m.ucode order by l.createtime desc limit 10' ;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得某一天咨询
 	 */
 	public function getQueById($id){
 		$sql = ' select * from df_question where is_show=1 and  id='.$id;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 获得提问者姓名
 	 */
 	public function getWho($id){
 		$sql = ' select * from df_member where ucode="'.$id.'"';
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 获得答案
 	 */
 	public function getAnswer($id){
 		$sql = ' select a.content,a.createtime,l.icon,l.name,l.ucode from df_answer a left join df_lawyer l on a.ucode=l.ucode where a.qid= '.$id;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得相关咨询
 	 */
 	public function getQue($type,$groupid){
 		$sql = ' select id,title from df_question where is_solve='.$type.' and group_id ='.$groupid.' order by createtime desc limit 6';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 更新表
 	 */
 	public function updateQuestion($id){
 		$sql = ' update df_question  set times=times+1,is_solve=2 where id= '.$id;
 		$result = $this->db->query($sql);
 		
 		return $result;
 	}
 	/*
 	 * 更新表
 	*/
 	public function updateQuestionClick($id){
 		$sql = ' update df_question  set click=click+1 where id= '.$id;
 		$result = $this->db->query($sql);
 		
 		return $result;
 	}
 	/*
 	 * 验证咨询标题
 	 */
 	public function verifyTitle($title){
 		$sql = ' select id from df_question where title like "%'.$title.'%" ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 验证是否有问题补充
 	 */
 	public function judgeAdd($id){
 		$sql = ' select content,createtime from df_ask_add where is_show=1 and qid='.$id;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 更新表
 	*/
 	public function updateAdd($id){
 		$sql = ' update df_question  set is_solve=1 where id= '.$id;
 		$result = $this->db->query($sql);
 	
 		return $result;
 	}
 }
