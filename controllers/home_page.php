<?php
if (! defined ( 'BASEPATH' ))exit ( 'No direct script access allowed' );
/**
 * 首页
 * @author chenxiao
 * @date 2014/09/26
 */
class home_page extends base {
	private $jdata = array();
	function __construct() {
		parent::__construct ();
		$this->jdata["success"] = 0;
		$this->jdata["message"] = "";
		$this->load->model ( "contract_model",'m_c' );
	}	
	
	/**
	 * 主页
	 */
	function index(){
		$this->output->cache(1440);
		$data = array();
		$data = $this->data;
		$this->load->model ( "judgement_model",'judge' );
		$data['region'] = $this->judge->getRegion();
		$this->load->model ( "laywer_model",'laywer' );
	    $data['territory'] = $this->laywer->getTerritory(true);
		$data['alpha'] = alpha();
		$data['lawyer'] = $this->recomand_lawyer(6,3);//推荐律师
		$data['rerritory'] = $this->m_ca->territory();//rerritory擅长领域
		$data['category'] = $this->judge->getCategory();//案由
		$data['hot_issue'] = $this->m_c->recommend(array('type'=>4,'limit'=>5));//热点问题
		$data['law_read'] = $this->m_c->recommend(array('type'=>6,'limit'=>3));//法规解读
		$data['dynamic'] = $this->m_c->recommend(array('type'=>5,'limit'=>20));//动态公告
		$data['message'] = $this->m_c->message();
		$data['symbiosis'] = $this->m_c->recommend(array('type'=>8,'limit'=>50));//合作伙伴
		$data['youq'] = $this->m_c->recommend(array('type'=>12,'limit'=>50));//友情链接
		$data['product_ent'] = $this->m_c->recommend(array('type'=>9,'limit'=>5));//企业
		$data['product_prv'] = $this->m_c->recommend(array('type'=>10,'limit'=>5));//私人
		$data['research'] = $this->m_c->recommend(array('type'=>14,'limit'=>4));//研究报告
		$data['institution'] = $this->judge->getInstitution();//审理机构
		$data['amanuensis'] = $this->judge->getAmanuensis();//文书类型
		$data['process'] = array(1=>'初审',2=>'终审',3=>'再审');
		$data['list_one'] = $this->newlaw(1);//'1' => '宪法法律',
		$data['list_two'] = $this->newlaw(3);//'3' => '司法解释',
		$data['list_three'] = $this->newlaw(2);//'2' => '行政法规',
		$data['list_four'] = $this->newlaw(4);//'4' => '部门规章',
		$data['list_five'] = $this->newlaw(8);//'8' => '地方法规',
		//$data['recruit'] = $this->m_c->job(1);招聘
		//$data['wanted'] = $this->m_c->job(2);求职
		$data['v_one'] = $this->onlineCourse(1,14,2);//线上沙龙
		$data['v_two'] = $this->onlineCourse(2,3,2);//专家论证
		$data['v_three'] = $this->onlineCourse(1,6,1);//专家讲堂
		$data['c_one'] = $this->onlineCourse(3,14,2);//业务培训
		$data['c_two'] = $this->onlineCourse(4,5,1);//模拟法庭
		$data['c_three'] = $this->onlineCourse(5,5,1);//法律英语
		$data['type'] = $this->m_c->get_type();
		$data['contract_price'] = $this->contract_price();
		//获得法律文章
		$data['article'] = $this->m_c->getArticle();
		$data['arttype'] = $this->m_c->getArticleType();
		$this->load->view('default/home',$data);
		
		
	}
	
	/**
	 * 最新
	 */
	function newlaw($type=0){
		$this->load->model ( "law_model",'m_l' );
		$type = empty($type)?'':' and effect_level= '.$type;
		return $this->m_l->newlawtotal($type);
	}
	

	function ok(){
		$data = $this->data;
		$this->load->view('default/suc',$data);
	}
	function header(){
		$data = $this->data;
		$json = array('type'=>$data['login_type'],'identity'=>$data['user']['identity'],'username'=>$data['user']['username'],'messagenum'=>!empty($data['messagenum'])?$data['messagenum']:0);
		echo json_encode($json);
	}
	function wrong(){
		$data = $this->data;
		$json = array('type'=>$data['login_type'],'identity'=>$data['user']['identity'],'username'=>$data['user']['username'],'messagenum'=>!empty($data['messagenum'])?$data['messagenum']:0);
		echo json_encode($json);
	}
	
	/**
	 * 
	 * @param int $t 分类		
	 * @param int $l 数量
	 * @param int $c (1、视频2、新闻分类)
	 */
	function onlineCourse($t,$l,$c=1){
		return $this->m_c->onlineCourse($t,$l,$c);
	}
	/*
	 * 首页动态公告
	 */
	function dynamic(){
		$data = $this->data;
		//$get = $this->input->get();
		$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
		$perpage = 12;
		$this->load->library('pagination');
		$ttt = $this->m_c->getdynamicTotal();
		$data['total'] = $ttt['total'];
		$data['totalpage'] = ceil($data['total']/$perpage);
		$config = $this->page_config($data['total'],$perpage,STATIC_URL.'home_page/dynamic?');
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
		$data['dynamic'] = $this->m_c->getdynamic($data['current'],$perpage);;//动态公告	
		$data['ad'] = $this->m_c->getAd();
		
		$this->load->view('default/dynamic',$data);
	}
	/*
	 * 全部搜索
	 */
	function search($name=false){
		$name = urldecode($name);
		$data = $this->data;
		if($name&&!empty($name)){
			$data['title'] = $name;
			#获得案件
			$data['case'] = $this->m_c->getCase($name);
			if(!empty($data['case'])){
				$ids = join(',',array_keys($data['case']));
				$this->load->model ( "judgement_model",'judge' );
				$data['content'] =  $this->judge->getContent($ids);
		  		$data['court'] = $this->judge->getCourt();
			}
	  		#法规
	  		$data['law'] = $this->m_c->getLaw($name);
	  		#咨询
	  		$data['ask'] = $this->m_c->getQuestion($name);
	  		$data['group'] = $this->m_c->getAskGroup();
	  		#律师
	  		$data['lawyer'] = $this->m_c->getLawyer($name);
	  		$this->load->model ( "laywer_model",'laywer' );
	  		$data['territory'] = $this->laywer->getTerritory();
			$this->load->view('default/search',$data);
			
		}else{
			
			$this->load->view('default/wrong',$data);
		}
		
		
	}
	function popular(){
		$data = $this->data;
		//$get = $this->input->get();
		$data['current'] = $this->input->get('page',0)?$this->input->get('page',0):1;
		$perpage = 12;
		$this->load->library('pagination');
		$ttt = $this->m_c->getpopularTotal();
		$data['total'] = $ttt['total'];
		$data['totalpage'] = ceil($data['total']/$perpage);
		$config = $this->page_config($data['total'],$perpage,STATIC_URL.'home_page/popular?');
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
		$data['dynamic'] = $this->m_c->getmess($data['current'],$perpage);;//动态公告	
		
		
		$this->load->view('default/popular',$data);
	}
}