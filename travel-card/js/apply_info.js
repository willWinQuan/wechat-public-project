    var type=getPar_WSY('type');
	var para=getPar_WSY('para');
	var argument=getPar_WSY('argument');	
	if(para){
		para=para.split(',')
	}
	if(argument){
		argument=argument.split(',');
		var team_info=true;
	}
	//	console.log(type)
	//禁止滚动-移动端
	//记录是否阻止滚动
	var disableScroll = false;
	document.body.addEventListener('touchmove', function (event) {
		if(disableScroll){
		   event.preventDefault();
		}
	}, false);	
	var vm=new Vue({
	  el: '#apply_info',
	  data: {
	    mask_status: false,
	    capion_status: false,
	    bank_list:[],
	    bank_index:0,
	    bank_id:0,
	    province:'',
	    city:'',
	    area:'',
	    bank_detail:false,
	    bank_address:'',
	    bank_phone:'',
	    name:'',
	    personal_price:'',
	    personal_pay_diy:'',
	    identity_front_img:'',
	    identity_back_img:'',
	    cue_text:'',
	    team_type:1,
	    pay_data:'',
	    team_name:'',
	    is_service:'',
	    service_url:'',
	    team_pay_diy:'',
	    team_price:'',
	    pre:'',
	    customer_id:'',
	    pre_bank:'',
	    province_bank:'',
	    city_bank:'',
	    area_bank:'',
	    is_status:0,
	    res:'',
	  },
	  created:function(){
//	  	var team_type=1;
		//获取团队id  类型
		var argument=getPar_WSY('argument');		
		if(argument){
			argument=argument.split(',');
			this._data.team_type=argument[2];
			this._data.team_name=argument[1];
		}
		$.ajax({
			url:'/o2o/web/index.php?m=travel_card&a=card_handle',
			data:{},
			type:'POST',
			dataType:'json',
			success:function(res){
				console.log(res)
				if(res.errcode==1008){
					window.location.href=res.data;
					return;
				};
				console.log(this)
				console.log(vm)
				if(vm.team_type==1){
					vm.bank_list=res.bank;
					vm.bank_id=res.bank[0].id;
				}
				vm.personal_price=res.personal_price;
				vm.personal_pay_diy=res.personal_pay_diy;
				vm.is_service=res.is_service;
				vm.service_url=res.service_url;
				vm.team_pay_diy=res.team_pay_diy;
				vm.team_price=res.team_price;
				vm.customer_id=res.customer_id;
				console.log(vm.bank_id);
			},
		})
		console.log(para);
		if(para[0] && para[1] && para[2] && para[4]){
			this._data.bank_detail=true;
			this._data.bank_address=para[0];
			this._data.bank_phone=para[1];
			this._data.branch_id=para[2];
			this._data.name=para[4]
		}
	  },
	  methods:{
	  	//上传身份证
	  	uploadImg:function(e){
	  		var that=this;
	  		console.log(e)
	  		var front_back=e.target.attributes[2].value;
			var files = e.target.files || e.dataTransfer.files;
			var img = new Image();
			var reader = new FileReader();
			reader.readAsDataURL(files[0]);
			reader.onload = function(e){
				var mb = (e.total/1024)/1024;
				if(mb>= 1){
					alert('文件大小大于1M');
					return;
				}
				img.src = this.result;
				imgBase64=this.result;
				img.style.width = "80%";
//				console.log(img.src)
				if(front_back==1){
					$('#click1 #one').css('display','none');
			        $("#click1").css('background','url('+img.src+') no-repeat center top');
			        $("#click1").css("background-size",'contain');		        
				}
				if(front_back==2){
					$('#click2 #two').css('display','none');
			        $("#click2").css('background','url('+img.src+') no-repeat center top');
			        $("#click2").css("background-size",'contain');
				}
				
			}	
			setTimeout(function(){
		//      var imgBase64=$('#click img').attr('src')
				var imgBase64=img.src
		        var data={
		            pic:imgBase64,
		        }
		        console.log(data)
		        $.ajax({
				  url:'/o2o/web/index.php?m=travel_card&a=upload_file',
		          type:'POST',
		          data:data,
		          async:true,
		          dataType:'json',
		          success:function(res){
		            console.log(res);
		            if(res.errcode==1008){
						window.location.href=res.data;
						return;
					};

				  	if(front_back==1){
		              	that._data.identity_front_img=res.data;
		              	if(vm.team_type==1){
//		              		localStorage.setItem("identity_front_img",that._data.identity_front_img);
		              		sessionStorage.identity_front_img=that._data.identity_front_img;
		              	}			              	
		            }
		            if(front_back==2){
		              	that._data.identity_back_img=res.data;
		              	if(vm.team_type==1){
//		              		localStorage.setItem("identity_back_img",that._data.identity_back_img);
							sessionStorage.identity_back_img=that._data.identity_back_img;
		              	}			              	
		            }
		              
		          },
		          error: function (res) {  
		              console.log(res)
		          } 
		       });
		     },500)
		},
		//选择银行
	  	select_bank:function(index,id){
	  		console.log(this);
		  	$('.bank_wrap').find('.bank').find('.circle').removeClass('active');
	  		this.bank_index=index;
	  		vm.bank_id=id;
	  		console.log(index);
	  		console.log(this.bank_index)
	  		console.log('pre:'+vm.pre)
	  		console.log('bank_id:'+vm.bank_id)
	  		if(vm.pre!=vm.bank_id){
	  			$('#province').html('')	  		
	  			$('#city').html('')
	  			$('#area').html('')
	  			vm.bank_detail=false;  	
	  			console.log(vm.bank_id)
	  		}
//	  		else{
	  			$('.bank_wrap').find('.bank').eq(index).find('.circle').addClass('active');
//	  		}
	  		console.log(vm.bank_id)
//	  		localStorage.setItem("bank_id", vm.bank_id);
	  		sessionStorage.bank_id=vm.bank_id;
	  		vm.pre=vm.bank_id;
	  		

	  	},
	  	//选择地区
	  	is_bank:function(){
	  		//获取省市区	
	  		if(vm.is_status==0){
				var that=this;
				vm.is_status=1
		  		var active=$('.select_bank').find('.circle').hasClass("active");
	//			if(active){
	//				var bank_name=$('.active').parents('.bank').find('label').html()
	//			}
//		  		if(localStorage.getItem("bank_id")){
				if(sessionStorage.bank_id){
		  			var bank_id=bank_id=sessionStorage.bank_id
		  		}else{
		  			var bank_id=vm.bank_id
		  		}

			  	$.ajax({
			  		url:'/o2o/web/index.php?m=travel_card&a=receive_address',
			  		data:{bank_id:bank_id},
			  		type:'POST',
			  		dataType:'json',
			  		success:function(res){
			  			console.log(res)
			  			vm.is_status=0
			  			if(res.errcode==1008){
							window.location.href=res.data;
							return;
						};
			  			console.log('pre:'+vm.pre_bank)
			  			console.log('id:'+vm.bank_id)
			  			if(vm.pre_bank!=vm.bank_id){
			  				var province_bank=[];
				  			var city_bank=[];
				  			var area_bank=[];
				  			for(var i=0;i<res.province.length;i++){
				  				province_bank.push(res.province[i])
				  				vm.province_bank=province_bank
				  			}
				  			for(var i=0;i<res.city.length;i++){
				  				if(res.city[i].parentid==res.province[0].text){
				  					city_bank.push(res.city[i])
				  					vm.city_bank=city_bank;
				  				}
				  			}
				  			for(var i=0;i<res.area.length;i++){
				  				if(res.area[i].parentid==res.city[0].text){
				  					area_bank.push(res.area[i])
				  					vm.area_bank=area_bank;
				  				}
				  			}
			  			}else{
			  				var province_bank=vm.province_bank;
			  				var city_bank=vm.city_bank;
			  				var area_bank=vm.area_bank;
			  			}
						console.log(vm.province_bank)
						console.log(vm.city_bank)
			  			var data1 = vm.province_bank;
						var data2 = vm.city_bank;
					  	var data3 = vm.area_bank;
					  	var picker3El = document.getElementById('picker3');
					  	var province=document.getElementById('province');
					  	var city=document.getElementById('city');
					  	var area=document.getElementById('area');
					  	var picker3 = new Picker({
					   		data: [data1, data2, data3]
					 	});
						// 当用户点击确定的时候，会派发picker.select事件，同时会传递每列选择的值数组selectVal和每列选择的序号数组selectIndex。
						picker3.on('picker.select', function (selectedVal, selectedIndex) {
						//	picker3El.innerText = data1[selectedIndex[0]].text + ' ' + data2[selectedIndex[1]].text + ' ' + data3[selectedIndex[2]].text;
							that._data.province=data1[selectedIndex[0]].text;
							that._data.city=data2[selectedIndex[1]].text;
							that._data.area=data3[selectedIndex[2]].text;
							
							
							console.log(data1[selectedIndex[0]].text.charAt(data1[selectedIndex[0]].text.length-1))
							if((data1[selectedIndex[0]].text.charAt(data1[selectedIndex[0]].text.length-1)=='省')||(data1[selectedIndex[0]].text.charAt(data1[selectedIndex[0]].text.length-1)=='市')||(data1[selectedIndex[0]].text.charAt(data1[selectedIndex[0]].text.length-1)=='区')){							
								province.innerText=data1[selectedIndex[0]].text.substring(0,data1[selectedIndex[0]].text.length-1);
							}else{
								province.innerText=data1[selectedIndex[0]].text;
							}
						    
						    if(data2[selectedIndex[1]].text.charAt(data2[selectedIndex[1]].text.length-1)=='市'){
						    	city.innerText=data2[selectedIndex[1]].text.substring(0,data2[selectedIndex[1]].text.length-1);
						    }else{
						    	city.innerText=data2[selectedIndex[1]].text;
						    }
						    
						    if(data3[selectedIndex[2]].text.charAt(data3[selectedIndex[2]].text.length-1)==('区'||'镇')){
						    	area.innerText=data3[selectedIndex[2]].text.substring(0,data3[selectedIndex[2]].text.length-1);
						    }else{
						    	area.innerText=data3[selectedIndex[2]].text;
						    }
						    
//						    console.log(localStorage.getItem("province"))
							console.log(data1[selectedIndex[0]].text)
							if((sessionStorage.province && (sessionStorage.province!=data1[selectedIndex[0]].text))||(sessionStorage.city && (sessionStorage.city!=data1[selectedIndex[1]].text))||(sessionStorage.city && (sessionStorage.city!=data1[selectedIndex[1]].text))){								
								vm.bank_detail=false;  	
							}		    
//						    localStorage.setItem("province", data1[selectedIndex[0]].text); 
//							localStorage.setItem("city", data2[selectedIndex[1]].text);
//							localStorage.setItem("area", data3[selectedIndex[2]].text);
							sessionStorage.province=data1[selectedIndex[0]].text;
							sessionStorage.city=data2[selectedIndex[1]].text;
							sessionStorage.area=data3[selectedIndex[2]].text;
						});
						//当一列滚动停止的时候，会派发picker.change事件，同时会传递列序号index及滚动停止的位置selectIndex。
					  	picker3.on('picker.change', function (index, selectedIndex) {
					    	console.log(index);
					    	console.log(selectedIndex)
					    	if (index === 0){
					    		data2=[];
					    		var pre_province=province_bank[selectedIndex].text;
					    		for(var i=0;i<res.city.length;i++){
					    			if(res.city[i].parentid==pre_province){
					    				data2.push(res.city[i])
					    			}
					  			}
					    		data3=[];
					    		for(var i=0;i<res.area.length;i++){
					    			if(res.area[i].parentid==data2[0].text){
						  				data3.push(res.area[i]);
						  			}
					    		}
							    picker3.refillColumn(1, data2);
							    picker3.refillColumn(2, data3);
							    picker3.scrollColumn(1, 0)
							    picker3.scrollColumn(2, 0)
							 }
						  	if (index === 1) {
						  		data3=[];
						  		for(var i=0;i<res.area.length;i++){
						  			if(res.area[i].parentid==city_bank[selectedIndex].text){
						  				data3.push(res.area[i]);
						  			}
						  		}
							    picker3.refillColumn(2, data3);
							    picker3.scrollColumn(2, 0)
							}
					  	});
						//当用户点击确定的时候，如果本次选择的数据和上一次不一致，会派发picker.valuechange事件，同时会传递每列选择的值数组selectVal和每列选择的序号数组selectIndex。
						picker3.on('picker.valuechange', function (selectedVal, selectedIndex) {
							console.log(selectedVal);
							console.log(selectedIndex)//三列  数组下标
						});
						picker3.show();
	//						picker3El.addEventListener('click', function () {
	//						  picker3.show();
	//						});	
						vm.pre_bank=vm.bank_id;
					}		  		
				})
		  	}
	  	},
	  	know:function(){
//	  		document.getElementById('shade').ontouchstart = function(e){ return true }
			
	  		this._data.mask_status=false;
	  		this._data.capion_status=false;
	  		$('body').css('height','auto');
	  		$('body').css('overflow-y','scroll');	  		
	  		disableScroll=false;
//	  		document.body.style.overflow='';
//			document.removeEventListener('touchmove',mo,false);
	  	},
	  }
	})
	//性别按钮
	$(".sex_radio").click(function() {
	    $(this).siblings(".user-defined").children("span").addClass("active");
	    $(this).parent().siblings(".sex").find("span").removeClass("active");
	});
	//验证码倒计时
	var time=60;
	$('.auth_code').click(function(){
		var phone_number=$("input[class='phone_number']").val();
		console.log(phone_number)
		if(!(/^1[34578]\d{9}$/.test(phone_number))){ 
	        vm.mask_status=true;
			vm.capion_status=true;
			vm.cue_text='手机号码有误';
			disableScroll=true;
			$('.mask_wrap').on('touchmove',function(){
				return false;
			})
	        return false; 
	    } 
		$('.auth_code').css('background','#555555')
	   if($('.auth_code').html()=='获取验证码' || $('.auth_code').html()=='重新发送'){
	   		var data={
	   			phone:phone_number
	   		}
	   		$.ajax({
	   			url:'/o2o/web/index.php?m=travel_card&a=verification_code',
	   			data:data,
	   			type:'POST',
	   			dataType:'json',
	   			success:function(res){
	   				console.log(res)
	   				if(res.errcode==1008){
						window.location.href=res.data;
						return;
					};
	   			}
	   		})
			count_down=setInterval(function(){
				time--;
				$('.auth_code').html(time);
				if(time==0){
					$('.auth_code').html('重新发送');
					time=60;
					clearInterval(count_down);
				}					
			},1000)
		}
	 })
	//图片裁剪
	var head_img='';
	var clipArea = new bjj.PhotoClip("#clipArea", {
		size: [260, 378],
		outputSize: [620, 910],
		file: "#file",
		view: "#view",
		ok: "#clipBtn",
		loadStart: function() {
			console.log("照片读取中");
			$('#clipArea').addClass('clipArea')
			$('.mask').show()
			$('#clipBtn').show()
			$('#clipBtn11').show()
			$('.clip_bottom').show()
		},
		loadComplete: function() {
			console.log("照片读取完成");
			
		},
		clipFinish: function(dataURL) {
			if(dataURL.length/1024>1024){
				alert("图片不能大于1M")
			}
			$('#clipArea').removeClass('clipArea')
			$('.mask').hide()
			$('#clipBtn').hide()
			$('#clipBtn11').hide()
			$('.clip_bottom').hide()
			
			$.ajax({
				url:'/o2o/web/index.php?m=travel_card&a=upload_file',
				data:{
					pic:dataURL,
				},
				type:'POST',
				dataType:'json',
				success:function(res){
					console.log(res)
					if(res.errcode==1008){
						window.location.href=res.data;
						return;
					};
					head_img=res.data;
					if(vm.team_type==1){
//						localStorage.setItem("head_img",head_img);
						sessionStorage.head_img=head_img;
					}					
				}
			})
		}
	});
	$('#clipBtn11').click(function(){
			$('#clipArea').removeClass('clipArea')
			$('.mask').hide()
			$('#clipBtn').hide()
			$('#clipBtn11').hide()
			$('.clip_bottom').hide()
			$('body').css('overflow-y','scroll')
	})
	$('#clipBtn').click(function(){
		$('body').css('overflow-y','scroll')
	})
	$('#file').click(function(){
		$('body').css('overflow-y','hidden')
	})
	

