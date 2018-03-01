<?php
/**
 * Created by Zend
 * Author: Tung(binn)
 * return $newName/false
 */
class Upload{
	public $_dir;      
	public $_maxsize; 
	public $_allowExt;
	public function up_load($input_key){
		$file = $_FILES[$input_key]; 
		$fileExt = $this->getExt($file['name']);
		if(!$this->isAllowExt($fileExt)){return false;}
		if(!$this->getSize($file['size'])){return false;}
		$dir = $this->mk_dir();
		if(!$dir){return false;}
		$newName = $this->randchar().'.'.$fileExt;  
		if(!_move_uploaded_file($file['tmp_name'],$dir.'/'.$newName)){ return false;}
		return $newName;
	}
	protected function getExt($fileName){return end(explode('.',$fileName));}
	public function setDir($dir){$this->_dir = $dir;}
	protected function getSize($size){return $size <= $this->_maxsize*1024*1024;}
	protected function randchar(){return date("YmdHis").rand(10000,99999);}
	public function setExt($fileExt){$this->_allowExt = $fileExt;}
	public function setSize($sizeNum){$this->_maxsize = $sizeNum;}
	protected function isAllowExt($fileExt){return in_array(strtolower($fileExt),explode(',',$this->_allowExt));}   
	public function check_key($input_key){if(!isset($_FILES[$input_key]['tmp_name'])){return false;}return true;}
    protected function mk_dir(){if(empty($this->_dir)){$path = './upload/'.date('Y/md');}
    else{$path = $this->_dir;}
    if(is_dir($path)||mkdir($path)){return $path;}
    else{return false;}
    }
    
}
