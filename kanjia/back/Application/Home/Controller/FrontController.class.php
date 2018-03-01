<?php
namespace Home\Controller;
session_start();
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET,OPTIONS');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header('Content-type:text/html;charset=utf-8');
use GuzzleHttp\json_decode;
#ini_set('date.timezone','Asia/Shanghai');
class FrontController extends \Think\Controller{
	public function _empty(){
		//空操作
		$this->display("Public:404"); 
	}
	public $user_id;
	public $user_id_en;
	public $customer_id;
	public $customer_id_en;
	public $activity_id;
	public $http;
	public function _initialize(){
		if(!isset($_GET['customer_id_en'])||!isset($_GET['activity_id'])){
			exit('缺少参数customer_id_en或activity_id');
		}
		$data = I('request.','','trim');
		$this->http = Protocol.$_SERVER['HTTP_HOST'];
		$customer_id = $data['customer_id_en'];
		$this->customer_id = is_numeric($customer_id)? $customer_id:decode_wsy($customer_id);
		$this->customer_id_en = encode_wsy($this->customer_id);
		$this->user_id = isset($data['user_id_en'])? decode_wsy($data['user_id_en']):'';
		$this->user_id_en = encode_wsy($this->user_id);
		$this->activity_id = $data['activity_id'];
		$_SESSION['customer_id'] = $this->customer_id;
		/* 初始化活动状态 */
		$init_k=0;
		$init_status['isvalid'] = true; 
		$init_status['customer_id'] = $this->customer_id;
		$init_list = M('activity')->field('id,activity_start_time,activity_end_time,activity_status')->where($init_status)->select();
		for(;$init_k<count($init_list);$init_k++){
		if($init_list[$init_k]['activity_status']==2){
		if(strtotime($init_list[$init_k]['activity_end_time'])<time()){
		$init_save['activity_status'] = 3;
		M('activity')->data($init_save)->where(['id'=>$init_list[$init_k]['id']])->save();
		}}}
	}
	
	//时间校正
	public function time_adjust($list_time){
		if(!$list_time||!is_array($list_time)){
			return false;
		}
		$result = array();
		$activity_start_time = $list_time[0];
		$activity_end_time = $list_time[1];
		$start_time = $list_time[2];
		$end_time = $list_time[3];
		if(strtotime($start_time)<=strtotime($activity_start_time)){
			$start_time = $activity_start_time;
		}
		if(strtotime($end_time)>strtotime($activity_end_time)){
			$end_time = $activity_end_time;
		}
		$result = [
		'activity_start_time' => str_replace('-','/',$activity_start_time),
		'activity_end_time' => str_replace('-','/',$activity_end_time),
		'start_time' => str_replace('-','/',$start_time),
		'end_time' => str_replace('-','/',$end_time),
		];
		return json_decode(json_encode($result));
	}
	
	//首页活动与商品状态信息 @param customer_id_en,user_id_en,activity_id
	public function indexInfo(){
		date_default_timezone_set('Asia/Shanghai');
		$result = array();
		$temp_pl = array();
		if(empty($this->user_id)){
			$result['err_code'] = 0;
			$result['data'] = '用户鉴权失败';
			return $this->ajaxReturn($result);
		}
		$where_c[] ='1=1';
		$where_c[] = "g.activity_id={$this->activity_id} and g.isvalid=1 and g.customer_id={$this->customer_id}";
		$str_activity = 'a.activity_title,a.activity_logo,a.activity_start_time,a.activity_end_time,a.activity_status,';
		$str_goods = 'g.activity_id,g.id,g.product_no,g.product_pro,g.price,g.buy_price,g.minimum_price,g.start_time,g.end_time';
		$list_goods = M('goods')->alias('g')
		->join("kj_activity a on g.activity_id=a.id and a.isvalid=1 and a.customer_id={$this->customer_id} and a.activity_status !=1")
		->where($where_c)
		->field($str_activity.$str_goods)
		->select();
		//时间校正
		array_walk($list_goods,function(&$ve,$data){
		$ve['nowtime'] = date('Y/m/d H:i:s',time());
		$ve['activity_start_time'] = str_replace('-','/',$ve['activity_start_time']);
		$ve['activity_end_time'] = str_replace('-','/',$ve['activity_end_time']);
		$ve['start_time'] = str_replace('-','/',$ve['start_time']);
		$ve['end_time'] = str_replace('-','/',$ve['end_time']);
		if($ve['activity_status']==2){
		if(strtotime($ve['start_time'])<=strtotime($ve['activity_start_time'])){
		$ve['start_time'] = str_replace('-','/',$ve['activity_start_time']);
		}
		if(strtotime($ve['end_time'])>strtotime($ve['activity_end_time'])){
		$ve['end_time'] = str_replace('-','/',$ve['activity_end_time']);
		}}});
		reset($list_goods);
		$list_com['isvalid'] = true;
		$list_com['customer_id'] = $this->customer_id;
		while(list($ver,$kid)=each($list_goods)){
		$where_pl = array_merge($list_com,['id'=>$kid['product_no']]);
		$temp_pl[] = M()->table(DB_NAME.'.weixin_commonshop_products')->field('id,name,default_imgurl')->where($where_pl)->find();
		}
		$temp_pl = array_map(function($data){
		$data['default_imgurl'] = BARGAIN_URL.$data['default_imgurl'];
		return $data;
		},$temp_pl);
		foreach($list_goods as &$ve){
		for($k=0;$k<count($temp_pl);$k++){
		if($ve['product_no']==$temp_pl[$k]['id']){
		$ve['name'] = $temp_pl[$k]['name'];
		$ve['default_imgurl'] = $temp_pl[$k]['default_imgurl'];
        }}}
        $where_action['user_id'] = $this->user_id;
        $where_action['activity_id'] = $this->activity_id;
        $where_action = array_merge($list_com,$where_action);
        foreach($list_goods as &$v){
        $where_action['product_id'] = $v['id'];
        $result_id = M('action')->where($where_action)->order('id desc')->getField('id');
        if(!empty($result_id)){
        $v['apply_id'] = $result_id;
        $v['share_status'] = 1;
        $v['share_desc'] = '已报名';
        }else{
		$v['share_status'] = 0;
		$v['share_desc'] = '未报名';
		}}
		unset($ve);
		reset($list_goods);
		array_walk($list_goods,function(&$ve,$data){
		if($ve['activity_status']==2){
		if(time()<strtotime($ve['activity_start_time'])){
		$ve['Activity_expire_code'] = 1;
		$ve['Activity_expire_desc'] = '活动未开始';
		}else{
		$ve['Activity_expire_code'] = 2;
		$ve['Activity_expire_desc'] = '活动进行中';
		if(time()<strtotime($ve['start_time'])){
		$ve['goods_expire_code'] = 1;
		$ve['goods_expire_desc'] = '商品砍价未开始';
		}
		if(time()>=strtotime($ve['start_time'])&&time()<strtotime($ve['end_time'])){
		$ve['goods_expire_code'] = 2;
		$ve['goods_expire_desc'] = '商品砍价进行中';
		}
		if(time()>strtotime($ve['end_time'])){
		$ve['goods_expire_code'] = 3;
		$ve['goods_expire_desc'] = '商品砍价已结束';
		}}}
		if($ve['activity_status']==3){
		$ve['Activity_expire_code'] = 3;
		$ve['Activity_expire_desc'] = '活动已结束';
		$ve['goods_expire_code'] = 3;
		$ve['goods_expire_desc'] = '商品砍价已结束';
		}
		if($ve['activity_status']==4){
		$ve['Activity_expire_code'] = 4;
		$ve['Activity_expire_desc'] = '活动已终止';
		$ve['goods_expire_code'] = 4;
		$ve['goods_expire_desc'] = '商品砍价已终止';
		}});
		reset($list_goods);
		$list_goods_new = [];
		$list_goods_new_new = [];
		while(list($kt,$j)=each($list_goods)){
		$list_goods_new['id'] = $j['id'];
		$list_goods_new['activity_id'] = $j['activity_id'];
		$list_goods_new['activity_start_time'] = $j['activity_start_time'];
		$list_goods_new['activity_end_time'] = $j['activity_end_time'];
		$list_goods_new['nowtime'] = $j['nowtime'];
		$list_goods_new['activity_expire_code'] = $j['Activity_expire_code'];
		$list_goods_new['activity_expire_desc'] = $j['Activity_expire_desc'];
		$list_goods_new['goods_expire_code'] = $j['goods_expire_code'];
		$list_goods_new['goods_expire_desc'] = $j['goods_expire_desc'];
		//$list_goods_new['activity_title'] = $j['activity_title'];
		$list_goods_new_new[] = $list_goods_new;}
		if(!$list_goods_new_new){
		$result['err_code'] = 0;
		$result['data'] = '没有数据';
		}else{
		$result['err_code'] = 1;
		$result['data'] = $list_goods_new_new;
		$result['activity_title'] = $list_goods[0]['activity_title'];
		}
		return $this->ajaxReturn($result);
		//
	}
	