//跳转页面
function jump_page(){
	console.log(vm.identity_front_img)
	var name=$("input[class='really_name']").val();
	var id_card=$("input[class='identity_number']").val();
	var sex=$('.radio_wrap').find('.active').parents('.sex').find('label').html();
	var phone=$("input[class='phone_number']").val();
	var code=$("input[class='code']").val();
	var price=vm.personal_price;
//	var bank_id=vm.bank_id;
	var branch_id=vm.branch_id;
//	localStorage.setItem("name", name);
//	localStorage.setItem("id_card", id_card);
//	localStorage.setItem("sex", sex);
//	localStorage.setItem("phone", phone);
//	localStorage.setItem("code", code);
	sessionStorage.name=name;
	sessionStorage.id_card=id_card;
	sessionStorage.sex=sex;
	sessionStorage.phone=phone;
	sessionStorage.code=code;
	if(!$('#province').html()){
		vm.mask_status=true;
		vm.capion_status=true;
		vm.cue_text='请先选择所在地区';
		$('body').css('overflow-y','hidden');
		return;
	}
	if(type){
//		var parameter=localStorage.getItem("bank_id")+'&province='+$('#province').html()+'&city='+$('#city').html()+'&area='+$('#area').html()+'&type='+type.substring(0,1);
//		var parameter=localStorage.getItem("bank_id")+'&province='+localStorage.getItem("province")+'&city='+localStorage.getItem("city")+'&area='+localStorage.getItem("area")+'&type='+type.substring(0,1);
		if(sessionStorage.bank_id){
			var parameter=sessionStorage.bank_id+'&province='+sessionStorage.province+'&city='+sessionStorage.city+'&area='+sessionStorage.area+'&type='+type.substring(0,1);			
		}else{
			var parameter=vm.bank_id+'&province='+sessionStorage.province+'&city='+sessionStorage.city+'&area='+sessionStorage.area+'&type='+type.substring(0,1);			
		}
	}else{
//		var parameter=localStorage.getItem("bank_id")+'&province='+$('#province').html()+'&city='+$('#city').html()+'&area='+$('#area').html()+'&type='+para[3];			
//		var parameter=localStorage.getItem("bank_id")+'&province='+localStorage.getItem("province")+'&city='+localStorage.getItem("city")+'&area='+localStorage.getItem("area")+'&type='+para[3];			
		if(sessionStorage.bank_id){
			var parameter=sessionStorage.bank_id+'&province='+sessionStorage.province+'&city='+sessionStorage.city+'&area='+sessionStorage.area+'&type='+para[3];				
		}else{
			var parameter=vm.bank_id+'&province='+sessionStorage.province+'&city='+sessionStorage.city+'&area='+sessionStorage.area+'&type='+para[3];						
		}
	}
	window.location.href='select_branch.html?parameter='+parameter;
}

