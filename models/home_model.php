<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 class home_model extends CI_Model{
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
 	 * ucode更新
 	*/
 	public function updateUcodeTable($table,$ucode,$type_arr){
 		$this->db->where('ucode', $ucode);
 		$this->db->update($table, $type_arr);
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
 	function getUserByucode($id){
 		 $r = $this->db->get_where('df_member',array('ucode'=>$id),1,0)->result_array();
 		 return $r[0];
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
 		$con .= ' order by createtime desc ';
 		if($page){
 			$nu = ($page-1)*$perpage;
 			$con.=' limit '.$nu.','.$perpage;
 		}
 		$sql = ' select id,title,group_id,createtime,is_solve from df_question '.$con;
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
 		$con .= ' order by createtime desc ';
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
 	 * 获得某个案件的信息
 	 */
 	public function getCaseInfo($id){
 		$sql = ' select name,is_audit from df_case where id='.$id;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 获得某个案件的投标律师
 	 */
 	public function getCaseLawyer($id){
 		$sql = ' select b.id,b.status,b.message,l.icon,l.ucode, l.name,l.region,l.secre,l.rerritory from df_bid b inner join df_lawyer l on b.ucode=l.ucode where b.tid='.$id.' and b.is_show=1 ';
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 验证表df_bid表id
 	 */
 	public function verifyBid($id){
 		$sql = ' select id from df_bid where is_show=1 and  id='.$id;
 		$result = $this->db->query($sql);
 		$arr = $result->row_array();
 		return $arr;
 	}
 	/*
 	 * 获得地区
 	 */
 	public function getRegion(){
 		$sql = ' select * from df_region ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('uuid');
 		return $arr;
 	}
 	/*
 	 * 验证案件是否中标
 	 */
 	public function verifyBidStatus($id){
 		$sql = ' select * from df_bid where is_show=1 and status=1 and tid= '.$id;
 		$result = $this->db->query($sql);
 		$arr = $result->result_array();
 		return $arr;
 	}
 	/*
 	 * 获得擅长领域
 	 */
 	public function getTerritory(){
 		$sql = ' select * from df_territory where is_show =1  ';
 		$result = $this->db->query($sql);
 		$arr = $result->fetchAll('id');
 		return $arr;
 	}
 }
