
var orderDetail=function(vm){
    this.orderDetail_data=function(){
    	var count_down_t=this.count_down_t;
    	var orderDetail_data=this.orderDetail_data;
    	var data={
    		customer_id:vm.customer_id,
    		id:vm.order_id
//  		id:'316'
    	};
    	getJSON('get',data,'/o2o/web/index.php?m=travel_mycard&a=order_detail').then(function(res){
    		console.log(res);
    		if(res.errcode !=1000){
				alert(JSON.stringify(res.errmsg));
				return;
			};
			var order_data=res.data;
			
			vm['orderDetail_data']=order_data;
			
			vm['service_url']=res.data.customer_url;
			
			switch (res.data.status){
				case '0': 
				$('.order_detailCard_l').css('top','-.4rem');
				var failure_time=res.data.failure_time;
			    count_down_t(orderDetail_data,failure_time);
			    
				break; //订单待支付状态样式
				case '2':$('.order_detailCard_l').css('top','0rem');break;//订单失效卡图样式
			};
			
				
    	}).catch(function(errContent){
			console.log(errContent);
		});	
   };
   this.count_down_t=function(orderDetail_data,endtime_n){
   	    var endtime_n=Number(endtime_n);
        console.log(typeof orderDetail_data)
   	    var down_timer=setInterval(function(){
   	    	//小时
   	    	var hour=parseInt(endtime_n/3600000);
   	    	//分钟
   	    	var min=parseInt(endtime_n%3600000/60000);
   	    	//秒
   	    	var second=parseInt(endtime_n%3600000%60000/1000);
   	    	
   	    	vm['hour']=hour;
   	    	vm['min']=min;
   	    	vm['second']=second;
   	    	endtime_n=endtime_n-1000;
   	    	
   	    	if(endtime_n<=0){
   	    		clearInterval(down_timer);
   	    		orderDetail_data();
   	    	}
   	    },1000)
   }
};
