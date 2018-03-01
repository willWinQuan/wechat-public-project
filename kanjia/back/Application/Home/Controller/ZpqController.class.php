<?php
namespace Home\Controller;
session_start();
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET,OPTIONS');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header('Content-type:text/html;charset=utf-8');
//use GuzzleHttp\json_decode;
#ini_set('date.timezone','Asia/Shanghai');
class ZpqController extends \Think\Controller{

	// public function _empty(){
	// 	$this->display("Public:404");
	// }

	public $user_id;
	public $user_id_en;
	public $customer_id;
	public $customer_id_en;
	public $http;

	public function _initialize(){
		$data = I('request.','','trim');
		$customer_id = $data['customer_id_en'];
		$this->customer_id = is_numeric($customer_id)? $customer_id:decode_wsy($customer_id);
		$this->customer_id_en = encode_wsy($this->customer_id);
		$this->user_id = isset($data['user_id_en'])? decode_wsy($data['user_id_en']):'';
		$this->user_id_en = encode_wsy($this->user_id);
		$_SESSION['customer_id'] = $this->customer_id;
		$this->http = 'https://'.$_SERVER['HTTP_HOST'];
	}

	/*获取activity排行榜*/
	public function get_activity_ranking_list(){
		$map_action['activity_id']=I("get.activity_id");
		$map_action['customer_id']=$this->customer_id;
		$map_action['isvalid']=1;
		$temp=M("action")->field("id")->where($map_action)->select();
		for ($i=0; $i < count($temp); $i++) { 
			$temp[$i]=$temp[$i]['id'];
		}
		$map_bargain['action_id']=array("IN",$temp);
		$map_bargain['customer_id']=$this->customer_id;
		$map_bargain['isvalid']=1;
		$result=M("bargain")->field('user_id,bargain_time,sum(bargain_price)')->where($map_bargain)->group('user_id')->order("sum(bargain_price) desc,bargain_time desc")->limit(10)->select();
		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				if($key=="sum(bargain_price)"){
					$result[$i]['bargain_price']=$result[$i]['sum(bargain_price)'];
				}
				if($key=="user_id"){
					$where['id']=$value;
					$user=M()->table(DB_NAME.".weixin_users")->where($where)->find();
					$result[$i]['user_pic']=$user['weixin_headimgurl'];
					$result[$i]['user_name']=$user['weixin_name'];
				}
			}
		}
		if($result){
			$invoke['data']=$result;
			$invoke['user']=$this->user_id;
			$this->ajaxReturn($invoke);
		}
	}

	/*获取activity排行榜我的排名*/
	public function get_activity_ranking_list_by_me(){
		$map_action['activity_id']=I("get.activity_id");
		$map_action['customer_id']=$this->customer_id;
		$map_action['isvalid']=1;
		$temp=M("action")->field("id")->where($map_action)->select();
		for ($i=0; $i < count($temp); $i++) { 
			$temp[$i]=$temp[$i]['id'];
		}

		$map_bargain['action_id']=array("IN",$temp);
		$map_bargain['customer_id']=$this->customer_id;
		$map_bargain['isvalid']=1;
		$result=M("bargain")->field('id,user_id,action_id,product_id,bargain_time,sum(bargain_price)')->where($map_bargain)->group('user_id')->order("sum(bargain_price) desc,bargain_time desc")->limit(10)->select();
		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				if($key=="user_id"){
					if($value==$this->user_id){
						$where['id']=$value;
						$user=M()->table(DB_NAME.".weixin_users")->where($where)->find();
						$invoke[]=array(
							'id'=>$result[$i]['id'],
							'action_id'=>$result[$i]['action_id'],
							'product_id'=>$result[$i]['product_id'],
							'user_id'=>$result[$i]['user_id'],
							'bargain_price'=>$result[$i]['sum(bargain_price)'],
							'bargain_time'=>$result[$i]['bargain_time'],
							'user_pic'=>$user['weixin_headimgurl'],
							'user_name'=>$user['weixin_name'],
							'number'=>$i+1,
							);
					}
				}
			}
		}
	    $this->ajaxReturn($invoke);
	}

	/*获取action排行榜*/
	public function get_action_ranking_list(){
		$map_bargain['action_id']=I("get.action_id");
		$map_bargain['customer_id']=$this->customer_id;
		$map_bargain['isvalid']=1;
		$result=M("bargain")->field('user_id,bargain_time,sum(bargain_price)')->where($map_bargain)->group('user_id')->order("sum(bargain_price) desc,bargain_time desc")->limit(10)->select();
		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				if($key=="sum(bargain_price)"){
					$result[$i]['bargain_price']=$result[$i]['sum(bargain_price)'];
				}
				if($key=="user_id"){
					$where['id']=$value;
					$user=M()->table(DB_NAME.".weixin_users")->where($where)->find();
					$result[$i]['user_pic']=$user['weixin_headimgurl'];
					$result[$i]['user_name']=$user['weixin_name'];
				}
			}
		}
		$invoke['data']=$result;
		$invoke['user']=$this->user_id;
		$this->ajaxReturn($invoke);
	}

	/*获取action时间榜*/
	public function get_bargain_time_list(){
		$map_bargain['action_id']=I("get.action_id");
		$map_bargain['customer_id']=$this->customer_id;
		$map_bargain['isvalid']=1;
		$result=M("bargain")->field("id,action_id,product_id,user_id,bargain_price,bargain_time")->where($map_bargain)->order("bargain_time desc")->limit(3)->select();
		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				if($key=="user_id"){
					$where['id']=$value;
					$user=M()->table(DB_NAME.".weixin_users")->where($where)->find();
					$result[$i]['user_pic']=$user['weixin_headimgurl'];
					$result[$i]['user_name']=$user['weixin_name'];
				}
			}
		}
		$invoke['data']=$result;
		$invoke['user']=$this->user_id;
		$this->ajaxReturn($invoke);
	}

	/*获取action排行榜我的排名*/
	public function get_action_ranking_list_by_me(){
		$map_bargain['action_id']=I("get.action_id");
		$map_bargain['customer_id']=$this->customer_id;
		$map_bargain['isvalid']=1;
		$result=M("bargain")->field('id,user_id,action_id,product_id,bargain_time,sum(bargain_price)')->where($map_bargain)->group('user_id')->order("sum(bargain_price) desc,bargain_time desc")->limit(10)->select();
		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				if($key=="user_id"){
					if($value==$this->user_id){
						$where['id']=$value;
						$user=M()->table(DB_NAME.".weixin_users")->where($where)->find();
						$invoke[]=array(
							'id'=>$result[$i]['id'],
							'action_id'=>$result[$i]['action_id'],
							'product_id'=>$result[$i]['product_id'],
							'user_id'=>$result[$i]['user_id'],
							'bargain_price'=>$result[$i]['sum(bargain_price)'],
							'bargain_time'=>$result[$i]['bargain_time'],
							'user_pic'=>$user['weixin_headimgurl'],
							'user_name'=>$user['weixin_name'],
							'number'=>$i+1,
							);
					}
				}
			}
		}
		// $map_bargain['action_id']=I("get.action_id");
		// $map_bargain['customer_id']=$this->customer_id;
		// $map_bargain['isvalid']=1;
		// $result=M("bargain")->field("id,action_id,product_id,user_id,bargain_price,bargain_time")->where($map_bargain)->order("bargain_price desc,bargain_time desc")->select();
		// $count=count($result);
		// $invoke = array();
		// for($i=0;$i<$count;$i++){
		// 	foreach ($result[$i] as $key => $value) {
		// 		if($key=="user_id"){
		// 			if($value==$this->user_id){
		// 				$where['id']=$value;
		// 				$user=M()->table(DB_NAME.".weixin_users")->where($where)->find();
		// 				$invoke[]=array(
		// 					'id'=>$result[$i]['id'],
		// 					'action_id'=>$result[$i]['action_id'],
		// 					'product_id'=>$result[$i]['product_id'],
		// 					'user_id'=>$result[$i]['user_id'],
		// 					'bargain_price'=>$result[$i]['bargain_price'],
		// 					'bargain_time'=>$result[$i]['bargain_time'],
		// 					'user_pic'=>$user['weixin_headimgurl'],
		// 					'user_name'=>$user['weixin_name'],
		// 					'number'=>$i+1,
		// 					);
		// 			}
		// 		}
		// 	}
		// }
		// foreach ($invoke as $key => $row){
		// 	$bargain_price[$key] = $row['bargain_price'];
		// 	$id[$key]  = $row['id'];
		// 	$action_id[$key]  = $row['action_id'];
		// 	$product_id[$key]  = $row['product_id'];
		// 	$user_id[$key]  = $row['user_id'];
		// 	$bargain_time[$key]  = $row['bargain_time'];
		// 	$user_pic[$key]  = $row['user_pic'];
	 //        $user_name[$key]  = $row['user_name'];
	 //        $number[$key]  = $row['number'];
	 //    }
	 //    $res = $this->arraySort($invoke,'bargain_price','desc');
	 //    for($i=0;$i<1;$i++){
	 //    	$result=$res[$i];
	 //    }
	    $this->ajaxReturn($invoke);
	}

	function arraySort($arr, $keys, $type = 'asc') {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v){
            $keysvalue[$k] = $v[$keys];
        }
        $type == 'asc' ? asort($keysvalue) : arsort($keysvalue);
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
           $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }

    //点击量
    public function count(){
    	$where['customer_id']=$this->customer_id;
    	$where['product_id']=I("get.product_id");
    	$check=M("click")->where($where)->find();
    	if($check){
    		$save['click_num']=$check['click_num']+1;
    		M("click")->where($where)->save($save);
    	}else{
    		$add['product_id']=I("get.product_id");
    		$add['click_num']=1;
    		$add['forwarding_num']=0;
    		$add['isvalid']=1;
    		$add['customer_id']=$this->customer_id;
    		M("click")->add($add);
    	}
    }

    //转发量
    public function forward(){
    	$where['customer_id']=$this->customer_id;
    	$where['activity_id']=I("get.activity_id");
    	$where['product_id']=I("get.product_id");
    	$check=M("forward")->where($where)->find();
    	if($check){
    		$save['forwarding_num']=$check['forwarding_num']+1;
    		M("forward")->where($where)->save($save);
    	}else{
    		$where['forwarding_num']=1;
    		$where['isvalid']=1;
    		M("forward")->add($where);
    	}
    }

    public function apply(){
    	$activity_id=I("get.activity_id");
    	$result1=$this->applying_ff($this->user_id,$this->customer_id,$activity_id);
    	$result2=$this->apply_buy_ff($this->user_id,$this->customer_id,$activity_id);
		$result3=$this->apply_ed_ff($this->user_id,$this->customer_id,$activity_id);
		$temp1=array();
		$temp2=array();
		$temp3=array();
    	if($result1){
    		$temp1=$result1;
    	}
    	if($result2){
    		$temp2=$result2;
    	}
    	if($result3){
    		$temp3=$result3;
    	}
    	$result=array_merge($temp1,$temp2,$temp3);
    	$invoke['data']=$result;
		$invoke['user']=$this->user_id;
		$this->ajaxReturn($invoke);
    }

    public function apply_buy(){
    	$activity_id=I("get.activity_id");
	    $invoke['data']=$this->apply_buy_ff($this->user_id,$this->customer_id,$activity_id);
		$invoke['user']=$this->user_id;
		$this->ajaxReturn($invoke);
    }

    public function apply_buy_ff($user_id,$customer_id,$activity_id){
    	$map_order['user_id']=$user_id;
		$map_order['is_pay']=1;
		$map_order['isvalid']=1;
		$map_order['customer_id']=$customer_id;
		$map_order['activity_id']=$activity_id;
		$result=M("order")->where($map_order)->select();
		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				if($key=="product_id"){
					$find_goods['id']=$value;
					$result_goods=M("goods")->where($find_goods)->find();
					$find_commonshop['id']=$result_goods['product_no'];
					$find_commonshop['id']=$result_goods['product_no'];
					$result_commonshop=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_commonshop)->find();
					$result[$i]['product_name']=$result_commonshop['name'];
					$result[$i]['product_pic']=$result_commonshop['default_imgurl'];
					$result[$i]['min_price']=(float)$result_goods['minimum_price'];
					$result[$i]['orgin_price']=(float)$result_goods['price'];
					$result[$i]['buy_price']=(float)$result_goods['buy_price'];
					$result[$i]['product_id']=$result_goods['id'];
					$result[$i]['apply_id']=$result[$i]['action_id'];
					$map_action['id']=$result[$i]['action_id'];
					$action=M("action")->where($map_action)->find();
					$result[$i]['latest_price']=$action['latest_price'];
				}
				$result[$i]['status']="已购买";
			}
		}
		return $result;
    }

    public function applying(){
    	$activity_id=I("get.activity_id");
    	$result=$this->applying_ff($this->user_id,$this->customer_id,$activity_id);
	    $invoke['data']=$result;
		$invoke['user']=$this->user_id;
		$this->ajaxReturn($invoke);
    }

    public function applying_ff($user_id,$customer_id,$activity_id){
    	$map_order['user_id']=$user_id;
		$map_order['is_pay']=1;
		$map_order['isvalid']=1;
		$map_order['customer_id']=$customer_id;
		$map_order['activity_id']=$activity_id;
		$temp=M("order")->field("action_id")->where($map_order)->select();
		if($temp){
			for ($m=0; $m < count($temp); $m++) {
				$temp[$m]=$temp[$m]['action_id'];
			}
			$map_action['id']=array("NOT IN",$temp);
		}
		$map_activity['isvalid']=1;
		$map_activity['id']=$activity_id;
		$map_activity['customer_id']=$customer_id;
		$temp2=M("activity")->where($map_activity)->find();
		// for ($m=0; $m < count($temp2); $m++) {
		// 	$temp2[$m]=$temp2[$m]['id'];
		// }
		$map_action['user_id']=$user_id;
		//$map_action['activity_id']=array("IN",$temp2);
		$map_action['activity_id']=$activity_id;
		$map_action['isvalid']=1;
		$result=M("action")->where($map_action)->select();
		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				if($key=="product_id"){
					$find_goods['id']=$value;
					$result_goods=M("goods")->where($find_goods)->find();
					if($result_goods['end_time']>date("Y-m-d H:i:s")){
						$result1[$i]['timeing']=(strtotime($result_goods['end_time'])-time())*1000;
						$find_commonshop['id']=$result_goods['product_no'];
						$result_commonshop=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_commonshop)->find();
						$result1[$i]['product_name']=$result_commonshop['name'];
						$result1[$i]['product_pic']=$result_commonshop['default_imgurl'];
						$result1[$i]['min_price']=(float)$result_goods['minimum_price'];
						$result1[$i]['orgin_price']=(float)$result_goods['price'];
						$result1[$i]['buy_price']=(float)$result_goods['buy_price'];
						if($temp2['activity_status']==3){
							$result1[$i]['status']="已过期";
						}else if($temp2['activity_status']==4){
							$result1[$i]['status']="已终止";
						}else{
							$result1[$i]['status']="进行中";
						}
						$result1[$i]['product_id']=$result_goods['id'];
						$result1[$i]['apply_id']=$result[$i]['id'];
						$result1[$i]['latest_price']=$result[$i]['latest_price'];
					}	
				}
			}
		}
		return $result1;
    }

    public function apply_ed(){
    	$activity_id=I("get.activity_id");
    	$result=$this->apply_ed_ff($this->user_id,$this->customer_id,$activity_id);
	    $invoke['data']=$result;
		$invoke['user']=$this->user_id;
		$this->ajaxReturn($invoke);
    }

    public function apply_ed_ff($user_id,$customer_id,$activity_id){
    	$map_order['user_id']=$user_id;
		$map_order['is_pay']=1;
		$map_order['isvalid']=1;
		$map_order['customer_id']=$customer_id;
		$map_order['activity_id']=$activity_id;
		$temp=M("order")->field("action_id")->where($map_order)->select();
		if($temp){
			for ($m=0; $m < count($temp); $m++) {
				$temp[$m]=$temp[$m]['action_id'];
			}
			$map_action['id']=array("NOT IN",$temp);
		}
		// $map_activity['isvalid']=1;
		// $map_activity['activity_status']=array("IN","3,4");
		// $map_activity['customer_id']=$customer_id;
		// $temp2=M("activity")->field("id")->where($map_activity)->select();
		// for ($m=0; $m < count($temp2); $m++) {
		// 	$temp2[$m]=$temp2[$m]['id'];
		// }
		$map_action['user_id']=$user_id;
		//$map_action['activity_id']=array("IN",$temp2);
		$map_action['activity_id']=$activity_id;
		$map_action['isvalid']=1;
		$result=M("action")->where($map_action)->select();
		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				if($key=="product_id"){
					$find_goods['id']=$value;
					$result_goods=M("goods")->where($find_goods)->find();
					if($result_goods['end_time']<date("Y-m-d H:i:s")){
						$find_commonshop['id']=$result_goods['product_no'];
						$result_commonshop=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_commonshop)->find();
						$result1[$i]['product_name']=$result_commonshop['name'];
						$result1[$i]['product_pic']=$result_commonshop['default_imgurl'];
						$result1[$i]['min_price']=(float)$result_goods['minimum_price'];
						$result1[$i]['orgin_price']=(float)$result_goods['price'];
						$result1[$i]['buy_price']=(float)$result_goods['buy_price'];
						$result1[$i]['status']="已过期";
						$result1[$i]['product_id']=$result_goods['id'];
						$result1[$i]['apply_id']=$result[$i]['id'];
						$result1[$i]['latest_price']=$result[$i]['latest_price'];
					}
				}
			}
		}
		return $result1;
    }

    public function join(){
    	$activity_id=I("get.activity_id");
    	$result1=$this->joining_ff($this->user_id,$this->customer_id,$activity_id);
    	$result2=$this->join_buy_ff($this->user_id,$this->customer_id,$activity_id);
		$result3=$this->join_ed_ff($this->user_id,$this->customer_id,$activity_id);
    	$temp1=array();
		$temp2=array();
		$temp3=array();
    	if($result1){
    		$temp1=$result1;
    	}
    	if($result2){
    		$temp2=$result2;
    	}
    	if($result3){
    		$temp3=$result3;
    	}
    	$result=array_merge($temp1,$temp2,$temp3);
	    $invoke['data']=$result;
		$invoke['user']=$this->user_id;
		$this->ajaxReturn($invoke);
    }

    public function joining(){
    	$activity_id=I("get.activity_id");
	    $invoke['data']=$this->joining_ff($this->user_id,$this->customer_id,$activity_id);
		$invoke['user']=$this->user_id;
		$this->ajaxReturn($invoke);
    }

    public function joining_ff($user_id,$customer_id,$activity_id,$activity_id){
    	$map_action['activity_id']=$activity_id;
    	$map_action['customer_id']=$customer_id;
    	$map_action['isvalid']=1;
    	$action=M("action")->where($map_action)->select();
    	for ($i=0; $i < count($action); $i++) { 
    		$action[$i]=$action[$i]['id'];
    	}

    	$map_activity['isvalid']=1;
		$map_activity['id']=$activity_id;
		$map_activity['customer_id']=$customer_id;
		$temp2=M("activity")->where($map_activity)->find();

    	$map_bargain['action_id']=array("IN",$action);
		$map_bargain['user_id']=$user_id;
		$map_bargain['customer_id']=$customer_id;
		$result=M("bargain")->field('action_id,product_id,bargain_time,sum(bargain_price)')->where($map_bargain)->group('action_id')->select();

		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				if($key=="product_id"){
					$map_order['action_id']=$result[$i]['action_id'];
					$map_order['is_pay']=1;
					$map_order['customer_id']=$customer_id;
					$order=M("order")->where($map_order)->find();
					if(!$order){
						$find_goods['id']=$value;
						$result_goods=M("goods")->where($find_goods)->find();
						if($result_goods['end_time']>date("Y-m-d H:i:s")){
							$result1[$i]['timeing']=(strtotime($result_goods['end_time'])-time())*1000;
							$find_commonshop['id']=$result_goods['product_no'];
							$result_commonshop=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_commonshop)->find();
							$result1[$i]['product_name']=$result_commonshop['name'];
							$result1[$i]['product_pic']=$result_commonshop['default_imgurl'];
							$result1[$i]['min_price']=(float)$result_goods['minimum_price'];
							$result1[$i]['orgin_price']=(float)$result_goods['price'];
							$result1[$i]['buy_price']=(float)$result_goods['buy_price'];
							if($temp2['activity_status']==3){
								$result1[$i]['status']="已过期";
							}else if($temp2['activity_status']==4){
								$result1[$i]['status']="已终止";
							}else{
								$result1[$i]['status']="进行中";
							}
							$map_action['id']=$result[$i]['action_id'];
							$action=M("action")->where($map_action)->find();
							$result1[$i]['activity_id']=$action['activity_id'];
							$map_weixin_users['id']=$action['user_id'];
							$user=M()->table(DB_NAME.".weixin_users")->where($map_weixin_users)->find();
							$result1[$i]['weixin_headimgurl']=$user['weixin_headimgurl'];
							$result1[$i]['weixin_name']=$user['weixin_name'];
							$data_bargain['action_id']=$result[$i]['id'];
							$data_bargain['user_id']=$result[$i]['user_id'];
							$result1[$i]['bargain_price']=$result[$i]['sum(bargain_price)'];
							$result1[$i]['product_id']=$result_goods['id'];
							$result1[$i]['apply_id']=$action['id'];
							$result1[$i]['latest_price']=$action['latest_price'];
						}
					}
				}
			}
		}
		return $result1;
    }

    public function join_buy(){
    	$activity_id=I("get.activity_id");
	    $invoke['data']=$this->join_buy_ff($this->user_id,$this->customer_id,$activity_id);
		$invoke['user']=$this->user_id;
		$this->ajaxReturn($invoke);
    }

    public function join_buy_ff($user_id,$customer_id,$activity_id,$activity_id){
    	$map_action['activity_id']=$activity_id;
    	$map_action['customer_id']=$customer_id;
    	$map_action['isvalid']=1;
    	$action=M("action")->where($map_action)->select();
    	for ($i=0; $i < count($action); $i++) { 
    		$action[$i]=$action[$i]['id'];
    	}

    	$map_bargain['action_id']=array("IN",$action);
		$map_bargain['user_id']=$user_id;
		$map_bargain['customer_id']=$customer_id;
		$result=M("bargain")->field('action_id,product_id,bargain_time,sum(bargain_price)')->where($map_bargain)->group('action_id')->select();

		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				if($key=="product_id"){
					$map_order['action_id']=$result[$i]['action_id'];
					$map_order['is_pay']=1;
					$map_order['customer_id']=$customer_id;
					$order=M("order")->where($map_order)->find();
					if($order){
						$find_goods['id']=$value;
						$result_goods=M("goods")->where($find_goods)->find();
						$result1[$i]['timeing']=(strtotime($result_goods['end_time'])-time())*1000;
						$find_commonshop['id']=$result_goods['product_no'];
						$result_commonshop=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_commonshop)->find();
						$result1[$i]['product_name']=$result_commonshop['name'];
						$result1[$i]['product_pic']=$result_commonshop['default_imgurl'];
						$result1[$i]['min_price']=(float)$result_goods['minimum_price'];
						$result1[$i]['orgin_price']=(float)$result_goods['price'];
						$result1[$i]['buy_price']=(float)$result_goods['buy_price'];
						$result1[$i]['status']="进行中";
						$map_action['id']=$result[$i]['action_id'];
						$action=M("action")->where($map_action)->find();
						$result1[$i]['activity_id']=$action['activity_id'];
						$map_weixin_users['id']=$action['user_id'];
						$user=M()->table(DB_NAME.".weixin_users")->where($map_weixin_users)->find();
						$result1[$i]['weixin_headimgurl']=$user['weixin_headimgurl'];
						$result1[$i]['weixin_name']=$user['weixin_name'];
						$data_bargain['action_id']=$result[$i]['id'];
						$data_bargain['user_id']=$result[$i]['user_id'];
						$result1[$i]['bargain_price']=$result[$i]['sum(bargain_price)'];
						$result1[$i]['product_id']=$result_goods['id'];
						$result1[$i]['apply_id']=$action['id'];
						$result1[$i]['latest_price']=$action['latest_price'];
					}
				}
			}
		}
		return $result1;
    }

    public function join_ed(){
    	$activity_id=I("get.activity_id");
	    $invoke['data']=$this->join_ed_ff($this->user_id,$this->customer_id,$activity_id);
		$invoke['user']=$this->user_id;
		$this->ajaxReturn($invoke);
    }

    public function join_ed_ff($user_id,$customer_id,$activity_id){
    	$map_action['activity_id']=$activity_id;
    	$map_action['customer_id']=$customer_id;
    	$map_action['isvalid']=1;
    	$action=M("action")->where($map_action)->select();
    	for ($i=0; $i < count($action); $i++) { 
    		$action[$i]=$action[$i]['id'];
    	}

    	$map_bargain['action_id']=array("IN",$action);
		$map_bargain['user_id']=$user_id;
		$map_bargain['customer_id']=$customer_id;
		$result=M("bargain")->field('action_id,product_id,bargain_time,sum(bargain_price)')->where($map_bargain)->group('action_id')->select();

		$count=count($result);
		for($i=0;$i<$count;$i++){
			foreach ($result[$i] as $key => $value) {
				if($key=="product_id"){
					$map_order['action_id']=$result[$i]['action_id'];
					$map_order['is_pay']=1;
					$map_order['customer_id']=$customer_id;
					$order=M("order")->where($map_order)->find();
					if(!$order){
						$find_goods['id']=$value;
						$result_goods=M("goods")->where($find_goods)->find();
						if($result_goods['end_time']<date("Y-m-d H:i:s")){
							$result1[$i]['timeing']=(strtotime($result_goods['end_time'])-time())*1000;
							$find_commonshop['id']=$result_goods['product_no'];
							$result_commonshop=M()->table(DB_NAME.".weixin_commonshop_products")->where($find_commonshop)->find();
							$result1[$i]['product_name']=$result_commonshop['name'];
							$result1[$i]['product_pic']=$result_commonshop['default_imgurl'];
							$result1[$i]['min_price']=(float)$result_goods['minimum_price'];
							$result1[$i]['orgin_price']=(float)$result_goods['price'];
							$result1[$i]['buy_price']=(float)$result_goods['buy_price'];
							$result1[$i]['status']="进行中";
							$map_action['id']=$result[$i]['action_id'];
							$action=M("action")->where($map_action)->find();
							$result1[$i]['activity_id']=$action['activity_id'];
							$map_weixin_users['id']=$action['user_id'];
							$user=M()->table(DB_NAME.".weixin_users")->where($map_weixin_users)->find();
							$result1[$i]['weixin_headimgurl']=$user['weixin_headimgurl'];
							$result1[$i]['weixin_name']=$user['weixin_name'];
							$data_bargain['action_id']=$result[$i]['id'];
							$data_bargain['user_id']=$result[$i]['user_id'];
							$result1[$i]['bargain_price']=$result[$i]['sum(bargain_price)'];
							$result1[$i]['product_id']=$result_goods['id'];
							$result1[$i]['apply_id']=$action['id'];
							$result1[$i]['latest_price']=$action['latest_price'];
						}
					}
				}
			}
		}
		return $result1;
    }

    public function order(){
    	echo DB_HOST."--".DB_NAME."--".DB_USER."--".DB_PWD."--".DB_PORT."--".DB_PREFIX;
    }

    public function get_product(){
    	$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
    }

    public function get_pro(){
    	$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$rs=$this->getList($customer_id);
		$k=-1;
        foreach ($rs as $key => $val) {
        	if($val['parent_name']==null){
        		$k++;
        		$arr[$k]['parent_name']=$val['name'];
        		$j=-1;
        	}
        	if($val['parent_name']!=null){
        		$j++;
        		$arr[$k]['child_name'][$j]=$val['name'];
        		$arr[$k]['value'][$j]=$val['id'];
        	}
        }
        $this->ajaxReturn($arr);
    }

    private function getList($customer_id,$pid=-1,&$result=array()){
        $where['customer_id']=$customer_id;
		$where['isvalid']=1;
		$where['parent_id']=$pid;
        $rs=M()->table(DB_NAME.".weixin_commonshop_pros")->where($where)->select();
        foreach ($rs as $key => $value) {
            $result[]=$value;
            $this->getList($customer_id,$value['id'],$result,$spac);    
        }
        return $result;
    }

    public function get_nowprice(){
    	if(I("get.customer_id_en")){

		}else{
			$invoke['error_code']=1003;
			$invoke['data']="缺少customer_id_en";
			$this->ajaxReturn($invoke);
		}
    	$customer_id_en=I("get.customer_id_en");
		$customer_id=decode_wsy(I("get.customer_id_en"));
		if(I("get.action_id")){
			$where1['id']=I("get.action_id");
			$bargain=M("action")->where($where1)->find();
		}else{
			$invoke['error_code']=1003;
			$invoke['data']="缺少类型";
			$this->ajaxReturn($invoke);
		}
		$order_map['action_id']=I("get.action_id");
		$order_map['isvalid']=1;
		$order_map['customer_id']=$customer_id;
		$res_order=M("order")->where($order_map)->find();
		if($res_order){
			if($res_order['is_pay']==0){
				$invoke['error_code']=1004;
				$invoke['data']="已领取，请支付";
				//https://admin.weisanyun.cn/weixinpl/mshop/orderlist.php?customer_id=DDkGNlU3U2k=&currtype=2
				$invoke['href']=$this->http.'/weixinpl/mshop/orderlist.php?customer_id='.$customer_id.'&currtype=2';
				$this->ajaxReturn($invoke);
			}else if($res_order['is_pay']==1){
				$invoke['error_code']=1001;
				$invoke['data']="已领取";
				$this->ajaxReturn($invoke);
			}	
			$map_action['user_id']=decode_wsy(I("get.user_id_en"));
			$map_action['activity_id']=I("get.activity_id");
			$map_action['isvalid']=1;
			$count=M("action")->where($map_action)->count();
			$map_activity['id']=I("get.activity_id");
			$activity=M("activity")->where($map_activity)->find();
			if($count>=$activity['apply_number']){
				$nowtime=time();
				if(strtotime($activity['activity_end_time'])<$nowtime){
					$invoke['error_code']=1002;
					$invoke['data']="活动已终止";
					$this->ajaxReturn($invoke);
				}
				if($activity['status']==4){
					$invoke['error_code']=1002;
					$invoke['data']="活动已过期";
					$this->ajaxReturn($invoke);
				}
				if($activity['apply_number']!=0 && $activity['apply_number_single']!=0){
					$invoke['error_code']=1002;
					$invoke['data']="报名次数已满";
					$this->ajaxReturn($invoke);
				}
			}else{
				$map_action2['product_id']=I("get.product_id");
				$map_action2['user_id']=I("get.user_id");
				$map_action2['isvalid']=1;
				$count2=M("action")->where($map_action2)->count();
				if($count2>=$activity['apply_number_single']){
					if($activity['apply_number']!=0 && $activity['apply_number_single']!=0){
						$invoke['error_code']=1002;
						$invoke['data']="报名次数已满";
						$this->ajaxReturn($invoke);
					}
				}else{
					if($res_order['is_pay']==0){
						$invoke['error_code']=1004;
						$invoke['data']="已领取，请支付";
						//https://admin.weisanyun.cn/weixinpl/mshop/orderlist.php?customer_id=DDkGNlU3U2k=&currtype=2
						$invoke['href']=$this->http.'/weixinpl/mshop/orderlist.php?customer_id='.$customer_id.'&currtype=2';
						$this->ajaxReturn($invoke);
					}else if($res_order['is_pay']==1){
						$invoke['error_code']=1001;
						$invoke['data']="已领取";
						$this->ajaxReturn($invoke);
					}
				}
			}
		}
		if(I("get.product_id")){
			$where['id']=I("get.product_id");
			$temp=M("goods")->where($where)->find();
			if($temp['product_pro']!=-1){
				$map_prices['id']=$temp['product_pro'];
				$list=M()->table(DB_NAME.".weixin_commonshop_product_prices")->where($map_prices)->find();
				if($list['proids']){
					if(strrpos($list['proids'],"_")){
						$result['proids']=$list['proids'];
						$type['id']=strstr($list['proids'],'_',true);
						$shu=M()->table(DB_NAME.".weixin_commonshop_pros")->field("name,parent_id")->where($type)->find();
						$type_parent['id']=$shu['parent_id'];
						$shu_parent=M()->table(DB_NAME.".weixin_commonshop_pros")->field("name")->where($type_parent)->find();
						$result['type_parent']=$shu_parent['name'];
						$result['type']=$shu['name'];
						$material['id']=substr(strstr($list['proids'],'_'),1);
						$xing=M()->table(DB_NAME.".weixin_commonshop_pros")->field("name,parent_id")->where($material)->find();
						$material_parent['id']=$xing['parent_id'];
						$xing_parent=M()->table(DB_NAME.".weixin_commonshop_pros")->field("name")->where($material_parent)->find();
						$result['material_parent']=$xing_parent['name'];
						$result['material']=$xing['name'];
					}else{
						$result['proids']=$list['proids'];
						$type['id']=$list['proids'];
						$shu=M()->table(DB_NAME.".weixin_commonshop_pros")->field("name,parent_id")->where($type)->find();
						$type_parent['id']=$shu['parent_id'];
						$shu_parent=M()->table(DB_NAME.".weixin_commonshop_pros")->field("name")->where($type_parent)->find();
						$result['type_parent']=$shu_parent['name'];
						$result['type']=$shu['name'];
						$result['material_parent']=null;
						$result['material']=null;
					}
				}
			}else{
				$result['proids']=null;
				$result['type_parent']=null;
				$result['material_parent']=null;
				$result['type']=null;
				$result['material']=null;
			}
			$map_shop['id']=$temp['product_no'];
			$res=M()->table(DB_NAME.".weixin_commonshop_products")->where($map_shop)->find();
			$result['id']=$res['id'];
			$result['img']=$this->http.$res['default_imgurl'];
			$result['name']=$res['name'];
			$result['inventory']=$temp['inventory'];
			// if($bargain['latest_price']==0){
			// 	$result['price']=0.01;
			// 	$result['post_data']=array($bargain['activity_id'],I("get.product_id"),I("get.action_id"),$bargain['user_id'],0.01,$customer_id);
			// }else{
			// 	$result['price']=$bargain['latest_price'];
			// 	$result['post_data']=array($bargain['activity_id'],I("get.product_id"),I("get.action_id"),$bargain['user_id'],$bargain['latest_price'],$customer_id);
			// }
			$result['price']=$bargain['latest_price'];
			$result['post_data']=array($bargain['activity_id'],I("get.product_id"),I("get.action_id"),$bargain['user_id'],$bargain['latest_price'],$customer_id);
			
			$result['href']=$this->http.'/weixinpl/mshop/order_form.php';
		    $invoke['error_code']=1000;
		    $invoke['data']=$result;
		}else{
			$invoke['error_code']=1003;
			$invoke['data']="缺少类型";
			$this->ajaxReturn($invoke);
		}
		$this->ajaxReturn($invoke);
    }

    public function small(){
    	$this->ajaxReturn();
    }

    public function get_coupon(){
    	if(empty(I("get.customer_id_en"))){
			$invoke['error']=1003;
			$invoke['data']="customer_id为空！";
			$this->ajaxReturn($invoke);
		}
		if(empty(I("get.user_id_en"))){
			$invoke['error']=1003;
			$invoke['data']="user_id_en为空！";
			$this->ajaxReturn($invoke);
		}
		if(empty(I("get.activity_id"))){
			$invoke['error']=1003;
			$invoke['data']="activity_id为空！";
			$this->ajaxReturn($invoke);
		}
		if(empty(I("get.action_id"))){
			$invoke['error']=1003;
			$invoke['data']="action_id为空！";
			$this->ajaxReturn($invoke);
		}
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$user_id=decode_wsy(I("get.user_id_en"));
		$activity['id']=I("get.activity_id");
		$activity['customer_id']=$customer_id;
		$res=M("activity")->where($activity)->find();
		$map_coupons['customer_id']=$customer_id;
		$map_coupons['isvalid']=1;
		if($res['coupon_id']){
			$map_coupons['id']=$res['coupon_id'];
			$coupons=M()->table(DB_NAME.".weixin_commonshop_coupons")->where($map_coupons)->find();
		}
		if($res['is_coupon']==1){
			$map_bargain['action_id']=I("get.action_id");
			$map_bargain['customer_id']=$customer_id;
			$map_bargain['user_id']=$user_id;
			$bargain=M("bargain")->field("sum(is_coupon)")->where($map_bargain)->select();
			if($bargain[0]['sum(is_coupon)']){
				$invoke['error']=1001;
				$invoke['data']="你已经获得过优惠券";
			}else{
				$money=rand($coupons['MinMoney'],$coupons['MaxMoney']);
				$invoke['error']=1000;
				$invoke['data']=array("money"=>$money,"coupon_id"=>$res['coupon_id']);
			}
		}else if($res['is_coupon']==2){
			if(strtotime($coupons['getEndTime'])>=time()){
				$rand1=rand(1,100);
				$rand2=rand($res['coupon_probability'],100);
				if($rand2>$rand1){
					$money=rand($coupons['MinMoney'],$coupons['MaxMoney']);
					$invoke['error']=1000;
					$invoke['data']=array("money"=>$money,"coupon_id"=>$res['coupon_id']);
				}else{
					$invoke['error']=1001;
					$invoke['data']="没有获得优惠券";
				}
			}else{
				$invoke['error']=1001;
				$invoke['data']="没有优惠券";
			}
		}else{
			$invoke['error']=1001;
			$invoke['data']="没有优惠券";
		}
		$this->ajaxReturn($invoke);
    }

    public function coupon(){
    	if(empty(I("get.customer_id_en"))){
			$invoke['error']=1003;
			$invoke['data']="customer_id为空！";
			$this->ajaxReturn($invoke);
		}
		if(empty(I("get.user_id_en"))){
			$invoke['error']=1003;
			$invoke['data']="user_id_en为空！";
			$this->ajaxReturn($invoke);
		}
		if(empty(I("get.money"))){
			$invoke['error']=1003;
			$invoke['data']="优惠券金额为空！";
			$this->ajaxReturn($invoke);
		}
		if(empty(I("get.coupon_id"))){
			$invoke['error']=1003;
			$invoke['data']="优惠券id为空！";
			$this->ajaxReturn($invoke);
		}
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$user_id=decode_wsy(I("get.user_id_en"));
		$map_coupons['id']=I("get.coupon_id");
		$coupons=M()->table(DB_NAME.".weixin_commonshop_coupons")->where($map_coupons)->find();
		$map_couponusers['user_id']=$user_id;
		$couponusers=M()->table(DB_NAME.".weixin_commonshop_couponusers")->where($map_couponusers)->find();
		$data['code']="d".$user_id.time();
		$data['user_id']=$user_id;
		$data['customer_id']=$customer_id;
		$data['Money']=I("get.money");
		$data['deadline']=date("Y-m-d H:i:s",strtotime("+".$coupons['Days']." day"));
		$data['isvalid']=1;
		$data['createtime']=date("Y-m-d H:i:s");
		$data['NeedMoney']=$coupons['NeedMoney'];
		$data['type']=1;
		$data['is_used']=0;
		if($couponusers){
			$data['class_type']=1;
		}else{
			$data['class_type']=2;
		}
		$data['coupon_id']=I("get.coupon_id");
		$data['is_coupon_inentityUse']=0;
		$data['use_roles']='1_2_3_4_5_6';
		$data['coupon_use_inentity']="1_-1";
		$data['is_receive']=1;
		$data['startline']=date("Y-m-d H:i:s");
		$res=M()->table(DB_NAME.".weixin_commonshop_couponusers")->data($data)->add();
		if($res){
			$invoke['error']=1000;
			$invoke['data']="已添加至个人优惠券里";
		}else{
			$invoke['error']=1001;
			$invoke['data']="添加至个人优惠券失败";
		}
		$this->ajaxReturn($invoke);
    }
    
    
    //分享链接参数接口 
    public function information(){
    	if(empty(I("get.customer_id_en"))){
			$invoke['error']=1003;
			$invoke['data']="customer_id为空！";
			$this->ajaxReturn($invoke);
		}
		if(empty(I("get.activity_id"))){
			$invoke['error']=1003;
			$invoke['data']="activity_id为空！";
			$this->ajaxReturn($invoke);
		}
		/*
		share_url = HOST_WSY+'/weixinpl/common_shop/jiushop/forward.php?
		type=bargainIndex
		&customer_id_en='+customer_id_en+'
		&activity_id='+activity_id+'
		&idx='+id+'
		&apply_id='+apply_id;
		*/
		if(I("get.activity_id")){
			$map_activity['id']=I("get.activity_id");
			$activity=M("activity")->where($map_activity)->find();
			$invoke['data']['share_title']=$activity['activity_title'];
			//$invoke['data']['title']=$activity['activity_title'];
			$invoke['data']['share_desc']="快跟我来砍砍砍";
			if($activity['activity_logo']){
				$invoke['data']['share_img']=$activity['activity_logo'];
			}else{
				$invoke['data']['share_img']=$this->http."/weixinpl/haggling/front/web/img/adkj.jpg";
			}
			$invoke['data']['share_url']=$this->http."/weixinpl/haggling/front/web/index.html?customer_id_en=".I("get.customer_id_en").'&activity_id='.I("get.activity_id");
			//$invoke['data']['share_url']=$this->http."/weixinpl/common_shop/jiushop/forward.php?type=bargainIndex&customer_id_en=".I("get.customer_id_en").'&activity_id='.I("get.activity_id");
		}
		if(I("get.product_id")){
			$map_product['id']=I("get.product_id");
			$product=M("goods")->where($map_product)->find();
			$map_products['id']=$product['product_no'];
			$products=M()->table(DB_NAME.".weixin_commonshop_products")->where($map_products)->find();
			$invoke['share_title']=$activity['activity_title'];
			$invoke['data']['share_desc']="前面有一个".$products['name']."快跟我来砍砍砍";
			$invoke['data']['share_img']=$this->http.$products['default_imgurl'];
		}
		if(I("get.action_id")){
			$map_action['id']=I("get.action_id");
			$action=M("action")->where($map_action)->find();
			$map_product['id']=$action['product_id'];
			$product=M("goods")->where($map_product)->find();
			$map_products['id']=$product['product_no'];
			$products=M()->table(DB_NAME.".weixin_commonshop_products")->where($map_products)->find();
			$invoke['data']['share_img']=$this->http.$products['default_imgurl'];
			$find_weixin_users['id']=$action['user_id'];
			$weixin_users=M()->table(DB_NAME.".weixin_users")->where($find_weixin_users)->find();
			$invoke['data']['share_desc']="我是".$weixin_users['weixin_name']."这里有一个".$products['name']."大家快跟我来砍砍砍";
			$invoke['data']['share_url']=$this->http."/weixinpl/haggling/front/web/participator.html?customer_id_en=".I("get.customer_id_en").'&activity_id='.I("get.activity_id").'&idx='.$action['product_id'].'&apply_id='.I("get.action_id");
			//$invoke['data']['share_url']=$this->http."/weixinpl/common_shop/jiushop/forward.php?type=bargainIndex&customer_id_en=".I("get.customer_id_en").'&activity_id='.I("get.activity_id").'&idx='.$action['product_id'].'&apply_id='.I("get.action_id");
		}
		$invoke['error']=1000;
		$this->ajaxReturn($invoke);
    }

    public function test_coupon(){
    	if(empty(I("get.customer_id_en"))){
			$invoke['error']=1003;
			$invoke['data']="customer_id为空！";
			$this->ajaxReturn($invoke);
		}
		if(empty(I("get.activity_id"))){
			$invoke['error']=1003;
			$invoke['data']="activity_id为空！";
			$this->ajaxReturn($invoke);
		}
		$customer_id=decode_wsy(I("get.customer_id_en"));
		$activity_id=I("get.activity_id");
		$action_map['customer_id']=$customer_id;
		$action_map['activity_id']=$activity_id;
		$action=M("action")->where($action_map)->select();
		for ($i=0; $i < count($action); $i++) { 
			$action_res[$i]=$action[$i]['id'];
		}
		$bargain_map['customer_id']=$customer_id;
		if($action_res){
			$bargain_map['action_id']=array("IN",$action_res);
		}else{
			$invoke['error']=1001;
			$invoke['data']="没有人获得优惠券";
			$this->ajaxReturn($invoke);
		}
		$time=time()-4;
		$newtime=date("Y-m-d H:i:s",$time);
		$bargain_map['bargain_time']=array("gt",$newtime);
		$bargain=M("bargain")->where($bargain_map)->select();
		for ($i=0; $i < count($bargain); $i++) { 
			$map_couponusers['createtime']=$bargain[$i]['bargain_time'];
			$map_couponusers['user_id']=$bargain[$i]['user_id'];
			$map_couponusers['customer_id']=$customer_id;
			$res[$i]=M()->table(DB_NAME.".weixin_commonshop_couponusers")->where($map_couponusers)->find();
		}
		if($res){
			for ($i=0; $i < 1; $i++) { 
				if($res[$i]['Money']==null){
					$invoke['error']=1001;
					$invoke['data']="没有人获得优惠券";
					$this->ajaxReturn($invoke);
				}
				$result['money']=$res[$i]['Money'];
				$find_weixin_name['id']=$res[$i]['user_id'];
				$weixin_name=M()->table(DB_NAME.".weixin_users")->where($find_weixin_name)->find();
				$result['weixin_name']=$weixin_name['weixin_name'];
				$bargain_map['user_id']=$res[$i]['user_id'];
				$bargain2=M("bargain")->where($bargain_map)->find();
				$action_map['id']=$bargain2['action_id'];
				$action2=M("action")->where($action_map)->find();
				$find_weixin_name2['id']=$action2['user_id'];
				$weixin_name2=M()->table(DB_NAME.".weixin_users")->where($find_weixin_name2)->find();
				$result['apply_name']=$weixin_name2['weixin_name'];
			}
			$invoke['error']=1000;
			$invoke['data']=$result;
			$this->ajaxReturn($invoke);
		}else{
			$invoke['error']=1001;
			$invoke['data']="没有人获得优惠券";
			$this->ajaxReturn($invoke);
		}
    }

    public function echo_customer(){
    	if(empty(I("get.customer_id_en"))){
			$invoke['data']="customer_id为空！";
			$this->ajaxReturn($invoke);
		}
		$invoke['data']=decode_wsy(I("get.customer_id_en"));
		$this->ajaxReturn($invoke);
    }

    public function xiao_chengxu(){
    	$this->ajaxReturn("123456");
    }

    public function testfortext(){
    	$data = I('request.','','trim');
    	if(!$data['customer_id_en']){
    		$invoke['data']='缺少customer_id_en';
    		$invoke['error']=1002;
    		$this->ajaxReturn($invoke);
    	}
    	$customer_id=decode_wsy($data['customer_id_en']);
    	$map_activity['customer_id']=$customer_id;
    	$map_activity['isvalid']=1;
		$activity=M("activity")->field("ruler")->where($map_activity)->find();
		$ruler=$activity['ruler'];
		echo $ruler;
    }

    public function testforindex(){
    	$data = I('request.','','trim');
    	if(!$data['customer_id_en']){
    		$invoke['data']='缺少customer_id_en';
    		$invoke['error']=1002;
    		$this->ajaxReturn($invoke);
    	}
    	$customer_id=decode_wsy($data['customer_id_en']);
    	require_once '/opt/www/weixin_platform/mp/config.php';
    	require_once '/opt/www/weixin_platform/weixinpl/common/utility_shop.php';
		//$from 商城调用高级接口
		$utlity_common = new \shopMessage_Utlity();
		$utlity_common->web_Authorization($customer_id,$from=1);
    }

}