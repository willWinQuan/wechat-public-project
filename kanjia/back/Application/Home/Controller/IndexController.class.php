<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller{
    public function _empty(){ 
    	$this->display("Public:404");
    }
    public function index(){
    	//...
    }
  
}

