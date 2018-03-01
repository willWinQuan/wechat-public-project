<?php
namespace Home\Controller;
session_start();
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET,OPTIONS');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header('Content-type:text/html;charset=utf-8');
use GuzzleHttp\json_decode;
#ini_set('date.timezone','Asia/Shanghai');
class WxController extends \Think\Controller{
	public function _empty(){
		//空操作
		$this->display("Public:404");
	}
	public $user_id;
	public $user_id_en;
	public $customer_id;
	public $customer_id_en;
	public $activity_id;
	public function _initialize(){
		if(!isset($_GET['customer_id_en'])||!isset($_GET['activity_id'])){
			//return false;
			//return $this->error('缺少参数customer_id_en');
			exit('缺少参数customer_id_en或activity_id');
		}
		$data = I('request.','','trim');
		$customer_id = $data['customer_id_en'];
		$this->customer_id = is_numeric($customer_id)? $customer_id:decode_wsy($customer_id);
		$this->customer_id_en = encode_wsy($this->customer_id);
		$this->user_id = isset($data['user_id_en'])? decode_wsy($data['user_id_en']):'';
		$this->user_id_en = encode_wsy($this->user_id);
		$this->activity_id = $data['activity_id'];
		$_SESSION['customer_id'] = $this->customer_id;
	}
	
	//网页授权
	public function getinfo(){
		$customer_id = $this->customer_id;
		$web_time = isset($_GET['web_time'])? trim(I('get.web_time')):'';
		if(!$customer_id||!$web_time){return false;}
		$arrayUrl = explode('?',$_SERVER['HTTP_REFERER']); 
		$_SESSION['pre_url'] = $arrayUrl[0];
		$c = new \Vendor\lib\Com(); 
		$Rt=$c->WxComm($customer_id,1); 
		$redirect_uri = 'https://'.$_SERVER['HTTP_HOST'].'/weixinpl/haggling/back/index.php/home/wx/getopenid?customer_id_en='.$this->customer_id_en.'&web_time='.$web_time.'&activity_id='.$this->activity_id;    
		if(isset($_GET['id'])){$redirect_uri .= '&id='.trim(I('get.id'));}		
		if(isset($_GET['idx'])){$redirect_uri .= '&idx='.trim(I('get.idx'));}
		if(isset($_GET['apply_id'])){$redirect_uri .= '&apply_id='.trim(I('get.apply_id'));}
		$redirect_uri = urlencode($redirect_uri);
		$interfaceUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$Rt.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';   
		header("location:".$interfaceUrl);
	}
	
	//获取用户openid
	public function getopenid(){
		$data = I('request.','','trim');
		$web_time = $data['web_time'];
		$pre_url = $_SESSION['pre_url'];
		$c = new \Vendor\lib\Com();
		$list_a = explode('@',$c->WxComm($this->customer_id));
		$i = new \Vendor\lib\Inc();
		$interfaceUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$list_a[0].'&secret='.$list_a[1].'&code='.$data['code'].'&grant_type=authorization_code';         
		$list_r = json_decode($i->gethttp($interfaceUrl));
		if(!$list_r){return false;}
		$openid = $list_r->openid;
		$where['isvalid'] = true;
		$where['weixin_fromuser'] = $openid;
		$where['customer_id'] = $this->customer_id;
		$font = '/weixinpl/haggling/front/web/index.html';
		$result = M()->table(DB_NAME.'.weixin_users')->field('id')->where($where)->select();
		if($result){ 
		$_SESSION["fromuser_".$this->customer_id]=$openid;
		$_SESSION["user_id_".$this->customer_id]=$result[0]['id'];
		$_SESSION["customer_id"]=$this->customer_id;
		$id = '';
		foreach($result as $key=>$v){
		if($v['id']){$id = $v['id']; break;}
		} $this->user_id_en = encode_wsy((string)$id);
		}else{
		$val = [];
		$val['isvalid'] = true;
		$val['weixin_fromuser'] = $openid;
		$val['customer_id'] = $this->customer_id;
		$val['createtime'] = date('Y-m-d H:i:s',time());
		$resultInt = M()->table(DB_NAME.'.weixin_users')->data($val)->add();
		if(!$resultInt){return $this->ajaxReturn(['err_code'=>'添加用户失败']);}
		$_SESSION["fromuser_".$this->customer_id]=$openid;
		$_SESSION["user_id_".$this->customer_id]=$resultInt;
		$_SESSION["customer_id"]=$this->customer_id;
		$this->user_id_en = encode_wsy((string)$resultInt);
		} if(empty($pre_url)){$pre_url = BARGAIN_URL.$font;}
		$redirectUrl = $pre_url.'?customer_id_en='.$this->customer_id_en.'&user_id_en='.$this->user_id_en.'&web_time='.$web_time.'&activity_id='.$this->activity_id;
		if(isset($_GET['id'])){$redirectUrl .= '&id='.trim(I('get.id'));}
		if(isset($_GET['idx'])){$redirectUrl .= '&idx='.trim(I('get.idx'));}
		if(isset($_GET['apply_id'])){$redirectUrl .= '&apply_id='.trim(I('get.apply_id'));}
		header("location:".$redirectUrl);
	}
	
	//微信js-sdk参数
	public function WxDisplay(){
		$j = array();
		$timestamp = time();
		$i = new \Vendor\lib\Com();
		$customer_id = $this->customer_id;
		if(empty($customer_id)){
		$j['err_code'] = 1002;
		$j['data'] = '鉴权失败';
		return $this->ajaxReturn($j,'json');
		} return $this->ajaxReturn($i->weixinDisplay($customer_id),'json');
	}
	
	
	
	
}



