<?php
/**
 * Created by Zend
 * Author: Tung(binn)
 * Date: 2016/10/5
 +--------------------------------
 */
class Page{	
	public $rowNums;     
	public $show;     
    public $pageNums;    
    public $page;      
    public $firstRow;
	public function __construct($rowNums,$show){ 		
		$this->show = $show;        
		$this->rowNums = $rowNums;
		$this->pageNums = ceil($this->rowNums/$this->show);   
		$this->page = empty($_GET['page'])?1:$_GET['page']; 
		$this->firstRow = ($this->page-1)*$this->show;      
	}
	public function prePage(){
		if($this->page>1){
		return $this->page-1;
		}else{
		return 1;
		}
	}
	public function nextPage(){
		if($this->page<$this->pageNums){
		return $this->page+1;
		}else{
		return $this->pageNums;  
		}
	}
	public function pageInfo($condition=null){ 
		if(empty($condition)){
		$strPage = '';
		if($this->page>1){
		$strPage.="<li><a href='?page=1'>首页</a></li>";
		$strPage.="<li><a href='?page=".$this->prePage()."'>上一页</a></li>";
		}
		$begin = ($this->page<=4)?1:$this->page-4;
		$end = ($this->page>$this->pageNums-4)?$this->pageNums:$this->page+4;
		if($this->page>=6){
		$strPage .= "<li><a href='?page=1'>1</a></li>";
		}
		if($this->page>6){
		$strPage .= "<li><a>...</a></li>";
		}
		for($i=$begin;$i<=$end;$i++){
		if($i==$this->page){
		$strPage .= "<li><a href='?page=".$i."' style='color:red;'>".$i."</a></li>";
		}else{
		$strPage .= "<li><a href='?page=".$i."'>".$i."</a></li>";
		}
		}
		if($this->page<$this->pageNums-5){
		$strPage .= "<li><a>...</a><li>";
		}
		if($this->page<$this->pageNums-4){
		$strPage .= "<li><a href='?page=".$this->pageNums."'>".$this->pageNums."</a></li>";
		}	
		if($this->page<$this->pageNums){
		$strPage.="<li><a href='?page=".$this->nextPage()."'>下一页</a></li>";
		$strPage.="<li><a href='?page=".$this->pageNums."'>尾页</a></li>";
		}		
		}else{
		$strPage = '';	
		if($this->page>1){
		$strPage.="<li onclick='pagehref(this)' page='1' condition='".$condition."' >首页</li>";
		$strPage.="<li onclick='pagehref(this)' page=".$this->prePage()." condition='".$condition."' >上一页</li>";
		}
		$begin = ($this->page<=4)?1:$this->page-4;
		$end = ($this->page>$this->pageNums-4)?$this->pageNums:$this->page+4;
		if($this->page>=6){
		$strPage .= "<li onclick='pagehref(this)' page='1' condition='".$condition."' >1</li>";
		}	
		if($this->page>6){
		$strPage .= "<li><a>...</a></li>";
		}		
		for($i=$begin;$i<=$end;$i++){
		if($i==$this->page){
		$strPage .= "<li onclick='pagehref(this)' page=".$i." condition='".$condition."' style='color:red;' >".$i."</li>";
		}else{
		$strPage .= "<li onclick='pagehref(this)' page=".$i." condition='".$condition."' >".$i."</li>";
		}
		}		
		if($this->page<$this->pageNums-5){
		$strPage .= "<li><a>...</a></li>";
		}
		if($this->page<$this->pageNums-4){
		$strPage .= "<li onclick='pagehref(this)' page=".$this->pageNums." condition='".$condition."' >".$this->pageNums."</li>";
		}			
		if($this->page<$this->pageNums){
		$strPage.="<li onclick='pagehref(this)' page=".$this->nextPage()." condition='".$condition."' >下一页</li>";
		$strPage.="<li onclick='pagehref(this)' page=".$this->pageNums." condition='".$condition."' >尾页</li>";				
		}			
		} return $strPage;
	}   		
}