	/**
	 * @desc 首页商品信息 (过期和终止的也要展示)
	 * @param customer_id_en,user_id_en,activity_id
	 */
	public function goodsInfo(){
		date_default_timezone_set('Asia/Shanghai');
		$result = array();
		$temp_pl = array();
		if(empty($this->user_id)){
			$result['err_code'] = 0;
			$result['data'] = '用户鉴权失败';
			return $this->ajaxReturn($result);
		}
		$where_c[] ='1=1';
		$where_c[] = "g.activity_id={$this->activity_id} and g.isvalid=1 and g.customer_id={$this->customer_id}";
		$str_activity = 'a.activity_title,a.activity_logo,a.activity_start_time,a.activity_end_time,a.activity_status,';
		$str_goods = 'g.activity_id,g.id,g.product_no,g.product_pro,g.price,g.buy_price,g.minimum_price,g.start_time,g.end_time';
		$list_goods = M('goods')->alias('g')
		->join("kj_activity a on g.activity_id=a.id and a.isvalid=1 and a.customer_id={$this->customer_id} and a.activity_status !=1")   
		->where($where_c)
		->field($str_activity.$str_goods)
		->select();
		//时间校正
		array_walk($list_goods,function(&$ve,$data){
		$ve['nowtime'] = date('Y/m/d H:i:s',time());
		$ve['activity_start_time'] = str_replace('-','/',$ve['activity_start_time']);
		$ve['activity_end_time'] = str_replace('-','/',$ve['activity_end_time']); 
		$ve['start_time'] = str_replace('-','/',$ve['start_time']);
		$ve['end_time'] = str_replace('-','/',$ve['end_time']);
		if($ve['activity_status']==2){
		if(strtotime($ve['start_time'])<=strtotime($ve['activity_start_time'])){
		$ve['start_time'] = str_replace('-','/',$ve['activity_start_time']);
		}
		if(strtotime($ve['end_time'])>strtotime($ve['activity_end_time'])){
		$ve['end_time'] = str_replace('-','/',$ve['activity_end_time']); 
		}}});
	    reset($list_goods);
		$list_com['isvalid'] = true;
		$list_com['customer_id'] = $this->customer_id;
		while(list($ver,$kid)=each($list_goods)){
		$where_pl = array_merge($list_com,['id'=>$kid['product_no']]);
		$temp_pl[] = M()->table(DB_NAME.'.weixin_commonshop_products')->field('id,name,default_imgurl')->where($where_pl)->find();
		}
		$temp_pl = array_map(function($data){
		$data['default_imgurl'] = BARGAIN_URL.$data['default_imgurl'];
		return $data;
		},$temp_pl);
		foreach($list_goods as &$ve){
		for($k=0;$k<count($temp_pl);$k++){
		if($ve['product_no']==$temp_pl[$k]['id']){
		$ve['name'] = $temp_pl[$k]['name'];
		$ve['default_imgurl'] = $temp_pl[$k]['default_imgurl'];
        }}}
		$where_action['user_id'] = $this->user_id;
		$where_action['activity_id'] = $this->activity_id;
		$where_action = array_merge($list_com,$where_action);
		foreach($list_goods as &$v){
		$where_action['product_id'] = $v['id'];
		$result_id = M('action')->where($where_action)->order('id desc')->getField('id');
		if(!empty($result_id)){
		$v['apply_id'] = $result_id;
		$v['share_status'] = 1;
		$v['share_desc'] = '已报名';
		}else{
		$v['share_status'] = 0;
		$v['share_desc'] = '未报名';
		}}
		unset($ve);
		reset($list_goods);
		array_walk($list_goods,function(&$ve,$data){
		if($ve['activity_status']==2){
		if(time()<strtotime($ve['activity_start_time'])){
		$ve['Activity_expire_code'] = 1;
		$ve['Activity_expire_desc'] = '活动未开始';
		}else{
		$ve['Activity_expire_code'] = 2;
		$ve['Activity_expire_desc'] = '活动进行中';
		if(time()<strtotime($ve['start_time'])){
		$ve['goods_expire_code'] = 1;
		$ve['goods_expire_desc'] = '商品砍价未开始';
		}
		if(time()>=strtotime($ve['start_time'])&&time()<strtotime($ve['end_time'])){
		$ve['goods_expire_code'] = 2;
		$ve['goods_expire_desc'] = '商品砍价进行中';
		}
		if(time()>strtotime($ve['end_time'])){
		$ve['goods_expire_code'] = 3;
		$ve['goods_expire_desc'] = '商品砍价已结束';
		}}}
		if($ve['activity_status']==3){
		$ve['Activity_expire_code'] = 3;
		$ve['Activity_expire_desc'] = '活动已结束';
		$ve['goods_expire_code'] = 3;
		$ve['goods_expire_desc'] = '商品砍价已结束';
		}
		if($ve['activity_status']==4){
		$ve['Activity_expire_code'] = 4;
		$ve['Activity_expire_desc'] = '活动已终止';
		$ve['goods_expire_code'] = 4;
		$ve['goods_expire_desc'] = '商品砍价已终止';
		}});
		reset($list_goods);
		$list_goods_new = []; 
		$list_goods_new_new = [];
		while(list($kt,$j)=each($list_goods)){
		$list_goods_new['id'] = $j['id'];
		$list_goods_new['activity_id'] = $j['activity_id'];
		$list_goods_new['product_no'] = $j['product_no'];
		$list_goods_new['product_pro'] = $j['product_pro'];
		$list_goods_new['price'] = $j['price'];
		$list_goods_new['buy_price'] = $j['buy_price'];
		$list_goods_new['minimum_price'] = $j['minimum_price'];
		$list_goods_new['start_time'] = $j['start_time'];
		$list_goods_new['end_time'] = $j['end_time'];
		$list_goods_new['name'] = $j['name'];
		$list_goods_new['default_imgurl'] = $j['default_imgurl'];
		$list_goods_new['Activity_title'] = $j['activity_title'];
		if($j['activity_logo']){
			$list_goods_new['Activity_logo'] = $j['activity_logo'];
		}else{
			$list_goods_new['Activity_logo'] = "http://shenzhen.weisanyun.cn/weixinpl/haggling/front/web/img/adkj.jpg";
		}
		$list_goods_new['Activity_startime'] = $j['activity_start_time'];
		$list_goods_new['Activity_endtime'] = $j['activity_end_time'];
		$list_goods_new['Activity_status'] = $j['activity_status'];
		$list_goods_new['nowtime'] = $j['nowtime'];
		$list_goods_new['Activity_expire_code'] = $j['Activity_expire_code'];
		$list_goods_new['Activity_expire_desc'] = $j['Activity_expire_desc'];
		$list_goods_new['goods_expire_code'] = $j['goods_expire_code'];
		$list_goods_new['goods_expire_desc'] = $j['goods_expire_desc'];
		$list_goods_new['share_status'] = $j['share_status'];
		$list_goods_new['apply_id'] = $j['apply_id'];
		$list_goods_new['share_desc'] = $j['share_desc'];
		$list_goods_new_new[] = $list_goods_new;
		}
		if(!$list_goods_new_new){
		$result['err_code'] = 0;
		$result['data'] = '没有数据';
		}else{
		$result['err_code'] = 1;
		$result['data'] = $list_goods_new_new;
		}
		return $this->ajaxReturn($result);
		//
	}
	
