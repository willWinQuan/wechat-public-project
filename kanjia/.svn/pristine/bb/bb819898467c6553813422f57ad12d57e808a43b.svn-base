user_id_en = getPar_WSY('user_id_en');
web_time = getPar_WSY('web_time');
console.log(user_id_en)
console.log(activity_id);
//默认展示数据
var vm=new Vue({
	el:'.main',
	data:{
		list:'',
		Activity_title:'',
		Activity_logo:'',
		day:'',
		hour:'',
		mintue:'',
		seconds:'',
		apply_id:'',
		Error:'',
		money:'',
		weixin_name:'',
		btnStatusData:'',
		activityStatus:''
	},
	//方法
	methods:{
		enroll:function(id,activity_id,latest_price,share_status,goods_expire_code,Activity_expire_code,apply_id){
			hits(activity_id,id); //商品点击量统计
			if(share_status==0){//没有报名
				location.href = 'NoEnroll.html?customer_id_en='+customer_id_en+'&user_id_en='+user_id_en+'&id='+id
				+'&activity_id='+activity_id+'&web_time='+web_time;
			}
			if(share_status==1){//已报名
				location.href = 'enrolled.html?customer_id_en='+customer_id_en+'&user_id_en='+user_id_en+'&id='+id+'&apply_id='+apply_id+'&activity_id='+activity_id+'&web_time='+web_time;
			}
		},
		clickRanking:function(){//点击排行榜
			location.href=HOST_WSY+'/weixinpl/haggling/front/web/sumList.html?customer_id_en='+customer_id_en+'&user_id_en='+user_id_en+'&web_time='+web_time+'&activity_id='+activity_id;
		},
		clickMyBargain:function(){//点击我的砍价
			location.href='mybargain.html?customer_id_en='+customer_id_en+'&user_id_en='+user_id_en+'&web_time='+web_time+'&activity_id='+activity_id;
		}
		
	},
})
/*活动页面展示数据*************开始*/
function res(){
	var vm=this.vm;
	var data = {customer_id_en:customer_id_en,user_id_en:user_id_en,activity_id:activity_id}
	console.log(data);
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/front/goodsInfo'
	$.ajax({
        url:url,
        data:data,
        async:true,
        type:'get',
        dataType:'json',
        success:function(res){
        	//展示
			document.getElementsByTagName('body')[0].style.visibility='visible';
        	console.log(res)
			//数据
            vm.Activity_logo=res.data[0]['Activity_logo']
			vm.list=res.data;
			title=res.activity_title;
        },
        error:function(res){
            console.log('请求信息失败！！！');
        },  
    });
}
res();

//活动标题赋值
var title="";
$(function(){
	console.log(title)
	console.log($('#index-title').html());
	$('#index-title').html(title);
})

//首页实时---倒计时&&商品按钮状态
function timeliness(){
	var vm=this.vm;
	var data={customer_id_en:customer_id_en,user_id_en:user_id_en,activity_id:activity_id};
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/front/indexInfo';

	ajax(url,data,"get",function(res){
		   // console.log("实时数据："+JSON.stringify(res));
		vm.activityStatus=res.data[0]['activity_expire_code'];
		// console.log(vm.activityStatus);

        vm.btnStatusData=res.data;
       
     if(new Date(res.data[0]['nowtime'])>new Date(res.data[0]['activity_start_time'])){
            // 活动开始
             //活动时间
            time=new Date(res.data[0]['activity_end_time'])-new Date(res.data[0]['nowtime'])
            // console.log(res.data[0]['activity_end_time']);
            day=parseInt(time/1000/60/60/24)
		    hour=parseInt(time/1000/60/60%24)
			mintue=parseInt(time/1000/60%60)
			seconds=parseInt(time/1000%60)
   
            if(day<10){
			day='0'+day
		    }
		    if(hour<10){
			hour='0'+hour
		    }
		    if(mintue<10){
			mintue='0'+mintue
		    }
		    if(seconds<10){
			seconds='0'+seconds
		    }
		    vm.day=day;
	        vm.hour=hour;
	        vm.mintue=mintue;
	        vm.seconds=seconds;
		    
  }else if(new Date(res.data[0]['nowtime'])<=new Date(res.data[0]['activity_start_time'])){
  	        time=new Date(res.data[0]['activity_start_time'])-new Date(res.data[0]['nowtime'])
            // console.log(res.data[0]['activity_end_time']);
            day=parseInt(time/1000/60/60/24)
		    hour=parseInt(time/1000/60/60%24)
			mintue=parseInt(time/1000/60%60)
			seconds=parseInt(time/1000%60)
   
            if(day<10){
			day='0'+day
		    }
		    if(hour<10){
			hour='0'+hour
		    }
		    if(mintue<10){
			mintue='0'+mintue
		    }
		    if(seconds<10){
			seconds='0'+seconds
		    }
		    vm.day=day;
	        vm.hour=hour;
	        vm.mintue=mintue;
	        vm.seconds=seconds;
            // 活动还没开始
            console.log("活动还没开始！");

  }

            // console.log(res.data.length)         
	})



	
}

timeliness()
setInterval('timeliness()',1000);


/*活动页面展示数据*************结束*/
/*优惠券*/
function prompt(){
	var vm=this.vm;
	var data = {customer_id_en:customer_id_en,activity_id:activity_id}
	console.log(data);
	var url=HOST_WSY+'/weixinpl/haggling/back/index.php/home/zpq/test_coupon'
	$.ajax({
        url:url,
        data:data,
        async:true,
        type:'get',
        dataType:'json',
        success:function(res){
        	console.log(res)
        	vm.Error=res.error;
        	vm.weixin_name=res.data['weixin_name'];
        	vm.money=res.data['money'];
        	setTimeout('prompt()',10000)
        },
        error:function(res){
            console.log('请求信息失败！！！');
        },  
    });
}
prompt();
