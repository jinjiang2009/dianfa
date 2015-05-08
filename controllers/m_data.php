<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 *
 * @author chenxiao
 * @date 2014/08/29
 * @desc 后台管理
 */
class m_data extends m_base {
	private $jdata = array();
	private $limit =20;
	private $params =array();
	function __construct() {
		parent::__construct ();
		$this->judgeUser();//用户登陆
		$this->judgeAuthority();
		$this->params = $this->get_params();
		$this->jdata["success"] = 0;
		$this->jdata["message"] = "";
		$this->load->library('Lib_page');
		$this->load->model ( "m_data_model",'m_d' );
		$this->load->model ( "m_config_model",'m_c' );
		$this->load->model ( "m_web_model",'m_w' );
	}
	

	
	/**
	 * 主页
	 */
	function index(){
		
		$data = array();
		$data['left'] = $this->getLeftMenu();
		$this->load->view('manage/left',$data);
	}
	
	
	/**
	 * 分类管理
	 */
	function df_type(){
		$p = array();
		$p = $this->get_params();
		$pa = $p;
		$pa ['type'] = empty ( $pa ['type'] ) ? '' :$pa ['type'];
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['type'] = empty ( $p ['type'] ) ? '' : ' and type='.$p ['type'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_d->typelist($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/df_type?type='.$pa ['type'].'&name='.$pa ['name'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/typelist',$data);
	}
	
	/**
	 * 添加分类
	 */
	function typeadd(){
		$p = $this->get_params();
		$p['createid'] = $this->getMLId();
		if($_POST){
			$r = $this->m_d->typeadd($p);
			if($r)
				$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
			$this->load->view('manage/typeadd');
		}
	}
	
	/**
	 * 编辑分类
	 */
	function typeedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['page'] = $this->input->get('page');
			$data['info'] = $this->m_d->typeinfo($id);
			$this->load->view('manage/typeadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_d->typeedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 分类显示或隐藏
	 */
	function typeshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->typeedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	/**
	 * 合同文书/法律文书
	 */
	function contract(){
		$p = $this->get_params();
		$pa = $p;
		$pa ['type'] = empty ( $pa ['type'] ) ? '' :$pa ['type'];
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];//+"&tid="+request('tid');
		$pa ['tid'] = empty ( $pa ['tid'] ) ? '' :$pa ['tid'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['type'] = empty ( $p ['type'] ) ? '' : ' and type='.$p ['type'];
		$p ['tid'] = empty ( $p ['tid'] ) ? '' : ' and tid='.$p ['tid'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_d->contractlist($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$data['type'] = $this->m_d->getType(true);
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/contract?type='.$pa ['type'].'&name='.$pa ['name'].'&tid='.$pa ['tid'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/contractlist',$data);
	}
	
	/**
	 * 添加 合同文书/法律文书
	 */
	function contractadd(){
		$p = $this->get_params();
		if($_POST){
			$p['createid'] = $this->getMLId();
			$p['owner'] = 1;
			$p['content'] =urldecode($p['content']);
			$p['name'] =urldecode($p['name']);
			$r = $this->m_d->contractadd($p);
			if($r)
		$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
			$data['type'] = $this->m_d->getType(true);
			$this->load->view('manage/contractadd',$data);
		}
	}
	
	/**
	 * 编辑 合同文书/法律文书
	 */
	function contractedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['page'] = $this->input->get('page');
			$data['info'] = $this->m_d->contractinfo($id);
			$data['type'] = $this->m_d->getType(true);
			$this->load->view('manage/contractadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			$p['content'] =urldecode($p['content']);
			$p['name'] =urldecode($p['name']);
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_d->contractedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 合同文书/法律文书 显示或隐藏
	 */
	function contractshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->contractedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	/**
	 * 合同文书/法律文书 审核
	 */
	function contractaudit(){
		$p=array();
		$p = $this->get_params();
		$p['is_audit'] = $p['is_audit']==1?2:1;
		$p['auditid'] = $this->getMLId();
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->contractedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	/**
	 * 判决文书
	 */
	function judgement(){
		$p = $this->get_params();
		$pa = $p;
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and title like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_d->judgementlist($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$data['category'] = $this->m_c->categoryshow();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/judgement?type='.'&name='.$pa ['name'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/judgementlist',$data);
	}
	
	/**
	 * 添加 判决文书
	 */
	function judgementadd(){
		$p = $this->get_params();
		if($_POST){
			$p['createid'] = $this->getMLId();
			$p['owner'] = 1;
			$p['content'] =urldecode($p['content']);
			$p['title'] =urldecode($p['title']);
			$p['lawer'] =urldecode($p['lawer']);
			$p['search'] =urldecode($p['search']);
			$r = $this->m_d->judgementadd($p);
			if($r)
				$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
			$data['categorys'] = $this->m_c->categorys();
			$data['region'] = $this->m_c->region();
			$this->load->view('manage/judgementadd',$data);
		}
	}
	
	/**
	 * 判决文书 编辑
	 */
	function judgementedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['info'] = $this->m_d->judgementinfo($id);
			$data['region'] = $this->m_c->region();
			if(!empty($data['info'][0]['region'] ))
				$data['city'] = $this->m_c->byuuidName($data['info'][0]['region']);
			if(!empty($data['info'][0]['category'] ))
				$data['category'] = $this->m_c->byidcategory($data['info'][0]['category']);
			if(!empty($data['info'][0]['court'] ))
				$data['court'] = $this->m_c->byidcourt($data['info'][0]['court']);
			$data['categorys'] = $this->m_c->categorys();
			$this->load->view('manage/judgementadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			$p['content'] =urldecode($p['content']);
			$p['title'] =urldecode($p['title']);
			$p['lawer'] =urldecode($p['lawer']);
			$p['search'] =urldecode($p['search']);
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_d->judgementedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 判决文书 显示或隐藏
	 */
	function judgementshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->judgementedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	/**
	 * 产品
	 */
	function product(){
		$p = array();
		$p = $this->get_params();
		$pa = $p;
		$pa ['type'] = empty ( $pa ['type'] ) ? '' :$pa ['type'];
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['type'] = empty ( $p ['type'] ) ? '' : ' and type='.$p ['type'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_d->product($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/product?type='.$pa ['type'].'&name='.$pa ['name'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/productlist',$data);
	}
	
	/**
	 * 添加产品
	 */
	function productadd(){
		$p = $this->get_params();
		$p['createid'] = $this->getMLId();
		if($_POST){
			$p['name'] =urldecode($p['name']);
			$p['content'] =urldecode($p['content']);
			$p['price'] =urldecode($p['price']);
			$p['service'] =urldecode($p['service']);
			$r = $this->m_d->productadd($p);
			if($r)
				$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
			$data =array();
			$data['infotype'] = eval(PRODUCT);
			$this->load->view('manage/productadd',$data);
		}
	}
	
	/**
	 * 编辑产品
	 */
	function productedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['page'] = $this->input->get('page');
			$data['info'] = $this->m_d->productinfo($id);
			$data['infotype']= eval(PRODUCT);
			$this->load->view('manage/productadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			$p['name'] =urldecode($p['name']);
			$p['content'] =urldecode($p['content']);
			$p['price'] =urldecode($p['price']);
			$p['service'] =urldecode($p['service']);
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_d->productedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 产品显示或隐藏
	 */
	function productshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->productedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	/**
	 * 指导案例
	 */
	function guide_case(){
		$p = $this->get_params();


		$pa = $p;
		$pa ['type'] = empty ( $pa ['type'] ) ? '' :$pa ['type'];
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['type'] = empty ( $p ['type'] ) ? '' : ' and type='.$p ['type'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';

		$r = $this->m_d->guide_caselist($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/guide_case?type='.$pa ['type'].'&name='.$pa ['name'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/guide_caselist',$data);
	}
	
	/**
	 * 添加 指导案例
	 */
	function guide_caseadd(){
		$p = $this->get_params();
		if($_POST){
			$p['createid'] = $this->getMLId();
			$p['content'] =urldecode($p['content']);
			$p['name'] =urldecode($p['name']);
			$r = $this->m_d->guide_caseadd($p);
			if($r)
				$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
			$data['type'] = $this->m_d->getType(true);
			$this->load->view('manage/guide_caseadd',$data);
		}
	}
	
	/**
	 * 编辑 指导案例
	 */
	function guide_caseedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['page'] = $this->input->get('page');
			$data['info'] = $this->m_d->guide_caseinfo($id);
			$data['type'] = $this->m_d->getType(true);
			$this->load->view('manage/guide_caseadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			$p['content'] =urldecode($p['content']);
			$p['name'] =urldecode($p['name']);
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_d->guide_caseedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 指导案例 显示或隐藏
	 */
	function guide_caseshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->guide_caseedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	/**
	 * 法律法规
	 */
	function law(){
		$p = $this->get_params();
	
	//+"&time_effect="+time_effect+"&cat="+cat+"&promulgation="+promulgation+"&effect_level="+effect_level;
		$pa = $p;
		$pa ['time_effect'] = empty ( $pa ['time_effect'] ) ? '' :$pa ['time_effect'];
		$pa ['cat'] = empty ( $pa ['cat'] ) ? '' :$pa ['cat'];
		$pa ['promulgation'] = empty ( $pa ['promulgation'] ) ? '' :$pa ['promulgation'];
		$pa ['effect_level'] = empty ( $pa ['effect_level'] ) ? '' :$pa ['effect_level'];
		
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['time_effect'] = empty ( $p ['time_effect'] ) ? '' : ' and time_effect='.$p ['time_effect'];
		$p ['cat'] = empty ( $p ['cat'] ) ? '' : ' and cat='.$p ['cat'];
		$p ['promulgation'] = empty ( $p ['promulgation'] ) ? '' : ' and promulgation='.$p ['promulgation'];
		$p ['effect_level'] = empty ( $p ['effect_level'] ) ? '' : ' and effect_level='.$p ['effect_level'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
	
		$r = $this->m_d->lawlist($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$data['lawconfig'] =eval(ART_CATEGORY);
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/law?time_effect='.$pa ['time_effect'].'&effect_level='.$pa ['effect_level'].'&cat='.$pa ['cat'].'&promulgation='.$pa ['promulgation'].'&name='.$pa ['name'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/lawlist',$data);
	}
	
	/**
	 * 添加 法律法规
	 */
	function lawadd(){
		$p = $this->get_params();
		if($_POST){
			$p['createid'] = $this->getMLId();
			$p['content'] =urldecode($p['content']);
			$p['name'] =urldecode($p['name']);
			$p['law_subtitle'] =urldecode($p['law_subtitle']);
			$p['law_introtitle'] =urldecode($p['law_introtitle']);
			$p['catalog'] =urldecode($p['catalog']);
			$p['order_content'] =urldecode($p['order_content']);
			$r = $this->m_d->lawadd($p);
			if($r)
				$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
			$data['type'] = $this->m_d->getType(true);
			$data['lawconfig'] =eval(ART_CATEGORY);
			$this->load->view('manage/lawadd',$data);
		}
	}
	
	/**
	 * 编辑 法律法规
	 */
	function lawedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['page'] = $this->input->get('page');
			$data['info'] = $this->m_d->lawinfo($id);
			$data['type'] = $this->m_d->getType(true);
			$data['lawconfig'] =eval(ART_CATEGORY);

			$this->load->view('manage/lawadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			$p['content'] =urldecode($p['content']);
			$p['name'] =urldecode($p['name']);
			$p['law_subtitle'] =urldecode($p['law_subtitle']);
			$p['law_introtitle'] =urldecode($p['law_introtitle']);
			$p['catalog'] =urldecode($p['catalog']);
			$p['order_content'] =urldecode($p['order_content']);
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_d->lawedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 法律法规 显示或隐藏
	 */
	function lawshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->lawedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	/**
	 * 案件招标
	 */
	function case_bidlist(){
		$p = array();
		$p = $this->get_params();
		$pa = $p;
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_d->case_bidlist($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/case_bidlist?'.'name='.$pa ['name'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/case_bidlist',$data);
	}
	
	/**
	 *  案件招标 显示或隐藏
	 */
	function case_bidshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->case_bidedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	/**
	 * 案件招标 审核
	 */
	function case_bidaudit(){
		$p=array();
		$p = $this->get_params();
		$p['is_audit'] = $p['is_audit']==1?2:1;
		$p['auditid'] = $this->getMLId();
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->case_bidedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	/**
	 * 案例详情
	 */
	function caseinfo(){
		$id = intval($this->input->get('id'));
		if($id){
			$data = array();
			$data['info'] = $this->m_d->caseInfo($this->input->get('id'));
			//$data['contract_type'] = $this->m_d->get_type();
			$data['user'] = $this->getUsers();
			//需求者先关信息
			if(!empty($data['info'][0]['ucode'])){
				$data['member'] = $this->m_d->membInfo($data['info'][0]['ucode']);
			}
			$data['type'] = $this->get_type();
			$this->load->view('manage/caseinfo',$data);
		}else{
			$p = array();
			$p = $_POST;
			$p['auditid'] = $this->getMLId();
			//$p['auditid'] = date('Y-m-d h:i:s',time());
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_d->caseedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 合同意见
	 */
	function contract_suggest(){
		$p = array();
		$p = $this->get_params();
		$pa = $p;
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_d->contract_suggest($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/contract_suggest?'.'name='.$pa ['name'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/contract_suggestlist',$data);
	}
	
	/**
	 * 合同意见处理
	 */
	function contract_suggest_worked(){
		$p=array();
		$p = $this->get_params();
		$p['worked'] = $p['worked']==1?2:1;
		$p['worked_id'] = $this->getMLId();
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->contract_suggestedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	/**
	 * 法律法规意见
	 */
	function law_suggest(){
		$p = array();
		$p = $this->get_params();
		$pa = $p;
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_d->law_suggest($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$data['lawconfig'] =eval(ART_CATEGORY);
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/law_suggest?'.'name='.$pa ['name'],
		);
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/law_suggestlist',$data);
	}
	
	/**
	 * 合同意见处理
	 */
	function law_suggest_worked(){
		$p=array();
		$p = $this->get_params();
		$p['worked'] = $p['worked']==1?2:1;
		$p['worked_id'] = $this->getMLId();
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->law_suggestedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	
	
	/**
	 * 合同需求
	 */
	function contract_need(){
		$p = array();
		$p = $this->get_params();
		$pa = $p;
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and mobile = "'.urldecode($p ['name']).'" ';
		$p ['order_code'] = empty ( $p ['order_code'] ) ? '' : ' and order_code = "'.$p ['order_code'].'" ';
		$r = $this->m_d->contract_need($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/contract_need?'.'name='.$pa ['name'],
		);
		
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/contract_need',$data);
	}
	
 	/**
	 * 合同需求详情
	 */
	function con_need_info(){
		$id = intval($this->input->get('id'));
		if($id){
		$data = array();
		$data['info'] = $this->m_d->prodInfo($this->input->get('id'));
		$data['contract_type'] = $this->m_d->get_type();
		$data['user'] = $this->getUsers();
		//需求者先关信息
		if(!empty($data['info'][0]['ucode'])){
			$data['member'] = $this->m_d->membInfo($data['info'][0]['ucode']);
		}
		//相关产品的信息
 		if(!empty($data['info'][0]['cor_id'])){
			$data['corinfo'] = $this->m_d->corinfo($data['info'][0]['cor_id']);
		} 
		$this->load->view('manage/prodinfo',$data);
		}else{
			$p = array();
			$p = $_POST;
			$p['worked_id'] = $this->getMLId();
			$p['worked_time'] = date('Y-m-d h:i:s',time());
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_d->orderedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
		
	} 
	
	/**
	 * 产品需求
	 */
	function product_need(){
		$p = array();
		$p = $this->get_params();
		$pa = $p;
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and mobile = "'.urldecode($p ['name']).'"';
		$p ['order_code'] = empty ( $p ['order_code'] ) ? '' : ' and order_code = "'.$p ['order_code'].'" ';
		$r = $this->m_d->product_need($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/product_need?'.'name='.$pa ['name'],
		);
		
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/product_need',$data);
	}
	
	/**
	 * 用户
	 */
	function memeber(){
		$p = array();
		$p = $this->get_params();
		$pa = $p;
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and account like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_d->memeber($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/memeber?'.'name='.$pa ['name'],
		);
		
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/memberlist',$data);
	}
	
	/**
	 * 用户详情操作
	 */
	function memeberopt(){
		$p = array();
		$p = $this->get_params();
		$code = $p['ucode'];
		$data=array();
		$data['info'] = $this->m_d->lawyerInfo($code);
		$data['member'] = $this->m_d->memberInfo($code);
		$this->load->view('manage/lawyerinfo',$data);
	}
	
	/**
	 * 律师用户
	 */
	function lawer(){
		$p = array();
		$p = $this->get_params();
		$pa = $p;
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_d->lawyer($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/lawer?'.'name='.$pa ['name'],
		);
		
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/lawyerlist',$data);
	}
	
	function lawyeropt(){
		$p = array();
		$p = $this->get_params();
		if(empty($p['id'])){
			$p = array();
			$p = $this->get_params();
			$code = $p['ucode'];
			$data=array();
			$data['info'] = $this->m_d->lawyerInfo($code);
			$data['member'] = $this->m_d->memberInfo($code);
			$this->load->view('manage/lawyerinfo',$data);
		}else{
			if($p['status']==2){
				$p['protocol']='';
				$p['status']=0;
			}
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_d->lawyeredit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	
	/**
	 * 文章
	 */
	function article(){
	    $p = array();
		$p = $this->get_params();
		$pa = $p;
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and title like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_d->article($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/article?name='.$pa ['name'],
		);
		
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/articlelist',$data);
	}
	
	
	/**
	 * 文章详情
	 */
	function articleInfo(){
		$data = array();
	$p = $this->get_params();
	 $data['info'] = $this->m_d->articleInfo($p);
	 $this->load->view('manage/articleinfo',$data);
	}
	
	/**
	 * 文章显示隐藏
	 */
	function articleshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->articleedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	
	/**
	 * 文章
	 */
	function report(){
		$p = array();
		$p = $this->get_params();
		$pa = $p;
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and title like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_d->report($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/report?name='.$pa ['name'],
		);
	
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/reportlist',$data);
	}
	
	
	function reportInfo(){
		$data = array();
		$p = $this->get_params();
		$data['info'] = $this->m_d->reportInfo($p);
		$this->load->view('manage/reportinfo',$data);
	}
	
	/**
	 * 视频
	 */
	function video(){
		$p = array();
		$params=array();
		$params = $this->get_params();
		$p = $params;
		$params ['type'] = empty ( $params ['type'] ) ? '' :$params ['type'];
		$params ['name'] = empty ( $params ['name'] ) ? '' :$params ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['type'] = empty ( $p ['type'] ) ? '' : ' and type='.$p ['type'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
		$r = $this->m_w->video($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$data['type'] = array(
				'1'=>'专家讲堂',
				'2'=>'模拟法庭',
				'3'=>'法律英语',
				'4'=>'实习律师',
				'5'=>'司法考试'
				);
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/video?type='.$params ['type'].'&name='.$params ['name'],
		);
		$data['their'] = $this->their();
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/videolist',$data);
	}
	
	/**
	 * 添加视频
	 */
	function videoadd(){
		$p = $this->get_params();
		$p['createid'] = $this->getMLId();
		if($_POST){
			$p['url'] = urldecode($p['url']);
			$r = $this->m_w->videoadd($p);
			if($r)
				$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
			$data['type'] = array(
				'1'=>'专家讲堂',
				'2'=>'模拟法庭',
				'3'=>'法律英语',
				'4'=>'实习律师',
				'5'=>'司法考试'
				);
			$data['their'] = $this->their();
			$this->load->view('manage/videoadd',$data);
		}
	}
	
	/**
	 * 编辑视频
	 */
	function videoedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['page'] = $this->input->get('page');
			$data['info'] = $this->m_w->videoinfo($id);
			$data['type'] = array(
				'1'=>'专家讲堂',
				'2'=>'模拟法庭',
				'3'=>'法律英语',
				);
			$data['their'] = $this->their();
			$this->load->view('manage/videoadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$p['url'] = urldecode($p['url']);
			$_r2 = $this->m_w->videoedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 显示视频
	 */
	function videoshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_w->videoedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	
	/**
	 * 新闻培训
	 */
	function newtrain(){
		$p = $this->get_params();
		$pa = $p;
		$pa ['type'] = empty ( $pa ['type'] ) ? '' :$pa ['type'];
		$pa ['name'] = empty ( $pa ['name'] ) ? '' :$pa ['name'];
		$p ['page'] = ! empty ( $p ['page'] ) ? intval($p ['page']) : 1;
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$p ['type'] = empty ( $p ['type'] ) ? '' : ' and type='.$p ['type'];
		$p ['name'] = empty ( $p ['name'] ) ? '' : ' and name like "%'.urldecode($p ['name']).'%"';
	
		$r = $this->m_d->newtrain($p);
		$data=array();
		$data['user'] = $this->getUsers();
		$data['list']=$r['list'];
		$data['opt'] = $this->getOptAut();
		$_arvg=array(
				'limit'=>$this->limit,
				'total'=>$r['total'][0]['total'],
				'nowpage'=>$p ['page'],
				'page'=>'page',
				'url'=>'/m_data/newtrain?type='.$pa ['type'].'&name='.$pa ['name'],
		);
		$data['type'] = array(
				'1'=>'线上沙龙',
				'2'=>'专家论证',
				'3'=>'业务培训',
				//1线上沙龙2专家论证3业务培训
		);
		$data['their'] = $this->newtraintheir();
		$data['page']=$this->lib_page->page($_arvg);
		$this->load->view('manage/newtrainlist',$data);
	}
	
	/**
	 * 添加新闻培训
	 */
	function newtrainadd(){
		$p = $this->get_params();
		if($_POST){
			$p['createid'] = $this->getMLId();
			$p['content'] =urldecode($p['content']);
			$p['name'] =urldecode($p['name']);
			$r = $this->m_d->newtrainadd($p);
			if($r)
				$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}else{
		$data['type'] = array(
				'1'=>'线上沙龙',
				'2'=>'专家论证',
				'3'=>'业务培训',
		);
		$data['their'] = $this->newtraintheir();
			$this->load->view('manage/newtrainadd',$data);
		}
	}
	
	/**
	 * 编辑 新闻培训
	 */
	function newtrainedit(){
		$id = intval($this->input->get('id'));
		if($id){
			$data['page'] = $this->input->get('page');
			$data['info'] = $this->m_d->newtraininfo($id);
		$data['type'] = array(
				'1'=>'线上沙龙',
				'2'=>'专家论证',
				'3'=>'业务培训',
		);
		$data['their'] = $this->newtraintheir();
			$this->load->view('manage/newtrainadd',$data);
		}else{
			$p = array();
			$p = $_POST;
			$p['content'] =urldecode($p['content']);
			$p['name'] =urldecode($p['name']);
			if(empty($p['id'])){
				$this->jdata["success"] = 2;
				$this->json_out($this->jdata);
			}
			$_r2 = $this->m_d->newtrainedit($p);
			if(!empty($_r2)){//修改OK
				$this->jdata["success"] = 1;
				$this->json_out($this->jdata);
			}
		}
	}
	
	/**
	 * 新闻培训 显示或隐藏
	 */
	function newtrainshow(){
		$p=array();
		$p = $this->get_params();
		$p['is_show'] = $p['is_show']==1?2:1;
		if(empty($p)){
			$this->json_out($this->jdata);
		}
		$_r2 = $this->m_d->newtrainedit($p);
		if(!empty($_r2)){//修改OK
			$this->jdata["success"] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	
	function newtraintheir(){
		//民事案件、刑事案件、行政案件、知识产权案件、实习律师培训、律师实务培训、业务指引、高院审判指南、司法考试、考研指导
		return explode('、','请选择、民事案件、刑事案件、行政案件、知识产权案件、实习律师培训、律师实务培训、业务指引、高院审判指南、司法考试、考研指导');
		//法学理论、宪法行政法、刑法、民商法、经济法、诉讼法（民诉、刑诉）、国际法、司法制度
		//实习律师培训、律师实务培训、业务指引、高院审判指南、司法考试、考研指导
	}
	
	function their(){
		return explode('、', '请选择、法学理论、宪法行政法、刑法、民商法、经济法、诉讼法、国际法、司法制度、民事案件、刑事案件、行政案件、知识产权案件、海商案件、法律术语、口语练习、考试指导');
	}
}