//立即支付
$('.pay_now').click(function(){
	console.log(this)
	console.log(argument)
	var name=$("input[class='really_name']").val();
	var id_card=$("input[class='identity_number']").val();
	var sex=$('.radio_wrap').find('.active').parents('.sex').find('label').html();
	var phone=$("input[class='phone_number']").val();
	var code=$("input[class='code']").val();
	var price=vm.personal_price;
//	var bank_id=vm.bank_id;
	var branch_id=para[2];
	identity_front_img=sessionStorage.identity_front_img;
	identity_back_img=sessionStorage.identity_back_img;
	if(sessionStorage.bank_id){
		vm.bank_id=sessionStorage.bank_id
	}
	if(para[3]==1 || type==1){
		var data={
			customer_id:vm.customer_id,
  			type:para[3],
  			name:name,
  			id_card:id_card,
  			sex:sex,
  			phone:phone,
  			code:code,
			head_img:sessionStorage.head_img,
  			identity_front_img:identity_front_img,
  			identity_back_img:identity_back_img,
			price:price,
			bank_id:vm.bank_id,
			branch_id:branch_id,
		}
	}
	if(team_info){
		var data={
			customer_id:vm.customer_id,
  			type:vm.team_type,
  			team_id:argument[0],
  			name:name,
  			id_card:id_card,
  			sex:sex,
  			phone:phone,
  			code:code,
  			head_img:head_img,
  			identity_front_img:vm.identity_front_img,
  			identity_back_img:vm.identity_back_img,
			price:price,
		}
	}
	console.log(data)
	$.ajax({
		url:'/o2o/web/index.php?m=travel_card&a=user_check',
		data:data,
		type:'POST',
		dataType:'json',
		success:function(res){
			console.log(res)
			if(res.errcode==1008){
				window.location.href=res.data;
				return;
			};
			if(res.errcode==1000){
//				vm.pay_data=JSON.stringify(res.data.data);
				vm.pay_data=res.data.data;
				setTimeout(function(){
					$('.to_pay').submit()
				},100)
			}else{
				vm.mask_status=true;
				vm.capion_status=true;
				vm.cue_text=res.errmsg;
				$('body').css('overflow-y','hidden');
			}
		}
	})
})