	//未报名砍价页面商品信息
	public function goods_details(){
		$result = [];
		$id = isset($_GET['id'])? trim(I('get.id')):'';
		if(empty($id)){
			$result['err_code'] = 1002;
			$result['data'] = '缺少商品id';
			return $this->ajaxReturn($result);
		}
		$list_goods = M('goods')->field('apply_number,play_number,create_time',true)->find($id);
		$product_no = $list_goods['product_no'];
		$where_c1['id'] =  $list_goods['product_no'];
		$where_c1['isvalid'] = true;
		$where_c1['customer_id'] = $this->customer_id;
		$list_goods_pl = M()->table(DB_NAME.'.weixin_commonshop_products')->field('id as shop_id,name,specifications,customer_service,description,introduce')->where($where_c1)->find();
		$where_c2['isvalid'] = 1;
		$where_c2['customer_id'] = $this->customer_id;
		$where_c2['product_id'] = $list_goods_pl['shop_id'];
		$list_goods = array_merge($list_goods,$list_goods_pl);
		$list_imgs = M()->table(DB_NAME.'.weixin_commonshop_product_imgs')->field('imgurl')->where($where_c2)->select();
		$result_img = [];
		for($j=0;$j<count($list_imgs);$j++){
			$result_img[] = BARGAIN_URL.$list_imgs[$j]['imgurl'];
		}
		if(!$result_img){
			$result_img[] = BARGAIN_URL.'/weixinpl/haggling/back/Public/ass/images/mrkj.jpg';
		}
		$where_c3['isvalid'] = 1;
		$where_c3['customer_id'] = $this->customer_id;
		$where_c3['id'] = $this->activity_id;
		$where_c3['activity_status'] = ['neq',1];
		$list_activity = M('activity')->field('activity_status,activity_start_time,activity_end_time,ruler')->where($where_c3)->find();
		$list_goods['activity_start_time'] = str_replace('-','/',$list_activity['activity_start_time']);
		$list_goods['activity_end_time'] = str_replace('-','/',$list_activity['activity_end_time']);
		$list_goods['activity_status'] = $list_activity['activity_status'];
		$list_goods_new = [];
		$list_goods_new[] = $list_goods;
		array_walk($list_goods_new,function(&$data,$ven){
			if($data['start_time']){$data['start_time'] = str_replace('-','/',$data['start_time']);}
			if($data['end_time']){$data['end_time'] = str_replace('-','/',$data['end_time']);}
			$data['nowtime'] = date('Y/m/d H:i:s',time());
		});
		$list_goods = $list_goods_new;
		//introduce 简介,description 详情描述 ,specifications 规格,customer_service 售后保障
		array_walk($list_goods,function(&$ve,$ken){
			//活动状态判断
			if($ve['activity_status']==2){
				//商品开始砍价时间校正
				if(strtotime($ve['start_time'])<=strtotime($ve['activity_start_time'])){
					$ve['start_time'] = $ve['activity_start_time'];
				}
				//商品结束砍价时间校正
				if(strtotime($ve['end_time'])>strtotime($ve['activity_end_time'])){
					$ve['end_time'] = $ve['activity_end_time'];
				}
			}
			/* if(strpos($ve['specifications'],'<p')!==false){
				$str = '';
				$str = substr($ve['specifications'],0,-4);
				$ve['specifications'] = substr(strrchr($str,'>'),1);
			}
			if(strpos($ve['customer_service'],'<p')!==false){ //<p>全国联保，放心下单吧亲</p>
				$strx = $ve['customer_service'];
				$lindex = strrpos($strx,'<')-strlen($strx);
				$strx = substr($strx,0,$lindex);
				$ve['customer_service'] = substr(strrchr($strx,'>'),1);
			} */
		});
		$list_goods = $list_goods[0];
		
		//这里添加店铺信息
		//$product_no = 1680;
		//$this->customer_id = 5195;
		//$this->customer_id_en = encode_wsy($this->customer_id);
		/**
		 * weixin_commonshops 商城表
		 * weixin_commonshop_products 商品表
         * weixin_commonshop_applysupplys 供应商表
         * weixin_commonshop_brand_supplys 品牌供应商   
		 */
		$mshopInfo = array();
		$list_shopInfo = array();
		$where_com['isvalid'] = true;
		$where_com['customer_id'] = $this->customer_id;  //
		//is_supply_id -1商城
		$where_products = array_merge($where_com,['id'=>$product_no]);
		$shop_id = M(DB_NAME.'.weixin_commonshop_products',null)->where($where_products)->getField('is_supply_id');
		if($shop_id<0){//商城
			$mshopInfo['mshop_code'] = 0; 
			$mshopInfo['mshop_data'] = '非品牌供应商'; 
		}else{//供应商
			$where_brand['isvalid'] = true;
			$where_brand['user_id'] = $shop_id;
			$isbrand_supply = M(DB_NAME.'.weixin_commonshop_applysupplys',null)->where($where_brand)->getField('isbrand_supply');
			if(!$isbrand_supply){
				$mshopInfo['mshop_code'] = 0;
				$mshopInfo['mshop_data'] = '非品牌供应商';
			}else{//品牌供应商
				//user_id供应商id,customer_id,isvalid  
				$where_isbrand['isvalid'] = true;
				$where_isbrand['brand_status'] = 1;
				$where_isbrand['user_id'] = $shop_id;
				$where_isbrand['customer_id'] = $this->customer_id;
				$commonshop_brand = M(DB_NAME.'.weixin_commonshop_brand_supplys',null)->field('brand_logo,brand_tel,pro_num,collect_num,brand_supply_name')->where($where_isbrand)->find();   
				$list_shopInfo['shop_name'] = $commonshop_brand['brand_supply_name'];
				$list_shopInfo['shop_tel'] = $commonshop_brand['brand_tel'];
				//http://admin.weisanyun.cn/weixinpl/mshop/my_store/my_store.php?customer_id=BTBRYQBiV20=&supplier_id=300667
				$list_shopInfo['shop_url'] = 'https://'.$_SERVER['HTTP_HOST'].'/weixinpl/mshop/my_store/my_store.php?customer_id='.$this->customer_id_en.'&supplier_id='.$shop_id;
				$list_shopInfo['shop_logo'] = BARGAIN_URL.$commonshop_brand['brand_logo'];
				$list_shopInfo['shop_pro_num'] = $commonshop_brand['pro_num'];
				$list_shopInfo['shop_collect_num'] = $commonshop_brand['collect_num'];
				$mshopInfo['mshop_code'] = 1;
				$mshopInfo['mshop_data'] = $list_shopInfo;
			}
		}
		if(!$list_goods){
			$result['err_code'] = 1003;
			$result['data'] = '没有数据';
		}else{
			$result['err_code'] = 1000;
			$result['mshop'] = $mshopInfo;
			$result['data'] = $list_goods;
			$result['img'] = $result_img;
			$result['ruler'] = $list_activity['ruler'];
			$result['qr_code'] = $this->http."/weixinpl/mshop/product_detail.php?customer_id=".$this->customer_id_en."&pid=".$product_no;
		}
		return $this->ajaxReturn($result);
		//
	}
	
	//用户报名权验证  customer_id_en,user_id_en,activity_id,id
	public function checkApply(){//
		$result = [];
		$product_id = isset($_GET['id'])? intval(I('get.id')):'';
		if(empty($this->user_id)){
			$result['err_code'] = 0;
			$result['data'] = '用户鉴权失败';
			return $this->ajaxReturn($result);
		}
		if(!$product_id){
			$result['err_code'] = 0;
			$result['data'] = '缺少商品id';
			return $this->ajaxReturn($result);
		}
		$list_com['isvalid'] = true;
		$list_com['customer_id'] = $this->customer_id;
		//如果当前活动商品用户已经报名且尚未领取,则不允许同时再次报名同一个商品 (可报名其他商品)
		$where_l['user_id'] = $this->user_id;
		$where_l['activity_id'] = $this->activity_id;
		$where_l['product_id'] = $product_id;
		$where_l = array_merge($list_com,$where_l);
		$result_apply = M('action')->where($where_l)->field('id')->order('id desc')->find();
		if($result_apply['id']){//已报名
			$order_s['isvalid'] = true;
			$order_s['activity_id'] = $this->activity_id;
			$order_s['product_id'] = $product_id;
			$order_s['user_id'] = $this->user_id;
			$order_s['customer_id'] = $this->customer_id;
			$order_s['action_id'] = $result_apply['id'];
			$result_order = M('order')->field('id,is_pay')->where($order_s)->order('id desc')->find();
			if(!$result_order){
				$result['err_code'] = 0;
				$result['data'] = '您当前活动商品正在进行中'; //尚未领取
				return $this->ajaxReturn($result);
			}else{
				if(empty($result_order['is_pay'])){
					$result['err_code'] = 0;
					$result['data'] = '您当前活动商品正在进行中'; //下单未支付
					return $this->ajaxReturn($result);
				}
			}
		}
		$number_activity = ''; //一个报名者可报名活动总次数
		$number_goods = '';    //一个报名者对该商品可以报名的次数 (优先级高于全局)
		//活动信息
		$where_activity['id'] = $this->activity_id;
		$where_activity = array_merge($list_com,$where_activity);
		$list_activity = M('activity')->field(true)->where($where_activity)->find();
		if(!$list_activity){
			$result['err_code'] = 0;
			$result['data'] = '该活动已无效';
			return $this->ajaxReturn($result);
		}
		$number_activity = $list_activity['apply_number'];
		$number_goods_global = $list_activity['apply_number_single'];
		$activity_status = $list_activity['activity_status'];
		$activity_start_time = str_replace('-','/',$list_activity['activity_start_time']);
		$activity_end_time = str_replace('-','/',$list_activity['activity_end_time']);
		//商品信息
		$where_goods['id'] = $product_id;
		$where_goods = array_merge($list_com,$where_goods);
		$list_goods = M('goods')->field(true)->where($where_goods)->find();
		if(!$list_goods){
			$result['err_code'] = 0;
			$result['data'] = '该商品已无效';
			return $this->ajaxReturn($result);
		}
		$inventory = $list_goods['inventory']; 
		$number_goods = $list_goods['apply_number'];
		$start_time = str_replace('-','/',$list_goods['start_time']); 
		$end_time = str_replace('-','/',$list_goods['end_time']); 
    
		//开始验证  活动状态
		switch($activity_status){//1：未发布；2：进行中；3：已过期；4：已终止；
			case 1:
				$result['err_code'] = 0;
				$result['data'] = '活动未发布';
				return $this->ajaxReturn($result);
			case 3:
				$result['err_code'] = 0;
				$result['data'] = '活动已结束';
				return $this->ajaxReturn($result);
			case 4:
				$result['err_code'] = 0;
				$result['data'] = '活动已终止';
				return $this->ajaxReturn($result);
		}
		//商品开始砍价时间校正
		if(strtotime($start_time)<=strtotime($activity_start_time)){
			$start_time = $activity_start_time;
		}
		//商品结束砍价时间校正
		if(strtotime($end_time)>strtotime($activity_end_time)){
			$end_time = $activity_end_time;
		}
		//验证活动时间
		if(time()<strtotime($start_time)){//砍价还没开始
			$result['err_code'] = 0;
			$result['data'] = '砍价未开始';
			return $this->ajaxReturn($result); 
		}
		if(time()>strtotime($end_time)){//活动进行中
			$result['err_code'] = 0;
			$result['data'] = '砍价已结束';
			return $this->ajaxReturn($result);
		}
		//验证库存
		if(intval($inventory)<=0){
			$result['err_code'] = 0;
			$result['data'] = '库存不足';
			return $this->ajaxReturn($result);
		}
		//0为不限制
		if(empty($number_activity)){
			$number_activity = 9999999;
			if(empty($number_goods)){$number_goods = $number_goods_global;}
			if(empty($number_goods)){$number_goods = 9999999;}
			if($number_goods>$number_activity){$number_goods = $number_activity;}
		}
		//验证报名次数
		$where_activity_pl['user_id'] = $this->user_id;
		$where_activity_pl['activity_id'] = $this->activity_id;
		$where_activity_pl = array_merge($list_com,$where_activity_pl);
		$where_goods_pl = array_merge($list_com,$where_activity_pl,['product_id'=>$product_id]);
		$times_activity = M('action')->where($where_activity_pl)->count('id');
		$times_goods = M('action')->where($where_goods_pl)->count('id');
		if($times_activity>=$number_activity){
			$result['err_code'] = 0;
			$result['data'] = '您当前的活动报名次数已满';
			return $this->ajaxReturn($result);
		}
		if($times_goods>=$number_goods){
			$result['err_code'] = 0;
			$result['data'] = '您对当前商品的报名次数已满';
			return $this->ajaxReturn($result);
		}
		//可以报名
		if(!$times_activity){
			$checkInfo['number_activity'] = $number_activity; //剩余报名活动次数
			$checkInfo['number_goods'] = $number_goods;       //当前商品剩余报名次数
		}else{
			if($number_activity>$times_activity){//可报名活动
				$number_activity = $number_activity-$times_activity;
				$number_goods = $number_goods-$times_goods;
				if($number_goods>$number_activity){$number_goods=$number_activity;}
				$checkInfo['number_activity'] = $number_activity;
				$checkInfo['number_goods'] = $number_goods;
			}
		}
		$result['err_code'] = 1;
		$result['data'] = $checkInfo;
		return $this->ajaxReturn($result);
		//
	}
	
