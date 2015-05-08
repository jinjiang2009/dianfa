<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 class laywer_model extends CI_Model{
 	function __construct(){
 		header("Content-type:text/html;charset=utf-8");
 		parent::__construct();
 		
 	}
 	/*
 	 * 获得头部推荐律师
 	 */
 	public function getLaywer(){
 		$sql = ' select ucode,name,icon,region,rerritory from df_lawyer where status!=5 and name !="'.'" order by remark,contract desc limit 7 ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得某个律师信息
 	 */
 	public function getLawyerById($id){
 		$sql = ' select * from df_lawyer where status!=5 and ucode= "'.$id.'"';
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 获得擅长领域
 	 */
 	public function getTerritory($con=null){
 		$sql = ' select * from df_territory where is_show =1  ';
 		if($con){
 			$sql .= ' and pid=0 ';
 		}
 		$sql .= ' order by sort asc ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得二级擅长领域
 	*/
 	public function getSecondTerritory($id){
 		$sql = ' select * from df_territory where  is_show =1 and pid='.$id.' order by sort asc ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得省份
 	*/
 	public function getRegion(){
 		$sql = ' select  * from df_region ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('uuid');
 		return $arr;
 	}
 	/*
 	 * 获得筛选律师
 	 */
 	public function getConLay($wh=null,$page=null,$perpage=null){
 		$con = ' where 1 ';
 		if($wh){
 			if(isset($wh['rerritory']) && $wh['rerritory']>0){
 				$con .= ' and LOCATE('.$wh['rerritory'].',rerritory) ';
 			}
 			if(isset($wh['vip']) && $wh['vip']>0 ){
 				$con .= ' and l.vip='.$wh['vip'];
 			}
 			if(isset($wh['region']) && $wh['region']>0){
 				$con .= ' and l.region='.$wh['region'];
 			}
 			if(isset($wh['secre']) && $wh['secre']>0){
 				$con .= ' and LOCATE('.$wh['secre'].',secre)';
 			}
 			if(isset($wh['name']) && !empty($wh['name'])){
 				$con .= ' and l.name like "%'.$wh['name'].'%"';
 			}
 			if(isset($wh['office']) && !empty($wh['office'])){
 				$con .= ' and l.work like "%'.$wh['office'].'%"';
 			}
 			
 		}
 		$con .= ' and name !="'.'"';
 		$con .= ' order by m.integral desc ';
 		if($page){
 			$nu = ($page-1)*$perpage;
 			$con.=' limit '.$nu.','.$perpage;
 		}
 		$sql = ' select  l.id,l.ucode,l.name,l.icon,l.vip,l.remark,l.contract,l.rerritory,l.descript,l.preyear,l.secre,l.createtime,m.integral from df_lawyer l left join df_member m on l.ucode=m.ucode  ' .$con;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得筛选律师总数
 	*/
 	public function getConLayTotal($wh=null){
 		$con = ' where 1 ';
 		if($wh){
 			if(isset($wh['rerritory']) && $wh['rerritory']>0){
 				$con .= ' and LOCATE('.$wh['rerritory'].',rerritory) ';
 			}
 			if(isset($wh['vip']) && $wh['vip']>0 ){
 				$con .= ' and l.vip='.$wh['vip'];
 			}
 			if(isset($wh['region']) && $wh['region']>0){
 				$con .= ' and l.region='.$wh['region'];
 			}
 			if(isset($wh['secre']) && $wh['secre']>0){
 				$con .= ' and LOCATE('.$wh['secre'].',secre)';
 			}
 			if(isset($wh['name']) && !empty($wh['name'])){
 				$con .= ' and l.name like "%'.$wh['name'].'%"';
 			}
 			if(isset($wh['office']) && !empty($wh['office'])){
 				$con .= ' and l.work like "%'.$wh['office'].'%"';
 			}
 		}
 		$con .= ' and name !="'.'"';
 		$sql = ' select  count(l.id) as total from df_lawyer l left join df_member m on l.ucode=m.ucode  ' .$con;
 	
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 获得律师案例
 	 */
 	public function getJudgement($id){
 		$sql = ' select id,title,court,conclude,category from df_judgement where laywer_id= "'.$id.'"' . ' order  by id desc ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得合同文书
 	 */
 	public function getContract($id){
 		$sql = ' select name,type,createtime from df_contract where ucode= "'.$id.'"';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得文章
 	*/
 	public function getArticle($id){
 		$sql = ' select * from df_article where ucode= "'.$id.'"' . ' order by id desc ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得文章
 	*/
 	public function getArticleById($id){
 		$sql = ' select * from df_article where id= '.$id;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * clicknum 加一
 	 */
 	public function updataArticle($id){
 		$sql = ' update df_article set click_num=click_num+1 where id= '.$id ;
 		return $this->db->query($sql);
 	}
 	/*
 	 * 获得审理机构
 	 */
 	public function getCourt(){
 		$sql = ' select * from df_court ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得案由
 	*/
 	public function getCategory(){
 		$sql = ' select * from df_category ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得文书合同类型 df_type表
 	 */
 	public function getConType(){
 		$sql = ' select  * from df_type ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	
 	
 	public function add_report($p){
 		return $this->db->insert('df_report',$p);
 	}
 	public function getFirm(){
 		$sql = ' select  name,url,img from acl_recommend where is_show=1 and type=13 order by createtime desc limit 25 ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();;
 		return $arr;
 	}
 	public function getRegFirm(){
 		$sql = ' select office,icon,url from df_lfrim where id <>4 ' ;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();;
 		return $arr;

 	}
 }