if (typeof(Storage) != "undefined") {
	if(vm.team_type==1){
		$("input[class='really_name']").val(sessionStorage.name);
	    $("input[class='identity_number']").val(sessionStorage.id_card);
	    if(sessionStorage.sex=='女'){
	    	$('.sex').eq(0).find('.circle').addClass('active')
	    }
	    if(sessionStorage.sex=='男'){
	    	$('.sex').eq(1).find('.circle').addClass('active')
	    }
	    if(sessionStorage.head_img){
	   		$('#view').css('background-image','url('+sessionStorage.head_img+')');
	    }
		if(sessionStorage.identity_front_img){
			$("#click1").css('background','url('+sessionStorage.identity_front_img+') no-repeat center top');
	    	$('#click1 #one').css('display','none')
	    	$("#click1").css("background-size",'contain')
		}
		if(sessionStorage.identity_back_img){
			$("#click2").css('background','url('+sessionStorage.identity_back_img+') no-repeat center top');
	    	$('#click2 #two').css('display','none')
	    	$("#click2").css("background-size",'contain')
		}
	
	    setTimeout(function(){
	    	console.log(sessionStorage.bank_id)
			for(var i=0;i<$('.bank').length;i++){
	//			console.log($('.bank').eq(i).attr('data-id'))
				if($('.bank').eq(i).attr('data-id')==sessionStorage.bank_id){
					var index=i;
				}  		
	    	}
	//		console.log(.find('label').html())
			$('.radio_wrap').find('.bank').eq(index).find('.circle').addClass('active').parents('.bank').siblings().find('.circle').removeClass('active');
		},50)
	    
	    if((sessionStorage.province.charAt(sessionStorage.province.length-1)=='省')||(sessionStorage.province.charAt(sessionStorage.province.length-1)=='市')||(sessionStorage.province.charAt(sessionStorage.province.length-1)=='区')){
	    	$('#province').html(sessionStorage.province.substring(0,sessionStorage.province.length-1));
	    }else{
	    	$('#province').html(sessionStorage.province);
	    }
	    
	    if(sessionStorage.city.charAt(sessionStorage.city.length-1)=='市'){
	    	$('#city').html(sessionStorage.city.substring(0,sessionStorage.city.length-1));
	    }else{
	    	$('#city').html(sessionStorage.city);
	    }
	    
	    if((sessionStorage.area.charAt(sessionStorage.area.length-1)=='区')||(sessionStorage.area.charAt(sessionStorage.area.length-1)=='镇')){
	    	$('#area').html(sessionStorage.area.substring(0,sessionStorage.area.length-1));
	    }else{
	    	$('#area').html(sessionStorage.area);
	    }
	    
	    $("input[class='phone_number']").val(sessionStorage.phone);
	    $("input[class='code']").val(sessionStorage.code);
	}
}else {
   document.getElementById("result").innerHTML = "抱歉！您的浏览器不支持 Web Storage ...";
}