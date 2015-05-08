<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 * 合同、法律文书
 * @author chenxiao
 * @date 2014/09/09
 */
class contract extends base {
	private $jdata = array();
	private $limit = 20;
	function __construct() {
		parent::__construct ();
		$this->jdata["success"] = 0;
		$this->jdata["message"] = "";
		$this->load->model ( "contract_model",'m_c' );
		$this->load->model ( "home_model",'m_h' );
		$this->load->library('pagination');

	}
	
	function index(){
		$data = array();
		$data = $this->data;
		$p = array();
		$get = array();
		$p = $this->get_params();
		$p['type'] = isset($p['type'])&& $p['type']==1? 1:2;
		$p['name'] = !empty($p['name'])?urldecode($p['name']):'';
		$get=$p;
		$p ['page'] = ! empty (  $p ['page'] ) ? intval($p ['page']) : 1;
		$p['type'] = !empty($p['type'])?" and type='".intval($p['type'])."' ":''; 
		$p['tid'] = !empty($p['tid'])?" and tid='".intval($p['tid'])."' ":'';
		$p['name'] = !empty($p['name'])?" and search like '%".$p['name']."%'":'';
		$p ['limit'] = $this->limit;
		$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
		$data['type'] = $this->m_c->get_type();
		$re = $this->m_c->contractlist($p);
		$data['list'] = $re['list'];
		$data['ranking'] = $this->ranking();
		$url = '';
		if(!empty($get)){
			unset($get['page']);
			foreach($get as $k=>$v){
				if(!empty($v) && $v !=0 && $v!=''){
					$url .= $k.'='.$v.'&';
				}
			}
		}
		$data['params'] =$get;
		$data['params']['total'] = $re['total'][0]['total'];
		$config = $this->page_config($re['total'][0]['total'],$this->limit,STATIC_URL.'contract/index?'.$url);
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
		$data['lawyer'] = $this->recomand_lawyer(8,2);//推荐律师
		$data['rerritory'] = $this->m_ca->territory();//rerritory擅长领域
		$data['contract_price'] = $this->contract_price();
	    $this->load->view('default/contractlist',$data);
	}
	
	/**
	 * 合同范本排行榜
	 */
	function ranking(){
		return $this->m_c->ranking();
	}
	
	/**
	 * 下载生成合同
	 */
    function create(){
    	$data = $this->data;
    	if(empty($data['user'])){
    		$data['prompt']='未登录请登录后再下载';
    		$this->load->view('default/prompt',$data);
    		return false;
    	}
    	
    	$id = $this->input->get('id');
    	$id = !empty($id)?intval($id):0;
    	$info = $this->m_c->con_info($id);
    	if(empty($info)){
    		$data['prompt']='未找到相关合同或文书';
    		$this->load->view('default/prompt',$data);
    		return false;
    	}
    	$this->m_c->down_num($id);
    	$d = $this->m_h->getUserByucode($data['user']['ucode']);
    	$int = $this->integral_config(15);
    	//integral_change();
    	if($int['num']>$d['integral']){
    		$data['prompt']='你的相关积分不足';
    		$this->load->view('default/tip',$data);
    		return false;
    	}
    	$ic = array(
    			'ucode'=>$d['ucode'],
    			'cause'=>15,
    			'cor_id'=>$id,
    			'num'=>$int['num'],
    			'type'=>2,
    	);
    	$this->integral_change($ic);
    	$wordname = time().".doc";
    	$headert='<html xmlns:o="urn:schemas-microsoft-com:office:office"
    	xmlns:w="urn:schemas-microsoft-com:office:word"
    	xmlns="http://www.w3.org/tr/rec-html40">';
    	$footer="</html>";
    	$handle = fopen('php://output', 'w');
    	header ("Content-Disposition: attachment; filename=" .$wordname );
    	header ("Content-type: application/octet-stream");
    	if (!empty($wordname))
    		fputs ($handle, $headert.$info[0]['content'].$footer);
    	fclose($handle);
    }
    
    /**
     * 起草审核合同
     */
    function draft(){
    	$data=array();
    	//$this->getUserLoginId();
    	$data = $this->data;
    	$type = $this->input->get('type');
    	$data['type'] = $type == 2?2:1;
    	$data['contract_type'] = $this->m_c->get_type();
    	$data['contract_price'] = $this->contract_price();
    	$this->load->view('default/contractdraft',$data);
    	
    }
    
    /**
     * 添加起草审核合同
     */
    function adddraft(){
    	$data = $this->data;
    	if($data['login_type']==4){
    		header_output('请先登录');die;
    	}
    	$post = $this->input->post();
    	if(isset($post['pri'])&&isset($post['type'])&&isset($post['contract_type'])&&isset($post['their'])){
    		//防止重复提交订单
    		session_start();
    		$_SESSION['ver'] = $data['ver'] = rand(0,999999);
    		$data['conpri'] = $post['pri'];
    		$data['contype'] = $post['type'];
    		$data['concontract_type'] = $post['contract_type'];
    		$data['contheir'] = $post['their'];
    		$data['llld'] = 1;
    		$data['moneytype'] = 1;
    		$data['subject'] = '文书合同';
    		$data['price'] = $post['pri'];
    		$data['type'] = 1;
    		$this->load->view('default/order_confirm',$data);
    	}else{
    		$this->load->view('default/wrong',$data);
    	}
    }
    
    function contractinfo() {
    	$data = array();
    	$data = $this->data;
    	$p = $this->get_params();
    	$p['id'] =intval($p['id']); 
    	if(empty($p['id'])){
    		$this->load->view('default/wrong',$data);
    		return ;
    	}
    	$data['info'] = $this->m_c->getInfo($p);
    	$data['ranking'] = $this->ranking();
    	$this->load->view('default/contractinfo',$data);
    }
    /**
     * app下载生成合同
     */
    function download(){
        $data = $this->data;
        $id = $this->input->get('id');
        $id = !empty($id)?intval($id):0;
        $info = $this->m_c->con_info($id);
        if(empty($info)){
            $data['prompt']='未找到相关合同或文书';
            $this->load->view('default/prompt',$data);
            return false;
        }
        $this->m_c->down_num($id);
        $int = $this->integral_config(15);
        $wordname = time().".doc";
        $headert='<html xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:w="urn:schemas-microsoft-com:office:word"
        xmlns="http://www.w3.org/tr/rec-html40">';
        $footer="</html>";
        $handle = fopen('php://output', 'w');
        header ("Content-Disposition: attachment; filename=" .$wordname );
        header ("Content-type: application/octet-stream");
        if (!empty($wordname))
            fputs ($handle, $headert.$info[0]['content'].$footer);
        fclose($handle);
    }
}