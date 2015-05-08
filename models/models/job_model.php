<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 class job_model extends CI_Model{
 	function __construct(){
 		header("Content-type:text/html;charset=utf-8");
 		parent::__construct();
 		
 	}
 	/*
 	 * 获得工作
 	 */
	public function getJob($type,$current=null,$perpage=null){
		$page = '';
		if($current){
			$tem = ($current-1)*$perpage;
			$page .= ' order by createtime desc  limit '.$tem.','.$perpage;
		}
		$sql = ' select * from df_job  where type= '.$type.' and is_show =1 '.$page;
		$result = $this->db->query($sql);
		$arr = $result->fetchAll('id');
		return $arr;
	}
	/*
	 * 获得回复
	 */
	public function getJobReply($id,$current=null,$perpage=null){
		$page = '';
		if($current){
			$tem = ($current-1)*$perpage;
			$page .= ' order by createtime desc  limit '.$tem.','.$perpage;
		}
		$sql = ' select * from df_job_reply  where job_id= '.$id.' and is_show =1 '.$page;
		$result = $this->db->query($sql);
		$arr = $result->fetchAll('id');
		return $arr;
	}
	/*
	 * 获得某个帖子的具体内容
	 */
	public function getJobCon($id){
		$sql = ' select title,createtime,content from  df_job j inner join df_job_con c on j.id=c.job_id where j.id= '.$id;
		$result = $this->db->query($sql);
		$arr = $result->row_array();
		return $arr;
	}
	/*
	 * 获取回复
	 */
	public function getReply($str){
		$sql = ' SELECT * FROM (SELECT * FROM df_job_reply where job_id in('.$str.')'.' ORDER BY  createtime DESC ) AS a GROUP BY job_id ORDER BY createtime DESC LIMIT 10 ';
		$result = $this->db->query($sql);
		$arr = $result->fetchAll('job_id');
		return $arr;
	}
	/*
	 * 插入表数据
	 */
	public function insertTable($table,$arr){
		 $this->db->insert($table,$arr);
		 return $this->db->insert_id();
	}
	/*
	 * 获得广告位
	 */
	public function getAd(){
		$sql = ' select * from acl_recommend where is_show= 1 and type=11';
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
 }
