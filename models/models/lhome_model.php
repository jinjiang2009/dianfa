<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 class lhome_model extends CI_Model{
 	function __construct(){
 		header("Content-type:text/html;charset=utf-8");
 		parent::__construct();
 		
 	}
 	/*
 	 * 更新
 	*/
 	public function updateTable($table,$id,$type_arr){
 		$this->db->where('id', $id);
 		$this->db->update($table, $type_arr);
 	}
 	/*
 	 * 更新da_lawyer
 	*/
 	public function updateLawyer($table,$id,$type_arr){
 		$this->db->where('ucode', $id);
 		$this->db->update($table, $type_arr);
 	}
 	
 	public function updateLfrim($table,$id,$type_arr){
 		$this->db->where('ucode',$id);
 		$this->db->update($table,$type_arr);
 	}
 	/*
 	 * 获得所有律师擅长领域
 	 */
 	public function getAllRerritory($id=null){
 		$con = '';
 		if($id){
 			$con .=' and pid<>0 ';
 		}else{
 			$con .=' and pid=0 ';
 		}
 		$sql = ' select * from df_territory where is_show=1  '.$con;
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 	/*
 	 * 获得所有律师擅长领域
 	*/
 	public function getTypeRerritory(){
 		$sql = ' select * from df_territory where is_show=1 and pid<>0 ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('pid|array|id');
 		return $arr;
 	}
 	/*
 	 * 获取律师职业照
 	 */
 	public function getLayerIcon($ucode){
 		$sql = ' select icon,lince_img from df_lawyer where ucode="'.$ucode.'"';
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 取律师回答数据
 	 */
 	public function getQuestionLog($id=null,$page=null,$perpage=null){
 		$con = ' where 1 ';
 		if($id){
 			$con .= ' and a.ucode= "'.$id.'"';
 		}
 		$con .= ' order by a.createtime desc ';
 		if($page){
 			$nu = ($page-1)*$perpage;
 			$con.=' limit '.$nu.','.$perpage;
 		}
 		$sql = ' select a.id,a.content,a.createtime,q.id as qqid,q.title,q.group_id from df_answer a inner join df_question q on a.qid=q.id '.$con;
 		
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 取律师回答数据
 	*/
 	public function getCaseLog($id=null,$page=null,$perpage=null){
 		$con = ' where  b.is_show=1 ';
 		if($id){
 			$con .= ' and b.ucode= "'.$id.'"';
 		}
 		$con .= ' order by btime desc ';
 		if($page){
 			$nu = ($page-1)*$perpage;
 			$con.=' limit '.$nu.','.$perpage;
 		}
 		$sql = ' select a.bid,a.pricetype,a.name,a.createtime,a.deadline_date,b.createtime as btime,b.status from df_bid b inner join df_case a  on a.id=b.tid '.$con;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得某个用户的具体信息
 	 */
 	public function getUserById($id){
 		$sql = ' select * from df_member where id= '.$id;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 验证密码
 	 */
 	public function verifyPwd($pwd,$id){
 		$sql = ' select id from df_member where id='.$id.' and pwd="'.md5($pwd).'"';
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 *获得积分日志
 	 */
 	public function getIntegralLog($id=null,$page=null,$perpage=null){
 		$con = ' where 1 ';
 		if($id){
 			$con .= ' and ucode= "'.$id.'"';
 		}
 		$con .= ' order by createtime desc ';
 		if($page){
 			$nu = ($page-1)*$perpage;
 			$con.=' limit '.$nu.','.$perpage;
 		}
 		$sql = ' select * from df_integral_log '.$con;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 		
 	}
 	/*
 	 *获得咨询
 	*/
 	public function getAskLog($id=null,$page=null,$perpage=null){
 		$con = ' where 1 ';
 		if($id){
 			$con .= ' and uid= "'.$id.'"';
 		}
 		if($page){
 			$nu = ($page-1)*$perpage;
 			$con.=' limit '.$nu.','.$perpage;
 		}
 		$sql = ' select id,title,group_id,createtime from df_question '.$con;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	
 	}
 	/*
 	 *获得系统消息
 	*/
 	public function getMessageLog($id=null,$page=null,$perpage=null){
 		$con = ' where 1 ';
 		if($id){
 			$con .= ' and ucode= "'.$id.'"';
 		}
 		if($page){
 			$nu = ($page-1)*$perpage;
 			$con.=' limit '.$nu.','.$perpage;
 		}
 		$sql = ' select * from df_message '.$con;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	
 	}
 	/*
 	 * 获得某条消息
 	 */
 	public function getMessageCon($id){
 		$sql = ' select * from df_message where id= '.$id;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 获得发布案件
 	 */
 	public function getJudgeLog($id=null,$page=null,$perpage=null){
 		$con = ' where 1 ';
 		if($id){
 			$con .= ' and ucode= "'.$id.'"';
 		}
 		$con .= ' order by createtime desc ';
 		if($page){
 			$nu = ($page-1)*$perpage;
 			$con.=' limit '.$nu.','.$perpage;
 		}
 		$sql = ' select id,name,createtime from df_case '.$con;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得文档
 	*/
 	public function getDocumentLog($id=null,$page=null,$perpage=null){
 		$con = ' where is_show=1 ';
 		if($id){
 			$con .= ' and ucode= "'.$id.'"';
 		}
 		$con .= ' order by createtime desc ';
 		if($page){
 			$nu = ($page-1)*$perpage;
 			$con.=' limit '.$nu.','.$perpage;
 		}
 		$sql = ' select id,name,type,createtime from df_contract '.$con;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得订单
 	*/
 	public function getOrderLog($id=null,$page=null,$perpage=null){
 		$con = ' where 1 ';
 		if($id){
 			$con .= ' and o.ucode= "'.$id.'"';
 		}
 		$con .= ' order by o.createtime desc ';
 		if($page){
 			$nu = ($page-1)*$perpage;
 			$con.=' limit '.$nu.','.$perpage;
 		}
 		$sql = ' select o.id,o.order_code,o.type,o.money,o.status,o.createtime,o.moneytype,c.name from df_order o left join df_privatelaw c on o.cor_id=c.id '.$con;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得律师信息
 	 */
 	public function getLawyer($ucode){
 		$sql = ' select * from df_lawyer where ucode="'.$ucode.'"';
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	
 	public function getLfrim($ucode){
 		$sql = 'select * from df_lfrim where ucode="'.$ucode.'"';
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 信息入库
 	*/
 	public function insertTable($table,$arr){
 		$this->db->insert($table,$arr);
 		return $this->db->insert_id();
 	}
 	/*
 	 * 更新加1
 	 */
 	public function addOne($table,$type,$ucode){
 		$sql = ' update '.$table.' set '.$type.'='.$type.'+1  where ucode="'.$ucode.'"';
 		$result = $this->db->query($sql);
 		
 	}
 	/*
 	 * 获得一级案由
 	 */
 	public function getCategory(){
 		$sql = ' select id,name from df_category where pid=0 ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	
 	}
 	/*
 	 * 更新加1
 	*/
 	public function addNum($table,$type,$ucode,$num){
 		$sql = ' update '.$table.' set '.$type.'='.$type.'+'.$num.' where ucode="'.$ucode.'"';
 		$result = $this->db->query($sql);
 	
 	}
 	/*
 	 * 获取文章的分类
 	 */
 	public function getArticleCategory($pid){
 		$sql = ' select id,name from df_article_category where pid= '.$pid;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
		/**
 	 * 更改协议文本
 	 * @param array $p
 	 */
 	public function protocol($p){
 		$ucode = $p['ucode'];
 		unset($p['code']);
 		return $this->db->update('df_lawyer',$p,array('ucode'=>$ucode));
 	}
 	
 	/**
 	 * 通过ucode 获取协议信息
 	 */
 	public function byUcodePotocol($p){
 		return  $this->db->get_where('df_lawyer',array('ucode'=>$p),1,0)->result_array();
 	}
 	
 	/**
 	 * 通过ucode 获取协议信息
 	 */
 	public function closecon($p){
 		$ucode = $p['ucode'];
 		unset($p['code']);
 		return $this->db->update('df_lawyer',$p,array('ucode'=>$ucode));
 	}
 }