	//发起报名
	public function shareApply(){
		$result = [];
		$product_id = isset($_GET['id'])? intval(I('get.id')):'';
		if(empty($this->user_id)){
			$result['err_code'] = 0;
			$result['data'] = '用户鉴权失败';
			return $this->ajaxReturn($result);
		}
		if(!$product_id){
			$result['err_code'] = 0;
			$result['data'] = '缺少商品id';
			return $this->ajaxReturn($result);
		}
		$list_com['isvalid'] = true;
		$list_com['customer_id'] = $this->customer_id;
		//如果当前活动商品用户已经报名且尚未领取,则不允许同时再次报名同一个商品 (可报名其他商品)
		$where_l['user_id'] = $this->user_id;
		$where_l['activity_id'] = $this->activity_id;
		$where_l['product_id'] = $product_id;
		$where_l = array_merge($list_com,$where_l);
		$result_apply = M('action')->where($where_l)->field('id')->order('id desc')->find();
		if($result_apply['id']){//已报名
			$order_s['isvalid'] = true;
			$order_s['activity_id'] = $this->activity_id;
			$order_s['product_id'] = $product_id;
			$order_s['user_id'] = $this->user_id;
			$order_s['customer_id'] = $this->customer_id;
			$order_s['action_id'] = $result_apply['id'];
			$result_order = M('order')->field('id,is_pay')->where($order_s)->order('id desc')->find();
			if(!$result_order){
				$result['err_code'] = 0;
				$result['data'] = '您当前活动商品正在进行中'; //尚未领取
				return $this->ajaxReturn($result);
			}else{
				if(empty($result_order['is_pay'])){
					$result['err_code'] = 0;
					$result['data'] = '您当前领取商品未支付'; //下单未支付
					return $this->ajaxReturn($result);
				}
			}
		}
		
		$number_activity = ''; //一个报名者可报名活动总次数
		$number_goods = '';    //一个报名者对该商品可以报名的次数 (优先级高于全局)
		//活动信息
		$where_activity['id'] = $this->activity_id;
		$where_activity = array_merge($list_com,$where_activity);
		$list_activity = M('activity')->field(true)->where($where_activity)->find();
		if(!$list_activity || $list_activity['activity_status']==3 || $list_activity['activity_status']==4){
			$result['err_code'] = 0;
			$result['data'] = '该活动已无效';
			return $this->ajaxReturn($result);
		}
		$number_activity = $list_activity['apply_number'];
		$number_goods_global = $list_activity['apply_number_single'];
		$activity_status = $list_activity['activity_status'];
		$activity_start_time = str_replace('-','/',$list_activity['activity_start_time']);
		$activity_end_time = str_replace('-','/',$list_activity['activity_end_time']);
		//商品信息
		$where_goods['id'] = $product_id;
		$where_goods = array_merge($list_com,$where_goods);
		$list_goods = M('goods')->field(true)->where($where_goods)->find();
		if(!$list_goods){
			$result['err_code'] = 0;
			$result['data'] = '该商品已无效';
			return $this->ajaxReturn($result);
		}
		$inventory = $list_goods['inventory'];
		$number_goods = $list_goods['apply_number'];
		$start_time = str_replace('-','/',$list_goods['start_time']);
		$end_time = str_replace('-','/',$list_goods['end_time']);
		//开始验证  活动状态
		switch($activity_status){//1：未发布；2：进行中；3：已过期；4：已终止；
			case 1:
				$result['err_code'] = 0;
				$result['data'] = '活动未发布';
				return $this->ajaxReturn($result);
			case 3:
				$result['err_code'] = 0;
				$result['data'] = '活动已结束';
				return $this->ajaxReturn($result);
			case 4:
				$result['err_code'] = 0;
				$result['data'] = '活动已终止';
				return $this->ajaxReturn($result);
		}
		//商品开始砍价时间校正
		if(strtotime($start_time)<=strtotime($activity_start_time)){
			$start_time = $activity_start_time;
		}
		//商品结束砍价时间校正
		if(strtotime($end_time)>strtotime($activity_end_time)){
			$end_time = $activity_end_time;
		}
		//验证活动时间
		if(time()<strtotime($start_time)){//砍价还没开始
			$result['err_code'] = 0;
			$result['data'] = '砍价未开始';
			return $this->ajaxReturn($result);
		}
		if(time()>strtotime($end_time)){//活动进行中
			$result['err_code'] = 0;
			$result['data'] = '砍价已结束';
			return $this->ajaxReturn($result);
		}
		//验证库存
		if(intval($inventory)<=0){
			$result['err_code'] = 0;
			$result['data'] = '库存不足';
			return $this->ajaxReturn($result);
		}
		//0为不限制
		if(empty($number_activity)){
			$number_activity = 9999999;
		    if(empty($number_goods)){$number_goods = $number_goods_global;}
		    if(empty($number_goods)){$number_goods = 9999999;}
		    if($number_goods>$number_activity){$number_goods = $number_activity;}
		}
		//验证报名次数
		$where_activity_pl['user_id'] = $this->user_id;
		$where_activity_pl['activity_id'] = $this->activity_id;
		$where_activity_pl = array_merge($list_com,$where_activity_pl);
		$where_goods_pl = array_merge($list_com,$where_activity_pl,['product_id'=>$product_id]);
		$times_activity = M('action')->where($where_activity_pl)->count('id');
		$times_goods = M('action')->where($where_goods_pl)->count('id');
		if($times_activity>=$number_activity){
			$result['err_code'] = 0;
			$result['data'] = '您当前的活动报名次数已满';
			return $this->ajaxReturn($result);
		}
		if($times_goods>=$number_goods){
			$result['err_code'] = 0;
			$result['data'] = '您对当前商品的报名次数已满';
			return $this->ajaxReturn($result);
		}
		///可以报名
		$list['activity_id'] = $this->activity_id;
		$list['product_id'] = $product_id;
		$list['user_id'] = $this->user_id;
		$list['apply_time'] = date('Y/m/d H:i:s',time());
		$list['latest_price'] = $list_goods['price'];
		$list['isvalid'] = 1;
		$list['customer_id'] = $this->customer_id;
		$resultInt = M('action')->data($list)->filter('htmlentities')->add();
		if(!$resultInt){
			$result['err_code'] = 0;
			$result['data'] = '报名失败';
		}else{
			$result_dec = M('goods')->where(['id'=>$product_id])->setDec('inventory');
			if($result_dec){
				$result['err_code'] = 1;
				$result['apply_id'] = $resultInt;
				$result['data'] = '报名成功';
			}else{ 
				$result['err_code'] = 0;
				$result['data'] = '报名出错'; //库存更新失败
			}
		}
		return $this->ajaxReturn($result);
		//
	}
	
