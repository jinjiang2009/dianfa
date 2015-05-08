<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 * 专利研究报告
 * @author chenxiao
 * @date 2014/09/26
 */
class research extends base {
	private $jdata = array();
	
	function __construct() {
		parent::__construct ();
		$this->jdata["success"] = 0;
		$this->jdata["message"] = "";
		$this->load->model ( "enterprise_model",'m_e' );
	}
	
	function index(){
		//$this->output->cache(1440);
		$data = array();
		$data = $this->data;
		$type = $this->input->get('type');
		$data ['list'] = $this->m_e->getResearch($type);
		$data['package'][0] = $data ['list'][0];
		$data['package'][1] = $data ['list'][1];
		unset($data ['list'][1]);
		unset($data ['list'][0]);
		$this->load->view('default/research.php',$data);
	}
	
	/**
	 * 产品详情页
	 */
	function product(){
		$id = $this->input->get('id');
		$id = intval($id);
		$data=array();
		$data = $this->data;
		$data ['info'] = $this->m_e->product($id);
		$this->load->view('default/product',$data);
	}
	
	
	/**
	 * 订单添加  
	 */
	public function add_order(){
		$data = $this->data;
		//防止重复提交订单
		session_start();
		$_SESSION['ver'] = $data['ver'] = rand(0,999999);
		if($data['login_type']==4){
	  		header_output('请先登录');die;
	  	}
		$params = $this->get_params();
		if(empty($data['user'])){
			$data['prompt']='未登录请登录再购买';
			$this->load->view('default/prompt',$data);
			return false;
		}
		$data['cor_id'] = empty($params['cor_id'])?0:intval($params['cor_id']);
		$data['type'] = 3;
		$r = $this->m_e->product($data['cor_id']);
		if(empty($r)){
			$data['prompt']='未找到相关产品';
			$this->load->view('default/prompt',$data);
			return false;
		}
		$money = $this->m_e->getCor($data['cor_id']);
		if(preg_match_all("/\d+/i", $money['unit_price'], $matches)){
			if(strstr($money['unit_price'],';')){
				$data['moneytype'] = 2;
				preg_match_all("/\d+/i", $money['unit_price'], $matches);
				$data['moneyarr'] = $matches[0];
				$data['price'] = explode(';',$money['unit_price']);
			}else{
				$data['moneytype'] = 1;
				preg_match("/\d+/i", $money['unit_price'], $matches);
				$data['price'] = $matches[0];
			}
			$data['subject'] = $money['name'];
			$data['llld'] = 2;
			$this->load->view('default/order_confirm',$data);
			//header("location:".STATIC_URL.'order?subject='.$money['name'].'&id='.$re);
		}else{
			$data['moneytype'] = 1;
			$data['price'] = 0.00;
			$data['subject'] = $money['name'];
			$data['llld'] = 2;
			$this->load->view('default/order_confirm',$data);
			/*
			$data['tt'] = 2;
			$data['order_code'] = $this->c_order();
			$data['prompt']='产品需求添加成功，我们律师会在1小时内与你联系';
			$this->m_e->insertTable('df_order',array('type'=>$data['type'],'cor_id'=>$data['cor_id'],'moneytype'=>3,'order_code'=>$data['order_code'],'ucode'=>$data['user']['ucode'],));
			$this->load->view('default/prompt_ok',$data);
			return false;
			*/
		}
			
			
		
	}
	
	
		
}