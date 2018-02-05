/*获取链接上的传参*/
var more_type=1;
var pagenum=1;
var pagesize=10;
var list=[];
var vm=new Vue({
	el:'#app',
	data:{
		type:1,
		list:[],
		more_more:-1,
	},
	created:function(){
		var data={
			pagenum:pagenum,
			pagesize:pagesize
		}
		$.ajax({
			url:'/o2o/web/index.php?m=travel_mycard&a=renew_list',
			data:data,
			type:'get',
			async:true,
			dataType:'json',
			success:function(res){
				if(res.errcode==1000){
					list=res.data.result_list;
					vm.list=list;
					if(res.data.result_list.length==0){
						vm.type=0;
						more_type=0;
					}else{
						if(res.data.result_list.length<10){
							vm.type=1;
							vm.more_more=0;
							more_type=0;
						}
					}
				}
				console.log(res)
			}
		})
		/*触碰底部触发请求*/
		var win_het=window.screen.height;
		window.onscroll=function(){
			if(win_het+$(window).scrollTop()>= document.body.scrollHeight){
				if(more_type==1){
					vm.more_more=1;
					more_type=0;
					pagenum=pagenum+1;
					var data={
						pagenum:pagenum,
						pagesize:pagesize
					}
					//
					$.ajax({
						url:'/o2o/web/index.php?m=travel_mycard&a=renew_list',
						data:data,
						type:'get',
						async:true,
						dataType:'json',
						success:function(res){
							for(var i=0;i<res.data.result_list.length;i++){
								list.push(res.data.result_list[i]);
							}
							console.log(list)
							more_type=0;
							setTimeout(function(){
								vm.more_more=0;
								vm.list=list;
							},500)
						}
					})
				}
			}
		}
	},
	methods:{
		renew_record:function(id){
			console.log(id)

			location.href ='/o2o/web/view/travel/renew_detail.html?card_id='+id;
		}
	}
})