	//已报名砍价页面信息 
	public function goodsInfoed(){
		$result = [];
		$list_goods = [];
		$list_imgs = '';
		$product_id = isset($_GET['id'])? intval(I('get.id')):'';
		if(empty($this->user_id)){
			$result['err_code'] = 1004;
			$result['data'] = '用户鉴权失败';
			return $this->ajaxReturn($result);
		}
		if(!$product_id){
			$result['err_code'] = 1004;
			$result['data'] = '缺少商品id';
			return $this->ajaxReturn($result);
		}
		$list_goods = $this->getInfo($this->user_id,$product_id);
		if(empty($list_goods['apply_status'])){
			$result['err_code'] = 1004;
			$result['data'] = '您没有报名当前商品';
			return $this->ajaxReturn($result);
		}
		$apply_id = $list_goods['apply_id'];
		$product_no = $list_goods['product_no'];
		$com['isvalid'] = true;
		$com['customer_id'] = $this->customer_id;
		$list_w = new \Vendor\lib\Com();
		//获取商品轮播图片
		$where_c2 = array_merge($com,['product_id'=>$product_no]);
		$list_imgs = M(DB_NAME.'.weixin_commonshop_product_imgs',null)->field('imgurl')->where($where_c2)->select();
		$list_imgs = array_map(function($data){return BARGAIN_URL.$data['imgurl'];},$list_imgs);
		if(!$list_imgs){
			$list_imgs[0]=BARGAIN_URL.'/weixinpl/haggling/back/Public/ass/images/mrkj.jpg';
		}
		//获取报名者用户名称与头像
		$list_action = M('action')->field('user_id,customer_id,activity_id')->where(['id'=>$apply_id,'isvalid'=>1])->find();
		$list_user = $list_w->getwxInfo($list_action['user_id'],$list_action['customer_id']);
		$list_goods['exp_user_name'] = $list_user['nickname'];
		$list_goods['exp_userUrl'] = $list_user['headimgurl'];
		//参与者砍价信息
		$list_bargain_userInfo = [];
		$where_bargain['isvalid'] = 1;
		$where_bargain['customer_id'] = $this->customer_id;
		$where_bargain['action_id'] =  $apply_id;
		$where_bargain['product_id'] = $product_id;
		$list_bargain = M('bargain')->field('id')->where($where_bargain)->order('id desc')->limit(3)->select();
		$bargain_user_total = M('bargain')->where($where_bargain)->count('id');
		if($list_bargain){
			for($j=0;$j<count($list_bargain);$j++){
				$list_bargain_userInfo[] = M('bargain')->field('user_id,bargain_price,bargain_time')->find($list_bargain[$j]['id']);
			}
		}
		foreach($list_bargain_userInfo as &$vm){
			$list_user_x = $list_w->getwxInfo($vm['user_id'],$this->customer_id);
			$vm['bargain_time'] = str_replace('-','/',$vm['bargain_time']);
			$vm['bargain_user_name'] = $list_user_x['nickname'];
			$vm['bargain_user_url'] = $list_user_x['headimgurl'];
		}
		if(!$list_goods){
			$result['err_code'] = 1003;
			$result['data'] = '没有数据';
		}else{
			$result['err_code'] = 1000;
			$result['img'] = $list_imgs;
			$result['data'] = $list_goods;
			$result['bargain_user_total'] = $bargain_user_total;
			$result['bargainUserInfo'] = $list_bargain_userInfo;
			$map_activity['id']=$list_action['activity_id'];
			$activity=M("activity")->field("ruler")->where($map_activity)->find();
			$result['ruler'] = $activity['ruler'];
			$result['qr_code'] = $this->http."/weixinpl/mshop/product_detail.php?customer_id=".$this->customer_id_en."&pid=".$product_no;
		}
		return $this->ajaxReturn($result);
		//
	}
	
	/**
	 * @desc 参与者砍价页信息(商品价格和参与者信息)
	 * @param user_id_en,apply_id
	 */
	public function getBargainInfo(){ 
		$result = [];
		$list_goods = [];
		$list_w = new \Vendor\lib\Com();
		$apply_id = isset($_GET['apply_id'])? trim(I('get.apply_id')):'';
		if(empty($apply_id)){
		$result['err_code'] = 1002;
		$result['data'] = '缺少报名id';
		return $this->ajaxReturn($result);}
		$com['isvalid'] = true;
		$com['customer_id'] = $this->customer_id;
		//商品最新价格
		$c_action['id'] = $apply_id;
		$where_action = array_merge($com,$c_action);
		$latest_price = M('action')->where($where_action)->getField('latest_price');
		$product_id = M('action')->where($where_action)->getField('product_id');
		$minimum_price = M('goods')->where("id=$product_id")->getField('minimum_price');
		$actual_time_price = round(($latest_price-$minimum_price),2); 
		//参与者信息
		$where_bargain = array_merge($com,['action_id'=>$apply_id]);
		$list_bargain_temp = M('bargain')->field('id')->where($where_bargain)->order('id desc')->limit(3)->select();
		if($list_bargain_temp){
		for($list_bargain=[],$j=0;$j<count($list_bargain_temp);$j++){
		$list_bargain[] = M('bargain')->field('product_id,user_id,bargain_price,bargain_time')->find($list_bargain_temp[$j]['id']);
		}}
		foreach($list_bargain as &$vm){ 
		$list_user = $list_w->getwxInfo($vm['user_id'],$this->customer_id);
		if(empty($list_user['subscribe'])){
		$condi['isvalid'] = true;
		$condi['id'] = $vm['user_id'];
		$baseInfo = M(DB_NAME.'.weixin_users',null)->field('weixin_name,weixin_headimgurl')->where($condi)->find();
		$vm['bargain_time'] = str_replace('-','/',$vm['bargain_time']);
		$vm['bargain_user_name'] = $baseInfo['weixin_name'];
		$vm['bargain_user_url'] = $baseInfo['weixin_headimgurl'];
		}else{
		$vm['bargain_time'] = str_replace('-','/',$vm['bargain_time']);
		$vm['bargain_user_name'] = $list_user['nickname'];
		$vm['bargain_user_url'] = $list_user['headimgurl'];
		}}
		if(!$latest_price&&!$list_bargain){
		$result['err_code'] = 1003;
		$result['data'] = '没有数据';
		}else{
		$result['err_code'] = 1000;
		$total_users = M('bargain')->distinct(true)->field('user_id')->where($where_bargain)->select();
		$result['bargain_user_total'] = count($total_users);
		$list_goods['latest_price'] = $latest_price;
		$list_goods['actual_time_price'] = $actual_time_price;
		$list_goods['bargainInfo'] = $list_bargain;
		$result['data'] = $list_goods;}
		return $this->ajaxReturn($result);
		//
	}
	
	/**
	 * 参与者砍价权验证  
	 * customer_id_en,参与者user_id_en,activity_id,apply_id
	 */
	public function checkBargain(){
		$result = array();
		$apply_id = isset($_GET['apply_id'])? trim(I('get.apply_id')):'';
		if(empty($this->user_id)){
			$result['err_code'] = 0;
			$result['data'] = '用户鉴权失败';
			return $this->ajaxReturn($result);
		}
		if(!$apply_id){
			$result['err_code'] = 0;
			$result['data'] = '缺少报名id';
			return $this->ajaxReturn($result);
		}
		$play_number_activity = '';       //参与者对当前活动可参与的砍价次数
		$play_number_goods_global = '';   //单个商品砍价总次数 (全局)
		$play_number_goods = '';          //参与者对单个商品可参与的砍价次数(优先级高于全局)
		$list_activity = M('activity')->field('play_number,play_number_single')->find($this->activity_id);
		$play_number_activity = $list_activity['play_number']; 
		$play_number_goods_global = $list_activity['play_number_single']; 
		$product_id = M('action')->where(['id'=>$apply_id])->getField('product_id');
		$check_user = M('action')->where(['id'=>$apply_id])->getField('user_id');
		if($check_user==$this->user_id){
			$result['is_me']=1;
		}else{
			$result['is_me']=0;
		}
		$play_number_goods = M('goods')->where(['id'=>$product_id])->getField('play_number');
		//0为不限制
		if(empty($play_number_activity)){
			$play_number_activity = 9999999;
			if(empty($play_number_goods)){$play_number_goods = $play_number_goods_global;}
			if(empty($play_number_goods)){$play_number_goods = 9999999;}
			if($play_number_goods>$play_number_activity){$play_number_goods = $play_number_activity;}
		}
		$com['isvalid'] = true;
		$com['customer_id'] = $this->customer_id;
		$c[] ='1=1';
		$c[] = "b.user_id={$this->user_id} and b.isvalid=1 and b.customer_id={$this->customer_id}";
		$times_activity = M('bargain')->alias('b')
		->join("kj_action a on a.id=b.action_id and a.activity_id={$this->activity_id} and a.isvalid=1 and a.customer_id={$this->customer_id}")
		->where($c)
		->count();
		$where_j[] ='1=1';
		$where_j[] = "b.user_id={$this->user_id} and b.product_id=$product_id and b.isvalid=1 and b.customer_id={$this->customer_id}";
		$times_goods_bargain = M('bargain')->alias('b')
		->join("kj_action a on a.id=b.action_id and a.activity_id={$this->activity_id} and a.isvalid=1 and a.customer_id={$this->customer_id}")
		->where($where_j)
		->count();
		$checkInfo = array();
		if(!$times_activity){
			$checkInfo['play_number_activity'] = $play_number_activity;  //剩余参与活动次数
			if($play_number_goods>$play_number_activity){$play_number_goods=$play_number_activity;}
			$checkInfo['play_number_goods'] = $play_number_goods;        //当前商品剩余砍价次数
		}else{
			if($times_activity>=$play_number_activity){
				$checkInfo['play_number_activity'] = 0;  
				$checkInfo['play_number_goods'] = 0;     
			}else{
				$play_number_activity = $play_number_activity-$times_activity;  
				$play_number_goods = $play_number_goods-$times_goods_bargain; 
				if($play_number_goods>$play_number_activity){$play_number_goods=$play_number_activity;}
				$checkInfo['play_number_activity'] = $play_number_activity;
				$checkInfo['play_number_goods'] = $play_number_goods;
			}
		}
		if(!$checkInfo){
			$result['err_code'] = 0;
			$result['data'] = 'null';
		}else{
			if($checkInfo['play_number_activity']>0){
				$result['err_code'] = 1;
				$result['data'] = $checkInfo;
			}else{
				$result['err_code'] = 0;
				$result['data'] = 'null';
			}
		}
		return $this->ajaxReturn($result); 
		//
	}
	
