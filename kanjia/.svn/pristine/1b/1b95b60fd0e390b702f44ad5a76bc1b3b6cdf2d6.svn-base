<?php
namespace Vendor\lib;
class Info{
	/**
	 * @param Info
	 */
	public function incc(){
		return false;
	}
	/**************************************************************/
	public function usermsg($text,$user_id,$customer_id){
		import('lib.Inc');
		$ss = new \Vendor\lib\Inc();
		$intr = explode('@',$this->fortest($user_id,$customer_id));
		$mdata['content'] = $text;
		$kdata['touser'] = $intr[0];
		$kdata['msgtype'] = 'text';
		$kdata['text'] = $mdata;
		$content = json_encode($kdata);
		$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$intr[1];
		$ss->p_curl($url,$content);
	
	}
	/* fortest ------ */
	public function fortest($user_id,$customer_id){
		vendor('lib.Weixincomm','','.class.php');
		$wei = new \Weixincomm();
		$op=M()->table(DB_NAME.'.weixin_users')->field('weixin_fromuser')->where("id=$user_id")->find();
		return $op['weixin_fromuser'].'@'.$wei->getWxAccessToken($customer_id);
	}

	
}



