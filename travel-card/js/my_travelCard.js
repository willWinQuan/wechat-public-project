
  var myTravelCard=function(){
  	
  	this.myorder_data=function(vm){//我的订单
 	     var myOrder_data_val={
 	     	    customer_id:vm.customer_id,
						pagenum:vm.myOrder_page,
						pagesize:5,
						type:2
         };
         
    	getJSON('get',myOrder_data_val,'/o2o/web/index.php?m=travel_mycard&a=my_order').then(function(res){
//  		console.log(res);
			if(res.errcode !=1000){
				if(res.errcode==1008){
					location.href=res.data;
					return;
				};
				alert(JSON.stringify(res.errmsg));
				return;
			};
			
			if(res.data.result_list.length==0 && vm.myOrder_page ==1){//没有我的订单
				vm['myorder_nodata']=true;
				return;
			};
			
			if(res.data.result_list.length<5 && vm.myOrder_page !=1){
            	vm['myorder_no_more']=true;
      };
			
			if(vm['myOrder_list']==''){
		  	vm['myOrder_list']=res.data.result_list;
		 }else{
		 	  vm['myOrder_list']=vm['myOrder_list'].concat(res.data.result_list);
		 };
		 
			vm['personal_name']=res.data.diy.personal_name;
			vm['team_name']=res.data.diy.team_name;
			vm['customer_id_m']=res.data.customer_id;
			console.log(vm['customer_id_m']);
			
    	}).catch(function(errContent){
			console.log(errContent);
		});
    };
    
    this.ecar_data=function(vm){ //展示电子卡数据
    	var ecar_data_val={
    		    customer_id:vm.customer_id,
						pagenum:vm.ecar_ecode_page,
						pagesize:5,
						type:2
      };
//   alert(JSON.stringify(ecar_data_val));
		getJSON('get',ecar_data_val,'/o2o/web/index.php?m=travel_mycard&a=mycard_list').then(function(res){
//			console.log(res);
        
			if(res.errcode !=1000){
				
				if(res.errcode==1008){
					location.href=res.data;
					return;
				};
				alert(JSON.stringify(res.errmsg));
				return;
			};
			
//			alert(JSON.stringify(res));
			
			vm['ecar_isopen']=res.data.is_open;//是否开启续费
			vm['ecar_logo_img']=res.data.logo_img;
			vm['ecar_cover_img']=res.data.cover_img;
			vm['ecar_back_img']=res.data.back_img;
			
			if(res.data.result_list.length==0 && vm.ecar_ecode_page ==1){//没有电子卡
				vm['eCard_nodata']=true;
				return;
			};
            
	    if(res.data.result_list.length<5 && vm.ecar_ecode_page !=1){
	    	vm['ecar_no_more']=true;
	    }
            
			var result_list=res.data.result_list;
			
		  if(vm['ecar_list']==''){
		  	vm['ecar_list']=result_list;
		  }else{
		  	vm['ecar_list']=vm['ecar_list'].concat(result_list);
		  }
						
		}).catch(function(errContent){
			console.log(errContent);
		});		
    };
    
    this.scroll_listen=function(vm){
    	var ecar_data=this.ecar_data;
    	var myorder_data=this.myorder_data;
    	$(window).scroll(function(){//滚动条距离下面的距离
			var s_top=$(window).scrollTop();
			var s_w=$(window).height();
			var s_d=$(document).height();
			var s_s=s_d-(s_top+s_w);
//			console.log(s_s);
			if(s_s==0){
				if(vm['tab_nav_falg']){ //tab_nav_falg==true 为我的电子卡
         	 		if(vm.ecar_no_more){ //ecar_no_more==true 没有更多
         	 			return;
         	 		};
         	 		vm['ecar_ecode_page']=Number(vm['ecar_ecode_page'])+1;
         	 		ecar_data(vm);
         	 		
         	 	}else if(!vm['tab_nav_falg']){//tab_nav_falg==false 为我的订单
         	 		if(vm.myorder_no_more){ //myorder_no_more==true 没有更多
         	 			return;
         	 		};
         	 		vm['myOrder_page']=Number(vm['myOrder_page'])+1;
         	 		myorder_data(vm);
         	 	}
			};
		});
    };
    
  }


  