	//发起砍价 customer_id_en,参与者user_id_en,activity_id,apply_id
	public function bargain_action(){
		$result = array();
		$apply_id = isset($_GET['apply_id'])? trim(I('get.apply_id')):'';
		if(empty($this->user_id)){
			$result['err_code'] = 0;
			$result['data'] = '用户鉴权失败';
			return $this->ajaxReturn($result);
		}
		if(!$apply_id){
			$result['err_code'] = 0;
			$result['data'] = '缺少报名id';
			return $this->ajaxReturn($result);
		}
		$play_number_activity = '';       //参与者对当前活动可参与的砍价次数
		$play_number_goods_global = '';   //单个商品砍价总次数 (全局)
		$play_number_goods = '';          //参与者对单个商品可参与的砍价次数(优先级高于全局)
		$list_activity = M('activity')->field('play_number,play_number_single')->find($this->activity_id);
		$play_number_activity = $list_activity['play_number'];
		$play_number_goods_global = $list_activity['play_number_single'];
		$product_id = M('action')->where(['id'=>$apply_id])->getField('product_id');
		$play_number_goods = M('goods')->where(['id'=>$product_id])->getField('play_number');
		//0为不限制
		$com['isvalid'] = true;
		$com['customer_id'] = $this->customer_id;
		if(empty($play_number_activity)){
			$play_number_activity = 9999999;
			if(empty($play_number_goods)){$play_number_goods = $play_number_goods_global;}
			if(empty($play_number_goods)){$play_number_goods = 9999999;}
			if($play_number_goods>$play_number_activity){$play_number_goods = $play_number_activity;}
		}
		/* 砍价权验证 */
		//活动信息
		$where_activity['id'] = $this->activity_id;
		$where_activity = array_merge($com,$where_activity);
		$list_activity = M('activity')->field(true)->where($where_activity)->find();
		if(!$list_activity){
			$result['err_code'] = 0;
			$result['data'] = '该活动已不存在';
			return $this->ajaxReturn($result);
		}
		$activity_status = $list_activity['activity_status'];
		$activity_start_time = str_replace('-','/',$list_activity['activity_start_time']);
		$activity_end_time = str_replace('-','/',$list_activity['activity_end_time']);
		//商品信息
		$where_goods['id'] = $product_id;
		$where_goods = array_merge($com,$where_goods);
		$list_goods = M('goods')->field(true)->where($where_goods)->find();
		if(!$list_goods){
			$result['err_code'] = 0;
			$result['data'] = '该商品已不存在';
			return $this->ajaxReturn($result);
		}
		$start_time = str_replace('-','/',$list_goods['start_time']);
		$end_time = str_replace('-','/',$list_goods['end_time']);
		
		//开始验证  活动状态
		switch($activity_status){//1：未发布；2：进行中；3：已过期；4：已终止；
			case 3:
				$result['err_code'] = 0;
				$result['data'] = '活动已结束';
				return $this->ajaxReturn($result);
			case 4://活动终止不可影响已经报名的商品继续砍价
				//$result['err_code'] = 0;
				//$result['data'] = '活动已终止';
				//return $this->ajaxReturn($result);
		}
		//商品开始砍价时间校正
		if(strtotime($start_time)<=strtotime($activity_start_time)){
			$start_time = $activity_start_time;
		}
		//商品结束砍价时间校正
		if(strtotime($end_time)>strtotime($activity_end_time)){
			$end_time = $activity_end_time;
		}
		if(time()<strtotime($start_time)){//砍价还没开始
			$result['err_code'] = 0;
			$result['data'] = '砍价未开始';
			return $this->ajaxReturn($result);
		}
		if(time()>strtotime($end_time)){//活动进行中
			$result['err_code'] = 0;
			$result['data'] = '砍价已结束';
			return $this->ajaxReturn($result);
		}
		
		//砍价剩余次数验证
		$c[] ='1=1';
		$c[] = "b.user_id={$this->user_id} and b.isvalid=1 and b.customer_id={$this->customer_id}";
		$times_activity = M('bargain')->alias('b')
		->join("kj_action a on a.id=b.action_id and a.activity_id={$this->activity_id} and a.isvalid=1 and a.customer_id={$this->customer_id}")
		->where($c)
		->count();
		$where_j[] ='1=1';
		$where_j[] = "b.user_id={$this->user_id} and b.product_id=$product_id and b.isvalid=1 and b.customer_id={$this->customer_id}";
		$times_goods_bargain = M('bargain')->alias('b')
		->join("kj_action a on a.id=b.action_id and a.activity_id={$this->activity_id} and a.isvalid=1 and a.customer_id={$this->customer_id}")
		->where($where_j)
		->count();
		if($times_activity>=$play_number_activity){
			$result['err_code'] = 0;
			$result['data'] = '参与当前活动的砍价次数已满';
			return $this->ajaxReturn($result);
		}else{
			$play_number_goods = $play_number_goods-$times_goods_bargain;
			if($play_number_goods<=0){
				$result['err_code'] = 0;
				$result['data'] = '对当前商品的砍价次数已满';
				return $this->ajaxReturn($result);
			}
		}
		//底价验证
		$latest_price = M('action')->where("id=$apply_id")->getField('latest_price');
		$minimum_price = M('goods')->where("id=$product_id")->getField('minimum_price');
		if($latest_price<=$minimum_price){
			$result['err_code'] = 0;
			$result['data'] = '已砍到最低价,不能再砍了';
			return $this->ajaxReturn($result);
		}
		//可以砍价
		$bargain_time = date('Y-m-d H:i:s',time());
		$activity_id = $list_action['activity_id'];
		$bargain_price = mt_rand($list_activity['bargain_min_price'],$list_activity['bargain_max_price']);
		$bargain_price = round(doubleval($bargain_price)/100,2);
		$money_gap = $latest_price-$minimum_price;
		if($bargain_price>$money_gap){$bargain_price = $money_gap;}
		$list['action_id'] = $apply_id;
		$list['product_id'] = $product_id;
		$list['user_id'] = $this->user_id;
		$list['bargain_price'] = $bargain_price;
		$list['bargain_time'] = $bargain_time;
		$list['isvalid'] = 1;
		$list['customer_id'] = $this->customer_id;
		$resultInt = M('bargain')->data($list)->filter('htmlentities')->add();
		if(!$resultInt){
			$result['err_code'] = 0;
			$result['data'] = '砍价失败';
		}else{//砍价成功
			$resultDec = M('action')->where(['id'=>$apply_id])->setDec('latest_price',$bargain_price);
			if($resultDec){
				$activity['id']=$this->activity_id;
				$activity['customer_id']=$this->customer_id;
				$res1=M("activity")->where($activity)->find();
				$map_coupons['customer_id']=$this->customer_id;
				$map_coupons['isvalid']=1;
				if($res1['coupon_id']){
					$map_coupons['id']=$res1['coupon_id'];
					$coupons=M()->table(DB_NAME.".weixin_commonshop_coupons")->where($map_coupons)->find();
				}
				if($res1['is_coupon']==1){
					$map_bargain['action_id']=$apply_id;
					$map_bargain['customer_id']=$this->customer_id;
					$map_bargain['user_id']=$this->user_id;
					$bargain=M("bargain")->field("sum(is_coupon)")->where($map_bargain)->select();
					if($bargain[0]['sum(is_coupon)']){
						$result['error_coupon']=1001;
						$result['coupon']="已经获得过优惠券";
					}else{
						$bargain_map['id']=$resultInt;
						$bargain_save['is_coupon']=1;
						$save_bargain=M("bargain")->where($bargain_map)->save($bargain_save);
						$money=rand($coupons['MinMoney'],$coupons['MaxMoney']);
						$result['error_coupon']=1000;
						$result['coupon']=array("money"=>$money,"coupon_id"=>$res1['coupon_id']);
					}
				}else if($res1['is_coupon']==2){
					if(strtotime($coupons['getEndTime'])>=time()){
						$rand1=rand(1,100);
						$rand2=rand($res1['coupon_probability'],100);
						if($rand2>$rand1){
							$bargain_map['id']=$resultInt;
							$bargain_save['is_coupon']=1;
							$save_bargain=M("bargain")->where($bargain_map)->save($bargain_save);
							$money=rand($coupons['MinMoney'],$coupons['MaxMoney']);
							$result['error_coupon']=1000;
							$result['coupon']=array("money"=>$money,"coupon_id"=>$res1['coupon_id']);
						}else{
							$result['error_coupon']=1001;
							$result['coupon']="没有获得优惠券";
						}
					}else{
						$result['error_coupon']=1001;
						$result['coupon']="没有优惠券";
					}
				}else{
					$result['error_coupon']=1001;
					$result['coupon']="没有优惠券";
				}
				if($result['error_coupon']==1000){
					// $map_coupons['id']=I("get.coupon_id");
					// $coupons=M()->table(DB_NAME.".weixin_commonshop_coupons")->where($map_coupons)->find();
					$map_couponusers['user_id']=$this->user_id;
					$couponusers=M()->table(DB_NAME.".weixin_commonshop_couponusers")->where($map_couponusers)->find();
					$data['code']="d".$this->user_id.time();
					$data['user_id']=$this->user_id;
					$data['customer_id']=$this->customer_id;
					$data['Money']=$money;
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
					$data['coupon_id']=$coupons['id'];
					$data['is_coupon_inentityUse']=0;
					$data['use_roles']='1_2_3_4_5_6';
					$data['coupon_use_inentity']="1_-1";
					$data['is_receive']=1;
					$data['startline']=date("Y-m-d H:i:s");
					M()->table(DB_NAME.".weixin_commonshop_couponusers")->data($data)->add();
				}
				$result['err_code'] = 1;
				$result['data'] = '砍价成功';
				$result['bargain_price'] = $bargain_price;
				$result['bargain_desc'] = '已成功砍价¥'.$bargain_price.'元';
				$result['bargain_time'] = $bargain_time;
				$this->tomsg($product_id,$apply_id,$bargain_price);
			}else{
				$result['err_code'] = 0;
				$result['data'] = '砍价出错';
			}
		}
		return $this->ajaxReturn($result);
		//	
	}
	public function tomsg($product_id,$apply_id,$bargain_price){
		//模板：用户名称+参加+产品名称+帮砍xx元！
		//亲，你的XXX好友帮你在XXXX砍价活动中砍了XX元
		$bj_c = new \Vendor\lib\Com();
		$u_name = M()->table(DB_NAME.'.weixin_users')->where(['id'=>$this->user_id])->getField('weixin_name');
		if(empty($u_name)){
		$userdatas = $bj_c->getwxInfo($this->user_id,$this->customer_id);
		$u_name = $userdatas['nickname'];}
		$product_no = M('goods')->where(['id'=>$product_id])->getField('product_no');
		$p_name = M()->table(DB_NAME.'.weixin_commonshop_products')->where(['id'=>$product_no])->getField('name');
		$a_cc = M('action')->field('user_id,activity_id,product_id')->where(['id'=>$apply_id])->find();
		$apply_user_id = $a_cc['user_id'];
		$activity_id = $a_cc['activity_id'];
		$product_id = $a_cc['product_id'];
		$activity_title = M('activity')->where(['id'=>$activity_id])->getField('activity_title');
		//$content = $u_name.'参加 '.$p_name.'帮砍'.$bargain_price.'元!';
		$content = '亲，你的'.$u_name.'好友帮你在'.$activity_title.'砍价活动中砍了'.$bargain_price.'元';
		$content = $content.' <a href=\"'.$this->http.'/weixinpl/haggling/front/web/enrolled.html?customer_id_en='.$this->customer_id_en.'&user_id_en='.encode_wsy($apply_user_id).'&id='.$product_id.'&idx='.$product_id.'&apply_id='.$apply_id.'&activity_id='.$activity_id.'\">点此查看</a>';
		$bj_c->sendb($content,$apply_user_id,$this->customer_id);
	}

