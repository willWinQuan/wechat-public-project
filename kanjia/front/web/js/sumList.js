$(function(){
	// console.log($(window).width());
//	适配苹果5s
	if($(window).width()<750){
		$('.user-img').css({"margin-right":".2rem"});
		$(".helpList-right").css({"font-size":".3rem"});
	};
})


//获取url上的参数
customer_id_en = getPar_WSY('customer_id_en');
user_id_en = getPar_WSY('user_id_en');
activity_id = getPar_WSY('activity_id');
//默认展示数据
var vm=new Vue({
	el:'.main',
	data:{
		listData:'',
		index:"",
		l_display:"none",
		p_index:"",
		p_user_name:"",
		p_user_pic:"",
		p_bargain_price:"",
		p_bargain_time:"",
		p_display:"none"
	}
})

//获取数据
function getListData(){
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/zpq/get_activity_ranking_list',
	    data={customer_id_en:customer_id_en,user_id_en:user_id_en,activity_id:activity_id},
	    type="get",
        timer="",//定时器     
        vm=this.vm;
        
//调用公共AJAX函数
   	ajax(url,data,type,function(result){
   		if(result != null){
   			vm.listData=result.data;
   			console.log(vm.listData)
   		}else{
   			vm.listData=null;
   		}
			
	});	
   		
// clearInterval(timer);
}

function getPersonalData(){
	var u_url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/zpq/get_activity_ranking_list_by_me',
	    data={customer_id_en:customer_id_en,user_id_en:user_id_en,activity_id:activity_id},
	    type="get",
        timer="",//定时器     
        vm=this.vm;
	ajax(u_url,data,type,function(result1){ 
		   // console.log(result1)
		    if(result1!=null){
            vm.l_display="block";
          	vm.p_display="block";
            vm.p_index=result1[0].number;
         	vm.p_user_name=result1[0].user_name;
         	vm.p_user_pic=result1[0].user_pic;
         	vm.p_bargain_price=result1[0].bargain_price;
         	vm.p_bargain_time=result1[0].bargain_time;
         	}
         	vm.l_display="block";
    });

}

getListData();
getPersonalData();
$("body").css({"opacity":"1"});
$(".list-nodata").css({"opacity":"1"});
setInterval("getListData()",3000);
setInterval("getPersonalData()",3000);

