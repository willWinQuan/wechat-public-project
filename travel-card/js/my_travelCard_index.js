   
   
$(function(){

   var vm=new Vue({
   	   el:'.main',
   	   data:{
         	  customer_id:'',
         	  user_id:'',
         	  tab_nav_falg:true,
         	  true_color:'rgb(255,133,48)',
         	  default_color:'#333',
         	  myorder_nodata:false,
         	  eCard_nodata:false,
         	  ecode_alert_show:false,
         	  ecar_logo_img:'',
         	  ecar_cover_img:'',
         	  ecar_back_img:'',
         	  ecar_list:'',
         	  ecar_isopen:1,
         	  ecar_ecode_img:'', 
         	  ecar_ecode_beginTime:'',
         	  ecar_ecode_endTime:'',
         	  ecar_ecode_page:1,
         	  ecar_no_more:false,
         	  myOrder_list:'',
         	  pay_data:'',
         	  myOrder_page:1,
         	  myorder_no_more:false,
         	  personal_name:'',
         	  team_name:'',
         	  customer_id_m:'',
         	  s_s:1,
         	  card_number:'',
         	  order_id:''
   	   },
   	   methods:{
           	show_ecode:function(id,ecode,begin,end,card_number){
     		    //展示二维码
     		    var that=this._data;
                that.ecode_alert_show=true;
                that.ecar_ecode_img=ecode;
                that.ecar_ecode_beginTime=begin;
                that.ecar_ecode_endTime=end;
                that.card_number=card_number;
                that.order_id=id;
                $('.mask').css('display',"block");
                disableScroll=true;  
                $('body').css('overflow','hidden');

                /*去掉手机滑动默认行为*///禁止滚动-移动端
				$('body').on('touchmove', function (event) {
				      event.preventDefault();
				},false);
                
                
                /*微信浏览器特殊处理*/
				if(window.navigator.userAgent.toLowerCase().match(/MicroMessenger/i) == 'micromessenger'){
				      $('body').addClass('index_body');//添加禁止滚动的样式
				}else{
				      $('body').removeClass('index_body');//去除禁止滚动的样式
				}
            },
            getPay_data:function(id){//获取支付data
            	var that=this._data;
         			var getPay_val={
         				customer_id:that.customer_id,
		                user_id:that.user_id,
         				id:id
         			};
       
         			getJSON('get',getPay_val,'/o2o/web/index.php?m=travel_mycard&a=goon_pay').then(function(res){	
						if(res.errcode !=1000){
							alert(JSON.stringify(res.errmsg));
							return;
						};
                        
						vm['pay_data']=res.data;
						setTimeout(function(){
							$('#form'+id).submit();
						},250);

         			}).catch(function(errContent){
						console.log(errContent);
					});
           },
           jump_myOrder_detail:function(id){
     	        location.href='order_detail.html?id='+id+'&customer_id='+vm.customer_id;
           },
           jump_renewRecord_jum:function(){//跳转到续费详情
     	        location.href='renew_record.html?customer_id='+vm.customer_id;
           },
           jum_getcard:function(){
                location.href='apply_card.html?customer_id='+vm.customer_id;
           },
          update_ecode:function(){
          	 var card_number=this._data.card_number; 
          	 var that=this._data;
           	   var data={
       	   	    customer_id:this._data.customer_id,
                card_number:card_number.slice(0,4)+card_number.slice(5,15)+card_number.slice(16,18)
           	   };
           	   console.log(data);
           	getJSON('get',data,'/o2o/web/index.php?m=travel_card&a=qr_code').then(function(res){	
           	    	 console.log(JSON.stringify(res));
					if(res.errcode !=1000){
						$('.ecode_genew_msg').css('display','block');
						$('.ecode_genew_msg').html('刷新失败');
						setTimeout(function(){
							$('.ecode_genew_msg').css('display','none');
						},2500)
						return;
					};

	                $('.ecode_img img').attr('src', res.qrcode_url +"?t=" + Math.random()); 
	                $('#qrcode'+that.order_id).attr('src',res.qrcode_url +"?t=" + Math.random());
	                $('.ecode_genew_msg').css('display','block');
					$('.ecode_genew_msg').html('刷新成功');
					setTimeout(function(){
						$('.ecode_genew_msg').css('display','none');
					},2500)
	
	             	}).catch(function(errContent){
						console.log(errContent);
					});
            }
            
   	   }
   })

                 //	      点击遮罩层隐藏二维码
       $('.mask').click(function(){
        	 vm.ecode_alert_show=false;
             $('.mask').css('display',"none");
             $("body").off("touchmove");
             $('body').css('overflow','auto');
             $('body').removeClass('index_body');
       })
        
        //tab切换
        $('.nav_item').click(function(){
        	vm.tab_nav_falg=!vm.tab_nav_falg;
        	$(this).css('color',vm.true_color);
        	$(this).siblings('.nav_item').css('color',vm.default_color);
        	$('html').css('background',vm.tab_nav_falg?'rgb(248,248,248)':'rgb(240,239,245)');
        	$('body').css('background',vm.tab_nav_falg?'rgb(248,248,248)':'rgb(240,239,245)');
        });
        
	    vm['customer_id']=getPar_WSY('customer_id');
        
        /*
         * 数据渲染 && 操作
         * 
         */
        var my_travelCard=new myTravelCard();

        //电子卡
        my_travelCard.ecar_data(vm);
        
        //我的订单
        my_travelCard.myorder_data(vm);
        
        //监听滚动触底事件执行分页
        my_travelCard.scroll_listen(vm);    
        
})