	/**
	 * @desc 获取活动商品状态信息
	 * @param customer_id,user_id_en,activity_id,id
	 */
	public function comInfo(){
		$product_id = isset($_GET['id'])? intval(I('get.id')):'';
		if(!$product_id){
			$result['err_code'] = 1004;
			$result['data'] = '缺少商品id';
			return $this->ajaxReturn($result);
		}
		$result = $this->getInfo($this->user_id,$product_id);
		return $this->ajaxReturn($result,'json');
		//
	}
	
	/**
	 * @desc 获取活动商品公共信息
	 * @param customer_id,user_id_en,activity_id,id
	 */
	public function getInfo($user_id,$product_id){
		$result = array();
		$com['isvalid'] = true;
		$com['customer_id'] = $this->customer_id;
		if(empty($user_id)){
			$result['err_code'] = 1004;
			$result['data'] = '用户鉴权失败';
			return $result;
		}
		if(!$product_id){
			$result['err_code'] = 1004;
			$result['data'] = '缺少商品id';
			return $result;
		}
	
		$where_c['activity_id'] = $this->activity_id;
		$where_c['product_id'] = $product_id;
		$where_c['user_id'] = $user_id;
		$where_c = array_merge($com,$where_c);
		$result_apply = M('action')->where($where_c)->count('id');
		if($result_apply){$apply_id = M('action')->where($where_c)->order('id desc')->getField('id');}
		$where_activity['id'] = $this->activity_id;
		$where_activity = array_merge($com,$where_activity);
		$list_activity = M('activity')->field('activity_status,activity_start_time,activity_end_time,is_care')->where($where_activity)->find();
		$where_goods['id'] = $product_id;
		$where_goods['activity_id'] = $this->activity_id;
		$where_goods = array_merge($com,$where_goods);
		$list_goods = M('goods')->field('product_no,product_num,inventory,price,buy_price,minimum_price,start_time,end_time')->where($where_goods)->find();
		$activity_expire_code = $list_activity['activity_status']; //2进行中,3已过期,4已终止
		$activity_start_time = str_replace('-','/',$list_activity['activity_start_time']);
		$activity_end_time = str_replace('-','/',$list_activity['activity_end_time']);
		$goods_start_time = str_replace('-','/',$list_goods['start_time']); //商品砍价开始时间
		$goods_end_time = str_replace('-','/',$list_goods['end_time']); //商品砍价结束时间
		/*是否需要关注*/
		$result['is_care']=$list_activity['is_care'];
		/*是否需要关注*/
		$result['qr_code'] = $this->http."/weixinpl/mshop/product_detail.php?customer_id=".$this->customer_id_en."&pid=".$list_goods['product_no'];
		//活动状态验证
		switch($activity_expire_code){
			case 2://活动进行中
				$result['activity_expire_code'] = 2;
				$result['activity_expire_desc'] = '活动进行中';
				//商品状态
				if(time()<strtotime($goods_start_time)){
					$result['goods_expire_code'] = 1;
					$result['goods_expire_desc'] = '商品砍价未开始';
				}
				if(time()>=strtotime($goods_start_time)&&time()<=strtotime($goods_end_time)){
					$result['goods_expire_code'] = 2;
					$result['goods_expire_desc'] = '商品砍价进行中';
				}
				if(time()>strtotime($goods_end_time)){
					$result['goods_expire_code'] = 3;
					$result['goods_expire_desc'] = '商品砍价已结束';
				}
				//商品砍价开始时间校正
				if(strtotime($goods_start_time)<=strtotime($activity_start_time)){
					$result['goods_start_time'] = $activity_start_time;
				}else{
					$result['goods_start_time'] = $goods_start_time;
				}
				//商品砍价结束时间校正
				if(strtotime($goods_end_time)>strtotime($activity_end_time)){
					$result['goods_end_time'] = $activity_end_time;
				}else{
					$result['goods_end_time'] = $goods_end_time;
				}
				break;
			case 3://活动已结束
				$result['activity_expire_code'] = 3;
				$result['activity_expire_desc'] = '活动已结束';
				$result['goods_expire_code'] = 3;
				$result['goods_expire_desc'] = '商品砍价已结束';
				break;
				// return $result;
			case 4://活动已终止
				$result['activity_expire_code'] = 4;
				$result['activity_expire_desc'] = '活动已终止';
				$result['goods_expire_code'] = 4;
				$result['goods_expire_desc'] = '商品砍价已终止';
				// return $result;
				break;
		}
		//活动信息
		$result['nowtime'] = date('Y/m/d H:i:s',time());
		$result['activity_id'] = $this->activity_id;
		$result['activity_start_time'] = $activity_start_time;
		$result['activity_end_time'] = $activity_end_time;
		//商品信息
		$result['id'] = $product_id;
		$result['product_no'] = $list_goods['product_no']; //商品编号
		$result['product_num'] = $list_goods['product_num']; //参与活动商品数量
		$result['inventory'] = $list_goods['inventory']; //库存 (实时剩余可供砍价的商品数量)
		$result['price'] = $list_goods['price'];
		$result['buy_price'] = $buy_price = $list_goods['buy_price'];
		$result['minimum_price'] = $list_goods['minimum_price'];
		//用户报名状态
		if($apply_id){//已报名
			$result['apply_status'] = 1;
			$result['apply_desc'] = '已报名';
			$result['apply_id'] = $apply_id;
			$result['latest_price'] = $latest_price = M('action')->where("id=$apply_id")->getField('latest_price');
			//报名id是否已完成领取
			if($latest_price<=$buy_price){
				$w_order['isvalid'] = true;
				$w_order['customer_id'] = $this->customer_id;
				$w_order['user_id'] = $user_id;
				$w_order['action_id'] = $apply_id;
				$w_order['activity_id'] = $this->activity_id;
				$order_result = M('order')->field('id,is_pay')->where($w_order)->find();
				if(!$order_result){
					$result['completed_status'] = -1;
					$result['completed_desc'] = '未领取';
				}else{
					if(empty($order_result['is_pay'])){
						$result['completed_status'] = -1;
						$result['completed_desc'] = '未领取';
					}else{
						$result['completed_status'] = 1;
						$result['completed_desc'] = '已领取';
					}
				}
			}else{
				$result['completed_status'] = -1;
				$result['completed_desc'] = '未领取';
			}
		}else{
			$result['apply_status'] = 0;
			$result['share_desc'] = '未报名';
		}
		return $result;
		//
	}
	
