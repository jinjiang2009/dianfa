<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 class judgement_model extends CI_Model{
 	function __construct(){
 		header("Content-type:text/html;charset=utf-8");
 		parent::__construct();
 		
 	}
 	/*
 	 * 获得省份
 	 */
 	public function getRegion(){
 		$sql = ' select  * from df_region where pid=138257780684828010 ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得文书类型
 	 */
 	public function getAmanuensis(){
 		$sql = ' select  * from df_amanuensis  ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得审理机构
 	*/
 	public function getInstitution(){
 		$sql = ' select  * from df_institution  ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得案由大的分类
 	 */
 	public function getCategory(){
 		$sql = ' select  * from df_category where pid=0 ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得全部案由
 	*/
 	public function getAllCategory(){
 		$sql = ' select  * from df_category  ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得全部法院
 	*/
 	public function getAllCourt(){
 		$sql = ' select  * from df_court  ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得全部文书类型
 	*/
 	public function getAllAmanuensis(){
 		$sql = ' select  * from df_amanuensis  ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得民事案由
 	*/
 	public function getCivil(){
 		$sql = ' select  * from df_category  where pid=1 and type=2';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得判决文书
 	 */
 	public function getJudge($wh=null,$page=null,$perpage=null){
 		$con = ' where 1 ';
 		if($wh){
 			if(isset($wh['id'])){
 				$con .= ' and id= '.$wh['id'];
 			}
 			if(isset($wh['laywer_id'])&&!empty($wh['laywer_id'])){
 				$con .= ' and lawer like "%'.$wh['laywer_id'].'%"';
 			}
 			if(isset($wh['serial'])&&!empty($wh['serial'])){
 				$con .= ' and serial= "'.$wh['serial'].'"';
 			}
 			if(isset($wh['title'])&&!empty($wh['title'])){
 				$con .= ' and title like "%'.$wh['title'].'%"';
 			}
 			if(isset($wh['category'])&&$wh['category']>0){
 				$con .= ' and category= '.$wh['category'];
 			}
 			if(isset($wh['region'])&&$wh['region']>0){
 				$con .= ' and region= '.$wh['region'];
 			}
 			if(isset($wh['institution'])&&$wh['institution']>0){
 				$con .= ' and institution= '.$wh['institution'];
 			}
 			if(isset($wh['amanuensis'])&&$wh['amanuensis']>0){
 				$con .= ' and institution= '.$wh['amanuensis'];
 			}
 			if(isset($wh['process'])&&$wh['process']>0){
 				$con .= ' and process= '.$wh['process'];
 			}
 			if(isset($wh['conclude_str'])){
 			 
 				$con .=empty($wh['conclude'])?'': ' and conclude>= '.$wh['conclude'];
 			}
 			if(isset($wh['conclude_end'])){
 				$con .=empty($wh['conclude'])?'':  ' and conclude<= '.$wh['conclude'];
 			}
 			

 		}
 		
 		if(isset($wh['court'])&&!empty($wh['court'])){
 			$con .= ' and c.name like "%'.$wh['court'].'%"';
 			$con.= '  order by j.createtime desc ';
 			if($page){
 				$nu = ($page-1)*$perpage;
 				$con.=' limit '.$nu.','.$perpage;
 			}
 			$sql = ' select  j.id,j.serial,j.title,j.court,j.category,j.lawer,j.conclude from df_judgement j inner join df_court c on j.court=c.id ' .$con;
 		
 		}else{
 			$con.= '  order by id desc ';
 			if($page){
 				$nu = ($page-1)*$perpage;
 				$con.=' limit '.$nu.','.$perpage;
 			}
 			$sql = ' select  id,serial,title,court,category,conclude,lawer from df_judgement  ' .$con;
 		}
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得案件总数
 	 */
 	public function getJudgeTotal($wh=null,$page=null,$perpage=null){
 		$con = ' where 1 ';
 		if($wh){
 			if(isset($wh['id'])){
 				$con .= ' and id= '.$wh['id'];
 			}
 			if(isset($wh['laywer_id'])&&!empty($wh['laywer_id'])){
 				$con .= ' and lawer like "%'.$wh['laywer_id'].'%"';
 			}
 			if(isset($wh['serial'])&&!empty($wh['serial'])){
 				$con .= ' and serial= "'.$wh['serial'].'"';
 			}
 			if(isset($wh['title'])&&!empty($wh['title'])){
 				$con .= ' and title like "%'.$wh['title'].'%"';
 			}
 			if(isset($wh['category'])&&$wh['category']>0){
 				$con .= ' and category= '.$wh['category'];
 			}
 			if(isset($wh['region'])&&$wh['region']>0){
 				$con .= ' and region= '.$wh['region'];
 			}
 			if(isset($wh['institution'])&&$wh['institution']>0){
 				$con .= ' and institution= '.$wh['institution'];
 			}
 			if(isset($wh['amanuensis'])&&$wh['amanuensis']>0){
 				$con .= ' and institution= '.$wh['amanuensis'];
 			}
 			if(isset($wh['process'])&&$wh['process']>0){
 				$con .= ' and process= '.$wh['process'];
 			}
 			if(isset($wh['conclude_str'])){
 	
 				$con .=empty($wh['conclude'])?'': ' and conclude>= '.$wh['conclude'];
 			}
 			if(isset($wh['conclude_end'])){
 				$con .=empty($wh['conclude'])?'':  ' and conclude<= '.$wh['conclude'];
 			}
 	
 	
 		}
 	
 		if(isset($wh['court'])&&!empty($wh['court'])){
 			$con .= ' and c.name like "%'.$wh['court'].'%"';
 			//$con.= '  order by j.createtime desc ';
 			if($page){
 				$nu = ($page-1)*$perpage;
 				$con.=' limit '.$nu.','.$perpage;
 			}
 			$sql = ' select  count(j.id) as total from df_judgement j inner join df_court c on j.court=c.id ' .$con;
 	
 		}else{
 			//$con.= '  order by id desc ';
 			if($page){
 				$nu = ($page-1)*$perpage;
 				$con.=' limit '.$nu.','.$perpage;
 			}
 			$sql = ' select  count(id) as total from df_judgement  ' .$con;
 		}
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 获得文章
 	 */
 	public function getArticle($wh=null,$page=null,$perpage=null){
 		$con = ' where is_show=1 ';
 		if($wh){
 			
 			if(isset($wh['title'])&&!empty($wh['title'])){
 				$con .= ' and title like "%'.$wh['title'].'%"';
 			}
 			if(isset($wh['type'])&&!empty($wh['type'])){
 				$con .= ' and type = '.$wh['type'];
 			}
 	
 		}
 		$con.= '  order by id desc ';
 		if($page){
 			$nu = ($page-1)*$perpage;
 			$con.=' limit '.$nu.','.$perpage;
 		}
 		$sql = ' select  id,title,content,createtime,ucode  from df_article  ' .$con;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得文章
 	*/
 	public function getArticleTotal($wh=null){
 		$con = ' where is_show=1 ';
 		if($wh){
 	
 			if(isset($wh['title'])&&!empty($wh['title'])){
 				$con .= ' and title like "%'.$wh['title'].'%"';
 			}
 			if(isset($wh['type'])&&!empty($wh['type'])){
 				$con .= ' and type = '.$wh['type'];
 			}
 	
 		}
 		
 		$sql = ' select  count(id) as total  from df_article  ' .$con;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 获得所有法院
 	 */
 	public function getCourt(){
 		$sql = ' select  * from df_court  where is_show=1 ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得所有审理机构
 	 */
 	public function getAllInstitution(){
 		$sql = ' select  * from df_institution   ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得裁判文书内容
 	 */
 	public function getContent($ids){
 		$sql = ' select  * from df_judgement_con where id in('.$ids.')   ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得所有文章分类
 	 */
 	public function getArticleType($p=false){
 		$sql = ' select  id,name,pid,img  from df_article_category where is_show=1  ' ;
 		$result = $this->db->query($sql);
 		if($p){
 			$arr = $result->fetchAll('pid|array|id');
 		}else{
 			$arr = $result->fetchAll('id');
 		}
 		
 		return $arr;
 	}
 	/*
 	 * 获得所有文章
 	 */
 	public function getAllArticle(){
 		$sql = ' select  id,title,ucode,type from df_article where is_show=1 order by createtime desc ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('type|array|id');
 		return $arr;
 	}
 	/*
 	 * 获得所有合同
 	 */
 	public function getAllContract(){
 		$sql = ' select id,tid,name from df_contract where type=1 and is_show=1 and is_audit=1  order by createtime desc ' ;
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('tid|array|id');
 		return $arr;
 	}
 	/*
 	 * 获得所有合同分类
 	 */
 	public function getAllType(){
 		$sql = 'select id,name from df_type where type=1 and is_show=1 order by createtime desc  ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获取某个合同的具体内容
 	 */
 	public function getContractContent($id){
 		$sql = ' select tid,name,content from df_contract where id= '.$id;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 		
 	}
 	
 	public function getClassify($l,$type){
 		
 		//$s ="select * from df_article where is_show =1 and type = ".$t." order by id desc limit 0,".$l;
 	   
 		$s = "select a.id,b.name,a.title from df_article as a,df_article_category as b where a.type=b.id and a.type={$type} order by id desc limit 0,".$l ;
 		//$s = ' selcet a.* from df_article a inner join  df_article_category c on a.type=b.id'
 		$_r = $this->db->query($s)->result_array();
 		return $_r;
 		//var_dump($_type);exit;
 	}
 	
 	//public function getCladdify1($type1){
 		//$type1="select id from df_article_category where id=$type ";
 		//$_type = $this->db->query($type1)->result_array();
 		
 	//}
 	
 	
 	/*
 	 * 获得相关法律
 	 */
 	public function getLaw($title=null){
 		//var_dump($title);exit;
 		if($title==null){
 			$sql = ' select id ,name from df_law  order by id desc limit 15';
 		}else{
 			$sql = ' select id ,name from df_law where name like "%'.$title.'%" limit 15 ';
 		}
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得相关律师
 	 */
 	public function getLawyer($ids=null){
 		
 		if($ids==null){
 			$sql = ' select ucode,name,remark,contract,rerritory,secre,icon from df_lawyer order by createtime desc limit 5 ';
 		}else{
 			$sql = ' select ucode,name,remark,contract,rerritory,secre,icon from df_lawyer where ucode in' .$ids.' limit 5 ';
 		}
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得相关title律师
 	 */
 	public function getJudgeLawyer($title){
 		$sql = ' select laywer_id from df_judgement where laywer_id!="0" and title like "%'.$title.'%" limit 5 ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	function getJudgeById($id){
 		$sql = ' select *from df_judgement where id= '.$id;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	public function insert($table,$arr){
 		$this->db->insert($table,$arr);
 		return $this->db->insert_id();
 	}
 	
 	public function getRec(){
 		$sql = ' select * from acl_recommend where is_show=1 and type=5 order by createtime desc limit 12 ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	public function mess(){
 		$sql = ' select * from df_mess where is_show=1 order by createtime desc limit 12 ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;		
 	}
 }
