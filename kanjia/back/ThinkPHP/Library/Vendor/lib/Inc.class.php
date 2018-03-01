<?php
namespace Vendor\lib;
class Inc{
	/**
	 * @param fkn
	 */
	public function incc(){return false;}
	public function serachInfo($conditionInfo,$like_field,$temp=[]){
	if(empty($conditionInfo)||!is_array($conditionInfo)){return false;}
	while(list($k,$request)=each($conditionInfo)){
	if($request||$request=='0'){
	if($k==$like_field)
	{
	$temp[$k]=[
	'like',
	"%$request%"
    ];
	}
	else{$temp[$k] = $request;}
	}
	} return $temp;
	}
	public function gethttp($url){
		$ch = curl_init ( $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	} 
	public function p_curl($url,$data){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'POST');
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0(compatible;MSIE 5.01;Windows NT 5.0)');
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch,CURLOPT_AUTOREFERER,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,urldecode($data));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$output = curl_exec($ch);
		if(curl_errno($ch)){}
		curl_close($ch);
	}
	public function go_curl($url){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
		$output = curl_exec($ch);
		if(curl_errno($ch)){}
		curl_close($ch);
		return $output;
	}

}