	//获取商品轮播图片
	public function getImgs($product_id){
		$product_no = M('goods')->where("id=$product_id")->getField('product_no');
		$where_c1['id'] = $product_no;
		$where_c1['isvalid'] = true;
		$where_c1['customer_id'] = $this->customer_id;
		$list_goods_pl = M()->table(DB_NAME.'.weixin_commonshop_products')->field('id as shop_id')->where($where_c1)->find();
		$where_c2['isvalid'] = 1;
		$where_c2['customer_id'] = $this->customer_id;
		$where_c2['product_id'] = $list_goods_pl['shop_id'];
		$list_imgs = M()->table(DB_NAME.'.weixin_commonshop_product_imgs')->field('imgurl')->where($where_c2)->select();
		$result_img = [];
		for($j=0;$j<count($list_imgs);$j++){$result_img[] = BARGAIN_URL.$list_imgs[$j]['imgurl'];}
		if(!$result_img){$result_img[] = BARGAIN_URL.'/weixinpl/haggling/back/Public/ass/images/mrkj.jpg';}
		return $result_img;
	}
	
	//参与者砍价页面信息 customer_id_en,activity_id,apply_id 
	public function getBargainInfo_header(){
		$result = array();
		$apply_id = isset($_GET['apply_id'])? trim(I('get.apply_id')):'';
		if(empty($apply_id)){
			$result['err_code'] = 0;
			$result['data'] = '缺少报名id';
			return $this->ajaxReturn($result); 
		}
		$user_id = M('action')->where(['id'=>$apply_id])->getField('user_id');
		$product_id = M('action')->where(['id'=>$apply_id])->getField('product_id');
		$activity_id = M('action')->where(['id'=>$apply_id])->getField('activity_id');
		$list_goods = $this->getInfo($user_id,$product_id);
		$goodsInfo[] = $list_goods;
		$goodsInfo = array_map(function($datax){
		$datax['name'] = M()->table(DB_NAME.'.weixin_commonshop_products')->where(['id'=>$datax['product_no']])->getField('name');
		return $datax;
		},$goodsInfo);
		$list_goods_new[] = $goodsInfo[0];
		$temp = array(); //商品信息
		$i = new \Vendor\lib\Com();
		while(list($ven,$data)=each($list_goods_new)){
		$temp['activity_id'] = $data['activity_id'];
		$temp['id'] = $data['id'];
		$temp['name'] = $data['name'];
		$temp['price'] = $data['price'];
		$temp['buy_price'] = $data['buy_price'];
		$temp['minimum_price'] = $data['minimum_price'];
		$temp['nowtime'] = $data['nowtime'];
		$temp['goods_start_time'] = $data['goods_start_time'];
		$temp['goods_end_time'] = $data['goods_end_time'];
		$temp['activity_start_time'] = $data['activity_start_time'];
		$temp['activity_end_time'] = $data['activity_end_time'];}
		$exp_userInfo = $i->getwxInfo($user_id,$this->customer_id);
		$exp_userInfo_wx['nickname'] = $exp_userInfo['nickname'];
		$exp_userInfo_wx['headimgurl'] = $exp_userInfo['headimgurl'];
		$listInfo['goodsInfo'] = $temp;
		$listInfo['goodsImgs'] = $this->getImgs($product_id);
		$listInfo['exp_userInfo'] = $exp_userInfo_wx;
		$map_activity['id']=$activity_id;
		$activitys=M("activity")->field("ruler")->where($map_activity)->find();
		$listInfo['ruler'] = $activitys['ruler'];
		if(!$listInfo){
		$result['err_code'] = 0;
		$result['data'] = '没有数据';
		}else{
		$result['err_code'] = 1;
		$result['data'] = $listInfo;
		} return $this->ajaxReturn($result);
		//
	} 
	
	//获取用户关注状态
	public function getSubscribe(){
		$result = [];
		$i = new \Vendor\lib\Com();
		if(empty($this->user_id)){
			$result['err_code'] = 1004;
			$result['data'] = '缺少参数user_id_en';
		    return $this->ajaxReturn($result);
		}
		$ins = $i->getwxInfo($this->user_id,$this->customer_id);
		if($ins['subscribe']){
			$result['err_code'] = 1000;
			$result['data'] = '已关注';
		}else{
			//$first['isbind']=1;
        	$first['isvalid']=1;
        	$first['customer_id']=$this->customer_id;
        	$first_res=M()->table(DB_NAME.'.weixin_baseinfos')->field('id')->where($first)->find();
        	$second['foreign_id']=$first_res['id'];
        	$second['type']=3;
        	$second['isvalid']=1;
        	$second_res=M()->table(DB_NAME.'.images')->field('imgurl')->where($second)->find();
			$result['err_code'] = 1001;
			$result['data'] = 'https://'.$_SERVER['HTTP_HOST']."/WeixinManager/qrsbig/".$second_res['imgurl'];
		}
		return $this->ajaxReturn($result);
	}
	
	//用户访问量统计 customer_id_en,activity_id
	public function access_count(){
		$result = array();
		$nowtime = date('Y/m/d H:i:s',time());
		$list_activity = M('activity')->field('customer_id')->find($this->activity_id);
		if($list_activity['customer_id']<>$this->customer_id){
		$result['err_code'] = 0;
		$result['data'] = '访问出错';
		return $this->ajaxReturn($result);
		}
		if(session('activity_id')!=$this->activity_id){
		unset($_SESSION['int']);
		}
		if(session('?expire')&&time()>session('expire')){
		unset($_SESSION['int']);
		}else{
	    session('expire',time()+60*3);
		}
		if(!session('?int')){
		$get_c['isvalid'] = 1;
		$get_c['activity_id'] = $this->activity_id;
		$get_c['customer_id'] = $this->customer_id;
		$list_access = M('access')->field('id,access_total')->where($get_c)->find();
		if(empty($list_access['id'])){
		$access_total = 1;
		$ins['isvalid'] = true;
		$ins['access_total'] = $access_total;
		$ins['latest_access_time'] = $nowtime;
		$ins['activity_id'] = $this->activity_id;
		$ins['customer_id'] = $this->customer_id;
		session('int',$access_total);
		session('expire',time()+60*3);
		session('activity_id',$this->activity_id);
		M('access')->data($ins)->add();
		}else{
		$access_total = $list_access['access_total'];
		if(!$access_total){
		$access_total = 1;
		}else{
		++$access_total;
		}
		$result_insert = M('access')->data(array('access_total'=>$access_total,'latest_access_time'=>$nowtime))->where($get_c)->save();
		if(!$result_insert){
		$result['err_code'] = 0;
		$result['data'] = '访问计数出错';
		return $this->ajaxReturn($result);
		}else{
		session('int',$access_total);
		session('expire',time()+60*3);
		session('activity_id',$this->activity_id);
		}}}
		$result['err_code'] = 1;
		$result['data'] = '您是第'.$_SESSION['int'].'位访客!';
		return $this->ajaxReturn($result);
		//
	}
	
	//产品点击量统计 customer_id_en,activity_id,id
	public function goods_hits(){
		$result = array();
		$nowtime = date('Y/m/d H:i:s',time());
		$product_id = isset($_GET['id'])? trim(I('get.id')):'';
		if(!$product_id){
		$result['err_code'] = 0;
		$result['data'] = '缺少商品id';
		return $this->ajaxReturn($result);
		}
		$list_com['isvalid'] = true;
		$list_com['customer_id'] = $this->customer_id;
		$list_goods['activity_id'] = $this->activity_id;
		$list_goods['product_id'] = $product_id;
		$where_goods = array_merge($list_com,$list_goods);
		$list_hits = M('hits')->field('id,hits')->where($where_goods)->find();
		if(!$list_hits['id']){
		$hits = 1;
		$list['hits'] = $hits;
		$list['isvalid'] = 1;
		$list['product_id'] = $product_id;
		$list['latest_click_time'] = $nowtime;
		$list['activity_id'] = $this->activity_id;
		$list['customer_id'] = $this->customer_id;
		M('hits')->data($list)->add();
		}else{
		$hits = $list_hits['hits'];
		$result_s = M('hits')->where($where_goods)->setInc('hits');
		if($result_s){++$hits;}
		}
		$result['err_code'] = 1;
		$result['data'] = '当前商品点击量为'.$hits;
		return $this->ajaxReturn($result);
		//
	} 
	
}




