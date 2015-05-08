<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 * 法律法规
 * @author chenxiao
 * @date 2014/09/09
 */
class law extends base {
	private $jdata = array();
	private $limit = 20;
	private $everid ='1,957,1143,5463';
	function __construct() {
		parent::__construct ();
		$this->jdata["success"] = 0;
		$this->jdata["message"] = "";
		$this->load->model ( "law_model",'m_l' );
		$this->load->library('pagination');

	}
	
	function index(){
		$this->output->cache(1440);
		$data = array();
		$data = $this->data;
		$data['lawconfig'] =eval(ART_CATEGORY);
		$data['newlaw'] = $this->newlaw(4);//最新颁布
		$data['everydayLaw'] = $this->m_l->everydayLaw($this->everid);//日常
		$data['list_one'] = $this->newlaw(4,1);//'1' => '宪法法律',
		$data['list_two'] = $this->newlaw(4,3);//'3' => '司法解释',
		$data['list_three'] = $this->newlaw(4,2);//'2' => '行政法规',
		$data['list_four'] = $this->newlaw(4,4);//'4' => '部门规章',
		$data['list_five'] = $this->newlaw(4,8);//'8' => '地方法规',
		$this->load->view('default/law',$data);
	}
/* 	'1' => '宪法法律',
· 中华人民共和国物权法 957
· 中华人民共和国消费者权益保护法 1
· 中华人民共和国劳动合同法实施条例 5463
· 中华人民共和国民事诉讼法 1143

*/
	
	/**
	 * 最新
	 */
	function newlaw($l,$type=0){
		$type = empty($type)?'':' and effect_level= '.$type;
		return $this->m_l->newlaw($l,$type);
	}
	
	/**
	 * 法律法规意见
	 */
	function suggest(){
		$p=array();
		$p = $this->input->post();
		$p['name'] = htmlspecialchars(urldecode($p['name'])) ;
		$p['ucode'] = $this->getUserLoginId(true);
		$r = $this->m_l->sdd_suggest($p);
		if(!empty($r)){
			$this->jdata['success'] = 1;
			$this->json_out($this->jdata);
		}
	}
	
	/**
	 * 法律法规列表
	 */
	function lawlist(){
		$data = array();
		$data = $this->data;
		$p = array();
		$p = $this->get_params();
		$get = $p;
		//var_dump($get);exit;
		$tit = $p['name'];
		$data['par'] =$p;
		$p ['page'] = ! empty (  $p ['page'] ) ? intval($p ['page']) : 1;
		$p['name'] = empty($p['name'])?'':' and name like "%'.$p['name'].'%" ';
		//$old  = empty($p['name'])?'':' where title like "%'.$p['name'].'%" ';
		$p['range'] = '';
		//$p['name'] = empty($p['name'])?'':' and name like "%'.$p['name'].'%"';range
		$p['promulgation'] = empty($p['promulgation'])?'':' and promulgation ="'.$p['promulgation'].'" ';
		$p['time_effect'] = empty($p['time_effect'])?'':' and time_effect ="'.$p['time_effect'].'" ';
		$p['effect_level'] = empty($p['effect_level'])?'':' and effect_level ="'.$p['effect_level'].'" ';
		$p['ps_date'] = empty($p['ps_date'])?'':' and effective_date >"'.$p['ps_date'].'" ';
		$p['pe_date'] = empty($p['pe_date'])?'':' and effective_date <"'.$p['pe_date'].'" ';
		$p['es_date'] = empty($p['es_date'])?'':' and effective_date >"'.$p['es_date'].'" ';
		$p['ee_date'] = empty($p['ee_date'])?'':' and effective_date <"'.$p['ee_date'].'" ';
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$re = $this->m_l->lawlist($p);
		$data['list'] = $re['list'];
		$url = '';
		if(!empty($get)){
			unset($get['page']);
			foreach($get as $k=>$v){
				if(!empty($v) && $v !==0 && $v!=''){
					$url .= $k.'='.$v.'&';
				}
			}
		}
		
		$data['total'] = $re['total'][0]['total'];
		$config = $this->page_config($re['total'][0]['total'],$this->limit,STATIC_URL.'law/lawlist?'.$url);
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
		$data['lawconfig'] =eval(ART_CATEGORY);
		
		
		$this->load->model ( "laywer_model",'laywer' );
		$data['territory'] = $this->laywer->getTerritory();
		//$data['pan'] = true;
		
		$data['panlaw'] = true;
		$data['panlawyer'] = true;
		
		if(isset($get['name'])&&!empty($get['name'])){
			$data['law'] = $this->m_l->getJudgement($get['name']);
			
			if(!empty($data['law'])){
				$data['panlaw'] = false;
			}else{
				$data['law'] = $this->m_l->getJudgement();
			}
			//$data['pan'] = false;
			
			$data['tit'] = $get['name'];
			$data['laywer_id']=$this->m_l->getJudgementlawyers($get['name']);
			
			if(empty($data['lawyer_id'])){
				//$data['pan'] = true;
				$data['lawyer'] = $this->m_l->getLawyers();
				
			}else{
				
				$data['panlawyer'] = false;
				
				$tem = '(';
				foreach($data['lawyer_id'] as $k=>$v){
					$tem .= '"'.$v['laywer_id'].'",';
				}
				$tem .= ' "1111" )';
				$data['lawyer'] = $this->m_l->getLawyers($tem);
			}
			//var_dump($data['lawyer']);exit;
		}else{
			//echo 22;exit;
			$data['lawyer'] = $this->m_l->getLawyers($tit);
  			$data['law'] = $this->m_l->getJudgement($tit);
		}
		
		//var_dump($data['lawyer']);exit;
		$this->load->view('default/lawlist',$data);
	}
	
	/**
	 * 法律法规
	 */
	function lawinfo(){
		$data = array();
		$data = $this->data;
		$id=$this->input->get('id');
		$data['info'] = $this->m_l->lawinfo($id);
		$data['newlaw'] = $this->newlaw(4);//最新颁布
		$data['everydayLaw'] = $this->m_l->everydayLaw($this->everid);//日常
		$data['lawconfig'] =eval(ART_CATEGORY);
		$this->load->view('default/lawinfo',$data);
	}
	
}