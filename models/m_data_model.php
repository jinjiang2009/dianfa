<?php
class m_data_model extends CI_Model{

	/**
	 * 数据处理
	 */
	private  $_db='';
	function __construct(){
		
		parent::__construct();
		$this->load->database();
		//$this->_db = $this->load->database('df',true);
	}	
	
	/**
	 * 分类列表
	 */
	function typelist($p){
		$s = 'select * from df_type where 1=1'.$p['type'].$p['name'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(*) total  from df_type where 1=1 '.$p['type'].$p['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 添加分类
	 */
	function typeadd($p){
		return $this->db->insert('df_type',$p);
	}
	
	/**
	 * 分类详情
	 */
	function typeinfo($id){
		$s = "select * from df_type where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 修改类型信息
	 */
	function typeedit($p){
		return $this->db->update('df_type',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 合同文书/法律文书 列表
	 */
	function contractlist($p){
		$s = 'select * from df_contract where 1=1'.$p['tid'].$p['type'].$p['name'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(*) total  from df_contract where 1=1 '.$p['tid'].$p['type'].$p['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 添加合同文书/法律文书
	 */
	function contractadd($p){
		return $this->db->insert('df_contract',$p);
	}
	
	/**
	 * 合同文书/法律文书详情
	 */
	function contractinfo($id){
		$s = "select * from df_contract where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 修改合同文书/法律文书信息
	 */
	function contractedit($p){
		return $this->db->update('df_contract',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 获得分类
	 * @param bool $t
	 */
	function getType($t=false){
		if(empty($t)){
			$s = 'select * from df_type ';
		}else{
			$s = 'select * from df_type where is_show = 1 ';
		}
		$r = $this->db->query($s)->result_array();
		$d = array();
		if(!empty($r)){
			foreach ($r as $k=>$v){
				$d[$v['id']]=$v;
			}
		}
		return $d;
	}
	
	/**
	 *  案件分类列表
	 */
	function case_type($p){
		$s = 'select * from df_category where 1=1'.$p['type'].$p['name'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(*) total  from df_category where 1=1 '.$p['type'].$p['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 添加 案件分类
	 */
	function case_typeadd($p){
		return $this->db->insert('df_category',$p);
	}
	
	/**
	 *  案件分类详情
	 */
	function case_typeinfo($id){
		$s = "select * from df_category where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 修改 案件分类
	 */
	function case_typeedit($p){
		return $this->db->update('df_category',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 案例文书
	 */
	function judgementlist($p){
		$s = 'select id,laywer_id,serial,title,court,category,region,institution,click,createtime,amanuensis,is_show from df_judgement where 1=1'.$p['name'].' order by id asc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_judgement where 1=1 '.$p['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	
	function messagelist($p){
		$s = 'select id,title,content,sort,createtime,is_show from df_mess where 1=1'.$p['title'].' order by id asc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_mess where 1=1 '.$p['title'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	function locallist($p){
		$s = 'select id,type,upload1,upload2,upload3,upload4 from df_federation order by id asc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_federation where 1=1 ';
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	function arguementlist($p){
		$s = 'select * from df_application order by id asc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_application where 1=1 ';
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	function lfrim($p){
		$s = 'select m.id,m.username,m.email,m.mobile,l.license,l.office,l.city,l.local from df_lfrim as l,df_member as m where m.ucode=l.ucode order by m.id asc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_member where 1=1 ';
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 添加 案例文书
	 */
	function judgementadd($p){
		return $this->db->insert('df_judgement',$p);
	}
	
	
	function messageadd($p){
		return $this->db->insert('df_mess',$p);
	}
	
	function localadd($docArray){
		return $this->db->insert('df_federation',$docArray);
	}
	/**
	 *  案例文书详情
	 */
	function judgementinfo($id){
		$s1 = "select * from df_judgement where id='{$id}'";
		$s2 = "select * from df_judgement_con where id='{$id}'";
		$r1 = $this->db->query($s1)->result_array();
		$r2 = $this->db->query($s2)->result_array();
		if(!empty($r2[0]['content']))$r1[0]['content'] = $r2[0]['content'];
		return $r1;
	}
	
	/**
	 * 修改 案例文书
	 */
	function judgementedit($p){
		$c = array();
		$c['content'] = $p['content'];
		unset($p['content']);
		$this->db->update('df_judgement_con',$c,array('id'=>$p['id']));
		return $this->db->update('df_judgement',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 产品列表
	 */
	function product($p){
		$s = 'select * from df_privatelaw where 1=1'.$p['type'].$p['name'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(*) total  from df_privatelaw where 1=1 '.$p['type'].$p['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 添加产品
	 */
	function productadd($p){
		return $this->db->insert('df_privatelaw',$p);
	}
	
	/**
	 * 产品详情
	 */
	function productinfo($id){
		$s = "select * from df_privatelaw where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 修改产品信息
	 */
	function productedit($p){
		return $this->db->update('df_privatelaw',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 指导案例 列表
	 */
	function guide_caselist($p){
		 $s = 'select * from df_guide_case where 1=1'.$p['type'].$p['name'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(*) total  from df_guide_case where 1=1 '.$p['type'].$p['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 添加指导案例
	 */
	function guide_caseadd($p){
		return $this->db->insert('df_guide_case',$p);
	}
	
	/**
	 * 指导案例详情
	 */
	function guide_caseinfo($id){
		$s = "select * from df_guide_case where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 修改指导案例
	 */
	function guide_caseedit($p){
		return $this->db->update('df_guide_case',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 法律法规 列表
	 */
	function lawlist($p){
		$s = 'select * from df_law where 1=1'.$p['time_effect'].$p['cat'].$p['promulgation'].$p['effect_level'].$p['name'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(*) total  from df_law where 1=1 '.$p['time_effect'].$p['cat'].$p['promulgation'].$p['effect_level'].$p['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 添加法律法规
	 */
	function lawadd($p){
		return $this->db->insert('df_law',$p);
	}
	
	/**
	 * 法律法规详情
	 */
	function lawinfo($id){
		$s = "select * from df_law where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 修改法律法规
	 */
	function lawedit($p){
		return $this->db->update('df_law',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 案件招标数据
	 */
	function case_bidlist($p){
		$s = 'select * from df_case where 1=1 '.$p ['name'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_case where 1=1 '.$p ['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 修改法律法规
	 */
	function case_bidedit($p){
		return $this->db->update('df_case',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 合同建议
	 */
	function contract_suggest($p){
		$s = 'select * from df_contract_suggest where 1=1 '.$p ['name'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_contract_suggest where 1=1 '.$p ['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 修改合同
	 */
	function contract_suggestedit($p){
		return $this->db->update('df_contract_suggest',$p,array('id'=>$p['id']));
	}
	
	
	/**
	 * 合同建议
	 */
	function law_suggest($p){
		$s = 'select * from df_law_suggest where 1=1 '.$p ['name'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_law_suggest where 1=1 '.$p ['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 修改法律法规建议
	 */
	function law_suggestedit($p){
		return $this->db->update('df_law_suggest',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 合同产品需求
	 * @param unknown_type $p
	 * @return unknown
	 */
	function contract_need($p){
	    $s = 'select * from df_order where 1=1 and type=1 '.$p ['name'].$p ['order_code'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_order where 1=1 '.$p ['name'].$p ['order_code'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	/**
	 *产品需求
	 * @param unknown_type $p
	 * @return unknown
	 */
	function product_need($p){
		$s = 'select * from df_order where 1=1 and type=3  '.$p ['name'].$p ['order_code'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(id) total  from df_order where 1=1 '.$p ['name'].$p ['order_code'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 产品详情 
	 */
	function prodInfo($id){
		return $this->db->get_where('df_order',array('id'=>$id) ,1, 0)->result_array();
	}
	
	/**
	 * 用户详情
	 * @param str $ucode
	 */
	function membInfo($ucode){
		return $this->db->get_where('df_member',array('ucode'=>$ucode) ,1, 0)->result_array();
	}
	
	/**
	 * 获得分类
	 */
	function get_type(){
		$s = "select id,type,name from df_type where is_show =1 order by sort asc";
		return $this->db->query($s)->fetchAll('id');
	
	}
	
	/**
	 * 获得先关产品信息
	 */
	function corinfo($id){
		return $this->db->get_where('df_privatelaw',array('id'=>$id) ,1, 0)->result_array();
	}
	
	/**
	 * 修改类型信息
	 */
	function orderedit($p){
		return $this->db->update('df_order',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 案例详情
	 */
	function caseInfo($id){
		return $this->db->get_where('df_case',array('id'=>$id),1,0)->result_array();
	}
	
	function orderInfo($id){
		return $this->db->get_where('df_order',array('id'=>$id),1,0)->result_array();
	}
	
	/**
	 * 修改案例信息
	 */
	function caseedit($p){
		return $this->db->update('df_case',$p,array('id'=>$p['id']));
	}
	
	/**
	 * 律师管理
	 */
	function lawyer($p){
		$l = "select id,name,is_grab,license,sex,vip,status,office,ucode,createtime from df_lawyer where 1 ".$p['name']." order by id desc limit ".$p['start'].",".$p['limit'];
		$t = "select count(id) total from df_lawyer  where 1 ".$p['name'];
		$r['list'] = $this->db->query($l)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 律师详情
	 */
	function lawyerInfo($id){
		return $this->db->get_where('df_lawyer',array('ucode'=>$id),1,0)->result_array();
	}
	
	/**
	 * 用户管理
	 */
	function memeber($p){
		$l = "select id,identity,loginfrom,mobile,email,ucode,account,create_time from df_member  order by id desc limit ".$p['start'].",".$p['limit'];
		$t = "select count(id) total from df_member ";
		$r['list'] = $this->db->query($l)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 用户详情
	 */
	function memberInfo($id){
		return $this->db->get_where('df_member',array('ucode'=>$id),1,0)->result_array();
	}
	
	/**
	 * 用户管理
	 */
	function article($p){
		$l = "select * from df_article where 1=1 ". $p ['name']." order by id desc limit ".$p['start'].",".$p['limit'];
		$t = "select count(id) total from df_article where 1=1 ". $p ['name'];
		$r['list'] = $this->db->query($l)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	/*
	 * 法律援助
	 */
	function getLawHelp($p){
		$l = "select * from df_help_detail where 1=1 ". $p ['name']." order by id desc limit ".$p['start'].",".$p['limit'];
		$t = "select count(id) total from df_help_detail where 1=1 ". $p ['name'];
		$r['list'] = $this->db->query($l)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	/*
	 * 法律信息
	 */
	function helpInfo($p){
		$sql = ' select d.*,type1,type2,type3,type4,type5,type6,type7 from df_help_detail d inner join df_help_type t on d.type=t.id where d.id= '.$p['id'];
		return $this->db->query($sql)->result_array($sql);
	}
	function helpedit($p){
		$s = ' update df_help_detail set `is_help`='.$p['is_help'].'  where id = '. $p['id'];
		return $this->db->query($s);
	}
	function articleInfo($p){
		return $this->db->get_where('df_article',array('id'=>$p['id']),1,0)->result_array();
	}
	function getHelpLawType(){
		$sql = ' select id,name from df_helplaw where is_show=1 ';
		$result = $this->db->query($sql);
		$arr = $result->fetchAll('id');
		return $arr;
	}
	function articleedit($p){
		if(!empty($p['ucode'])){
			$sign = $p['is_show']==1?'+':'-';
		 $s = 'update df_lawyer set `remark` = `remark`'.$sign.'1  where ucode = "'. $p['ucode'].'"';
			$this->db->query($s);
		}
		unset($p['ucode']);
		return $this->db->update('df_article',$p,array('id'=>$p['id']));
		
	}
	
	/**
	 * 用户管理
	 */
	function report($p){
		$l = "select * from df_report  order by id desc limit ".$p['start'].",".$p['limit'];
		$t = "select count(id) total from df_report";
		$r['list'] = $this->db->query($l)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	function reportInfo($p){
		return $this->db->get_where('df_report',array('id'=>$p['id']),1,0)->result_array();
	}
	
	function lawyeredit($p){
		return $this->db->update('df_lawyer',$p,array('id'=>$p['id']));
	}
	
	
	/**
	 * 新闻培训 列表
	 */
	function newtrain($p){
		$s = 'select * from df_newtrain where 1=1'.$p['type'].$p['name'].' order by id desc limit '.$p['start'].','.$p['limit'];
		$t = 'select count(*) total  from df_newtrain where 1=1 '.$p['type'].$p['name'];
		$r['list'] = $this->db->query($s)->result_array();
		$r['total'] = $this->db->query($t)->result_array();
		return $r;
	}
	
	/**
	 * 添加新闻培训
	 */
	function newtrainadd($p){
		return $this->db->insert('df_newtrain',$p);
	}
	
	/**
	 * 新闻培训
	 */
	function newtraininfo($id){
		$s = "select * from df_newtrain where id='{$id}'";
		return $this->db->query($s)->result_array();
	}
	
	/**
	 * 修改新闻培训
	 */
	function newtrainedit($p){
		return $this->db->update('df_newtrain',$p,array('id'=>$p['id']));
	}
	
} 