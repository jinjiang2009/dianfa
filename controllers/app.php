<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class app extends base{

	
  function __construct(){
    parent::__construct();
    $this->load->model ( "app_model",'app' );
   	$this->data['get'] = $this->input->get();
   	$me =  isset($this->data['get']['format']) ? $this->data['get']['format']:'Json';
  	$this->data['request'] = 'response'.$me;
  }
  /**
   * 咨询中心页
   */
  public function getFirstAskGroup(){
     $con =  $this->app->getGroup();
     $this->data['request'](200,'success',$con);
	}
  public function getSecondAskGroup(){
      $con =  $this->app->getGroup($this->data['get']['pid']);
      $this->data['request'](200,'success',$con);
  }
  function getFirstConGroup(){
		$this->data['request'] ('200','success',$this->app->get_type()) ;
	}
	function getConList(){
        $p = array();
        $p ['page'] =  isset ($this->data['get']['page'] ) ? intval($this->data['get']['page']) : 1;
        $p['type'] = isset( $this->data['get']['ctype']) ? intval( $this->data['get']['ctype']):2;
        $p['type'] = " and type= ".$p['type'];
        $p['tid'] = isset( $this->data['get']['tid'])?" and tid='".intval( $this->data['get']['tid'])."' ":'';
        $p['name'] = isset( $this->data['get']['name'])?" and search like '%". $this->data['get']['name']."%'":'';
        $p ['limit'] = isset($this->data['get']['limit']) ? $this->data['get']['limit']:10 ;
        $p ['start'] = ($p ['page'] - 1) * $p ['limit'];
        $type = $this->app->get_type();
        $con = $this->app->contractlist($p);
        
        foreach($con as $k=>$v){
        	$con[$k]['groupname'] = $type[$v['tid']]['name'];
        	$con[$k]['link'] = 'http://www.idianfa.com/contract/download?id='.$v['id'];
        }
        $this->data['request'](200,'success',$con);
    }
    function getLawGroup(){
    	$arr = eval(ART_CATEGORY);
    	$temp = array();
    	foreach($arr['law_cat']['child_category'] as $k=>$v){
    		$temp['law_cat'][$k]['id'] = $k;
    		$temp['law_cat'][$k]['name'] = $v;
    	}
    	foreach($arr['promulgation_department']['child_category'] as $k=>$v){
    		$temp['promulgation'][$k]['id'] = $k;
    		$temp['promulgation'][$k]['name'] = $v;
    	}
    	foreach($arr['effect_level']['child_category'] as $k=>$v){
    		$temp['effect_level'][$k]['id'] = $k;
    		$temp['effect_level'][$k]['name'] = $v;
    	}
    	$this->data['request'] ('200','success',$temp);
    }
    
    function getLawConById(){
    	if(isset($this->data['get']['id']))
    	$this->data['request'] ('200','success',$this->app->getLawById($this->data['get']['id']));
    	else
    	$this->data['request'] ('401','参数错误','');	
    }
    function getLawList(){
    	$p = array();
    	$arr = eval(ART_CATEGORY);
        $p ['page'] =  isset ($this->data['get']['page'] ) ? intval($this->data['get']['page']) : 1;
        $p['catid'] = isset( $this->data['get']['catid'])?" and cat='".intval( $this->data['get']['catid'])."' ":'';
        $p['promulgationid'] = isset( $this->data['get']['promulgationid'])?" and promulgation='".intval( $this->data['get']['promulgationid'])."' ":'';
        $p['effect_levelid'] = isset( $this->data['get']['effect_levelid'])?" and effect_level='".intval( $this->data['get']['effect_levelid'])."' ":'';
        $p['name'] = isset( $this->data['get']['name'])?" and name like '%". $this->data['get']['name']."%'":'';
        $p ['limit'] = isset($this->data['get']['limit']) ? $this->data['get']['limit']:10 ;
        $p ['start'] = ($p ['page'] - 1) * $p ['limit'];
        $rs = $this->app->getLawList($p);
        foreach($rs as $k=>$v){
        	$rs[$k]['promulgation'] = $arr['promulgation_department']['child_category'][$rs[$k]['promulgation']];
        	$rs[$k]['effect_level'] = $arr['effect_level']['child_category'][$rs[$k]['effect_level']];
        }
       
        $this->data['request'](200,'success',$rs);
    }
    function getThirdGroup(){
    	$arr = eval(ART_CATEGORY);
    	$tem = array();
    	$tem['privatelawyer'] = array_slice($arr['category']['child_category'],1,5);
    	$tem['enterprise'] = array_slice($arr['category']['child_category'],6,12);
    	$tem['research'] = '';
    	$this->data['request'] ('200','success',$tem);
    
    }
    function getSedList(){
    	$p = array();
    	$p ['page'] =  isset ($this->data['get']['page'] ) ? intval($this->data['get']['page']) : 1;
    	$p['type'] = isset( $this->data['get']['type'])?" and infotype='".$this->data['get']['type']."' ":'';
    	$p['name'] = isset( $this->data['get']['name'])?" and name like '%". $this->data['get']['name']."%'":'';  	
    	$p ['limit'] = isset($this->data['get']['limit']) ? $this->data['get']['limit']:10 ;
    	$p ['start'] = ($p ['page'] - 1) * $p ['limit'];
    	$zl = $this->app->privatelawlist($p);
    	foreach($zl as $k=>$v ){
    		if(strpos($v['unit_price'],';')){
    			$zl[$k]['pricearray'] = explode(';',$v['unit_price']);
    		}
    	}
    	
    	$this->data['request'](200,'success',$zl);
    	
    }
    
}
