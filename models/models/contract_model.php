<?php
/**
 * 文书合同
 * @author cx
 *
 */
class contract_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}	
	
	/**
	 * 获得分类
	 */
	function get_type(){
		$s = "select id,type,name from df_type where is_show =1 order by sort asc";
		return $this->db->query($s)->fetchAll('id');
		
	}
	
	/**
	 * 合同法律文书文类
	 */
	function contractlist($p){
		$s = 'select id,tid,name,type,createtime from df_contract where 1=1 '.$p ['type'].$p['tid'].$p['name'].' and is_show=1 and is_audit=1  order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_contract where 1=1 '.$p ['type'].$p['tid'].$p['name'].' and is_show=1 and is_audit=1 ';
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 合同范本排行榜
	 */
	function ranking(){
		$s = 'select id,tid,name,type,createtime from df_contract where 1=1 and type=1 and is_show=1 and is_audit=1  order by id asc limit 0,10';
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 合同法律文书
	 */
	function con_info($id){
		$s = "select id,type,name,content from df_contract where is_show =1 and is_audit =1 and id='{$id}' ";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 合同下载量
	 */
	function down_num($id){
		$s = "update df_contract set `down_num`=`down_num`+1 where id='{$id}'";;
		return $this->db->query($s);
	}
	
	/**
	 * 合同制作审核
	 */
	function adddraft($p){
		$this->db->insert('df_order',$p);
		return $this->db->insert_id();
	}
	
	/**
	 * 推荐管理
	 */
	function recommend($p){
		$s = 'select id,ucode,sort,img,url,name,showtime,coment from acl_recommend where is_show = 1 and type='.$p['type'].' order by sort asc limit 0,'.$p['limit'];
		$_r = $this->db->query($s)->result_array();
		return $_r;
	}
	
	
	function message(){
		$s = 'select id,title from df_mess where is_show = 1 order by sort asc limit 8';
		$result = $this->db->query($s);
 		$arr = $result->result_array();
 		return $arr;
	}
	
	function mess($id){
		$s = 'select id,title,content from df_mess where is_show = 1 and id='.$id;
		$result = $this->db->query($s);
		$arr = $result->row_array();
		return $arr;
	}
	/*
	 * 获得动态公告所有
	 */
	function getdynamicTotal(){
		$sql = ' select count(*) as total from acl_recommend where type=5 and is_show=1 ' ;
		$_r = $this->db->query($sql)->row_array();
		return $_r;
	}
	/*
	 * 获得所有热门资讯
	 */
	function getpopularTotal(){
		$sql = ' select count(*) as total from df_mess where  is_show=1 ' ;
		$_r = $this->db->query($sql)->row_array();
		return $_r;
	}
	
	function messTotal(){
		$sql = ' select count(*) as total from df_mess where is_show=1 ' ;
		$_r = $this->db->query($sql)->row_array();
		return $_r;
	}
	/*
	 * 获得动态
	 */
	function getdynamic($page,$perpage){
		$nu = ($page-1)*$perpage;
		$con =' limit '.$nu.','.$perpage;
		$sql = ' select * from acl_recommend where is_show=1 and type=5 ' .$con;
		$_r = $this->db->query($sql)->result_array();
		return $_r;
	}
	
	
	function getmess($page,$perpage){
		$nu = ($page-1)*$perpage;
		$con =' limit '.$nu.','.$perpage;
		$sql = ' select * from df_mess where is_show=1 ' .$con;
		$_r = $this->db->query($sql)->result_array();
		return $_r;
	}
	/*
	 * 获得动态广告
	 */
	function getAd(){
		$sql = 'select id,name,img,unit_price from df_privatelaw where is_show=1   order by sort desc limit 3 ';
		$re= $this->db->query($sql)->result_array();
		return $re;
	}
	/**
	 * 求职招聘
	 */
	function job($t){
		$s ="select * from df_job where type='{$t}'  order by id desc  limit 0,5";
		$_r = $this->db->query($s)->result_array();
		return $_r;
	}
	
	/**
	 * 获得详情
	 * @param  $p
	 */
	function getInfo($p){
		return $this->db->get_where('df_contract',array('id'=>$p['id']),1,0)->result_array();
	}
	
	/**
	 * 在线或课程详情
	 */
	function onlineCourse($t,$l,$c){
		if($c == 1){
			$s ="select * from df_video where is_show =1 and type = ".$t." order by id desc limit 0,".$l;
		}else if($c == 2){
			$s ="select * from df_newtrain where is_show =1  and type = ".$t."  order by id desc limit 0,".$l;
		}
		$_r = $this->db->query($s)->result_array();
		return $_r;
	}
	
	
	
	
	function onlineBusiness($group,$l){
		$s = "select a.id,a.name from df_newtrain as a where a.group={$group} order by id desc limit 0,".$l;
		$_r = $this->db->query($s)->result_array();
		return $_r;
	}

	function onlineCourseInfo($p){
		return $this->db->get_where('df_newtrain',array('id'=>$p['id']),1,0)->result_array();
	}
	
	function onlineCourse2($t,$l,$t2){
		$s ="select * from df_newtrain where is_show =1  and type = ".$t." and their = ".$t2."  order by id desc limit 0,".$l;
		$_r = $this->db->query($s)->result_array();
		return $_r;
		
	}
	
	
	/*
	 * 综合搜索获得案件
	 */
	function getCase($title){
		$sql = ' select  id,serial,title,court,conclude from df_judgement  where is_show=1 and title like "%'.$title.'%" limit 8 ' ;
		$result = $this->db->query($sql);
		$arr = $result->fetchAll('id');
		return $arr;
	}
	/*
	 * 获得法规
	 */
	function getLaw($name){
		$sql = ' select  id,name from  df_law  where is_show=1 and name like "%'.$name.'%" limit 8 ' ;
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
	/*
	 * 获得咨询
	 */
	function getQuestion($title){
		$sql = ' select  id,uid,group_id,title,is_solve,createtime,times from df_question  where is_show=1 and title like "%'.$title.'%" limit 8 ';
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
	/*
	 * 获得问题分类
	*/
	public function getAskGroup(){
		$sql = ' select * from df_group ' ;
		$result = $this->db->query($sql);
		$arr = $result->fetchAll('id');
		return $arr;
	}
	/*
	 * 获得律师
	 */
	public function getLawyer($name){
		$sql = ' select ucode,name,remark,contract,rerritory,secre,icon from df_lawyer where name like "%'.$name.'%" ';
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
	
	public function getRecommend($table,$arr){
		$this->db->insert($table,$arr);
		return $this->db->insert_id();
	
	}
	
	public function getApplication($table,$arr){
		$this->db->insert($table,$arr);
		return $this->db->insert_id();
	
	}
	
	public function getExperts(){
		$sql = ' select `name`,`desc` from df_expert where is_show=1 order by id asc limit 40 ';
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
	public function getAllExperts(){
		$sql = ' select `id`,`name`,`desc`,`type` from df_expert where is_show=1 order by id asc ';
		$result = $this->db->query($sql);
		$arr = $result->fetchAll('type|array|id');
		return $arr;
	}
	public function getExpertType(){
		$sql = ' select * from df_expert_type  ';
		$result = $this->db->query($sql);
		$arr = $result->fetchAll('id');
		return $arr;
	}
	public function getTraimGroup(){
		$sql = ' select * from df_train_group where is_show=1 ';
		$result = $this->db->query($sql);
		$arr = $result->fetchAll('id');
		return $arr;
	}
	#法律文章
	public function getArticle(){
		$sql = ' select a.*,l.name from df_article a inner join df_lawyer l on a.ucode=l.ucode where a.is_show=1 order by a.id desc  limit 10 ' ;
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
	public function getArticleType($p=false){
 		$sql = ' select  id,name,pid  from df_article_category where is_show=1  ' ;
 		$result = $this->db->query($sql);
 		if($p){
 			$arr = $result->fetchAll('pid|array|id');
 		}else{
 			$arr = $result->fetchAll('id');
 		}
 		
 		return $arr;
 	}
	public function getPerson($id){
		$sql = ' select * from df_recommend where is_show =1 and judge='.$id;
		$result = $this->db->query($sql);
		$arr = $result->fetchAll('type|array|id');
		return $arr;
	}
	public function getExpert(){
		$sql = ' select * from df_zhuan where is_show = 1 and type=2 ';
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
	public function judgeIp($id,$ip){
		$sql = ' select *  from df_toupiao where recommendid='.$id.' and ip="'.$ip.'"';
		$result = $this->db->query($sql);
		$arr = $result->row_array();
		return $arr;
	}
	public function getAllRecommend(){
		$sql = ' select *  from df_recommend where is_show=1 and judge=2 ';
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
	public function getNowRecommend($id){
		$sql = ' select name,poll from df_recommend where is_show=1 and id= '.$id;
		$result = $this->db->query($sql);
		$arr = $result->row_array();
		return $arr;
	}
	public function getTop(){
		$sql = " select * from df_recommend where judge=2 and is_show=1 order by poll desc limit 15";
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
	public function insertleft($table,$arr){
		$this->db->insert($table,$arr);
		return $this->db->insert_id();
	}
	public function updatePoll($id){
		$sql = ' update df_recommend set poll=poll+1 where id= '.$id;
		$result = $this->db->query($sql);
		return $result;
	}
	public function getJudge($id,$ip){
		$sql = ' select *  from df_toupiao where recommendid='.$id.' and ip="'.$ip.'"';
		$result = $this->db->query($sql);
		$arr = $result->row_array();
		return $arr;
	}
	public function getInsert($table,$arr){
		$this->db->insert($table,$arr);
		return $this->db->insert_id();
	}
	public function getUpdate($id){
		$sql = ' update df_recommend set poll=poll+1 where id= '.$id;
		$result = $this->db->query($sql);
		return $result;
	}
	public function getNow($id){
		$sql = ' select name,poll from df_recommend where is_show=1 and id= '.$id;
		$result = $this->db->query($sql);
		$arr = $result->row_array();
		return $arr;
	}
	public function getBot(){
		$sql = " select * from df_recommend where judge=1 and is_show=1 order by poll desc limit 10";
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
	public function getAll(){
		$sql = ' select *  from df_recommend where is_show=1 and judge=1 ';
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
	public function getSet(){
		$sql = ' select *  from df_zhuan where is_show=1 and type=2 ';
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
	public function getBotByPull(){
		$sql = " select name,poll from df_recommend where judge=1 and is_show=1 order by poll desc limit 20";
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
	public function getJudgeByPull($ip){
		$sql = ' select *  from df_toupiao where type=2 and ip="'.$ip.'"';
		$result = $this->db->query($sql);
		$arr = $result->result_array();
		return $arr;
	}
	public function getInsertPoll($post,$ip){
		$sql = ' insert into df_toupiao (ip,type,is_show,recommendid) values ';
		foreach($post['toup'] as $k=>$v){
			$sql .= '("'.$ip.'"'.',2,1,'.$v.'),' ;
		}
		$result = $this->db->query(substr($sql,0,-1));
		return $result;
	}
	public function getUpdatePoll($post){
		$sql = ' update df_recommend set poll= poll+1 where id in('.join($post['toup'],',').')';
		$result = $this->db->query($sql);
		return $result;
	}
} 