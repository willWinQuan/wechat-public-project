//获取url上的参数
customer_id_en = getPar_WSY('customer_id_en');
user_id_en = getPar_WSY('user_id_en');
activity_id = getPar_WSY('activity_id');
apply_id = getPar_WSY('apply_id'); 
console.log(apply_id);
//默认展示数据
var vm=new Vue({
	el:'.main',
	data:{
		listData:'',
    p_data:'',
		p_display:"none",
    Nodata:""
	}
})

//获取数据
function getListData(){
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/zpq/get_action_ranking_list',
	    data={customer_id_en:customer_id_en,user_id_en:user_id_en,action_id:apply_id},
	    type="get",
      timer="",//定时器     
      vm=this.vm;
        
//调用公共AJAX函数
   	ajax(url,data,type,function(result){
      console.log(result.data==null)
   	   if(result!=null){
   	   	  vm.listData=result.data;
   	   };
       if(result.data==null){
          vm.Nodata="没有数据...";
          console.log(1)
       }
		
	});

}


function getPersonalData(){
    var  u_url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/zpq/get_action_ranking_list_by_me',
        data={customer_id_en:customer_id_en,user_id_en:user_id_en,action_id:apply_id},
        type="get",
        timer="",//定时器     
        vm=this.vm;

    ajax(u_url,data,type,function(result1){ 
          if(result1!=null){
            // vm.l_display="block";
            vm.p_display="block";
            vm.p_data=result1[0];
           }
           
    })

  }

getListData();
getPersonalData();
var vm=this.vm;
vm.l_display="block";
$("body").css({"opacity":"1"});
// $(".list-nodata").css({"opacity":"1"});
setInterval("getListData()",5000);
setInterval("getPersonalData()",5000);

