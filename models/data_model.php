<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 class data_model extends CI_Model{
  	/**
  	 * 数据处理
  	 */
  	 private  $_db='';
  	 private  $_law='';
     function __construct(){
      header("Content-type:text/html;charset=utf-8");
      parent::__construct();
      $this->_db = $this->load->database('df',true);
     }
     public function getGroup(){
     	$sql = ' select * from q_group ' ;
     	$result = $this->_db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     }
     public function getQuestion(){
     	$sql = ' select * from questions where q_id>6562 ' ;
     	$result = $this->_db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     }
     public function getQuestionTime(){
     	$sql = ' select q_id,q_time from questions where q_id<112467 and q_id>=302 ' ;
     	$result = $this->_db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     }
     public function updateTable($table,$id,$type_arr){
     	$this->db->where('id', $id);
     	$this->db->update($table, $type_arr);
     }
     public function getAnswer(){
     	$sql = ' select * from answers  ' ;
     	$result = $this->_db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     }
     public function insertTable($arr,$table){
     	$this->db->insert($table,$arr);
     }
     public function getJudgement(){
     	//$sql = ' select * from legal_judgment_main where id<=115278 and id>90000 ' ;
     	$sql = ' select * from legal_judgment_main where id<=115278 and id>90000 ' ;
     	$result = $this->_db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     }
     public function modifyJudgement(){
     	//$sql = ' select * from legal_judgment_main where id<=115278 and id>90000 ' ;
     	$sql = ' select * from legal_judgment_main where id<=115278 and id>90000 ' ;
     	$result = $this->_db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     }
     public function getCourt(){
     	$sql = ' select * from legal_court_main  ' ;
     	$result = $this->_db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     }
     public function getRegion(){
     	$sql = ' select * from legal_system_region  ' ;
     	$result = $this->_db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     }
     public function getDfCourt(){
     	$sql = ' select * from df_court where id >500 and id<=1372  ' ;
     	$result = $this->db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     }
     public function updateJudgement($id,$uuid){
     	$sql = ' select id from df_judgement where court=  '.$uuid ;
     	$result = $this->db->query($sql);
     	$arr = $result->result_array();
     	foreach($arr as $k=>$v){
     		$this->db->where('id', $v['id']);
     		$this->db->update('df_judgement', array('court'=>$id));
     	}
     }
     public function getLaw(){
     	$this->_law = $this->load->database('law',true);
     	$sql = ' select * from laws where aid>20000 and  aid<=30000 ' ;
     	$result = $this->_law->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     }
     public function getbjJudgement(){
     	$sql = ' select * from df_bjcourt where id=1  ' ;
     	$result = $this->db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     }
     public function getQuesTimes(){
     	$sql = ' SELECT q.id ,COUNT(a.id) AS tot FROM df_answer a INNER JOIN df_question q on q.id=a.qid where q.id>=50364 and q.id<=112476 GROUP BY q.id HAVING tot>0  ';
     	$result = $this->db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     }
     public function getLawyer(){
     	$sql = ' select * from grab_lawer where id>=10000 and id<95941 ';
     	$result = $this->db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     	
     }
     public function getreg(){
     	$sql = ' select * from df_region where type =1 ';
     	$result = $this->db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     
     }
     public function gettest($id){
     	$sql = ' select * from test where id='.$id;
     	$result = $this->_db->query($sql);
     	$arr = $result->row_array();
     	return $arr;
     }
     public function getlawl(){
     	$sql = ' select * from df_law where id>1';
     	$result = $this->_db->query($sql);
     	$arr = $result->result_array();
     	return $arr;
     }
     public function getLawList($id){
     	$sql = ' select * from df_law where id=26227 ';
     	$result = $this->db->query($sql);
     	$arr = $result->row_array();
     	return $arr;
     }
      public function getOffice(){
      	$sql = ' select title,tel,address from df_office ';
      	$result = $this->_db->query($sql);
      	$arr = $result->result_array();
      	return $arr;
      }
      public function veriOffice($name){
      	$sql = ' select id from df_office where title like "%'.$name.'%" ';
      	$result = $this->_db->query($sql);
      	$arr = $result->row_array();
      	return $arr;
      }
      public function getJu($id){
      	$sql = ' select * from df_judgement_copy3 where id= '.$id;
      	$result = $this->db->query($sql);
      	$arr = $result->row_array();
      	return $arr;
      }
      public function insert($table,$arr){
      	$this->db->insert($table,$arr);
      	return $this->db->insert_id();
      }
  }
