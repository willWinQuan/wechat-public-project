	var customer_id=getPar_WSY('customer_id')
	var vm=new Vue({
	  el: '#apply_card',
	  data:{
	    is_personal_card:'',
	    is_team_card:'',
	    personal_diy:'',
	    team_diy:'',
	    boot_page:'',
	    apply_index:0,
	    card_way:'',
	    is_service:'',
	    service_url:'',
	    is_empty:'',
	  },
	  created:function(){
	  	this._data.card_way=$('.apply_now').attr('data-id')
		console.log(this._data.card_way)
	  	var data={
	  		customer_id:customer_id,
		}
		$.ajax({
			url:'/o2o/web/index.php?m=travel_card&a=boot_page',
			data:data,
			type:'POST',
			dataType:'json',
			success:function(res){
				console.log(res)
				if(res.errcode==1008){
					window.location.href=res.data;
					return;
				};
				vm.is_personal_card=res.is_personal_card;
				vm.is_team_card=res.is_team_card;
				vm.personal_diy=res.personal_diy;
				vm.team_diy=res.team_diy;
				vm.boot_page=res.boot_page;
				vm.is_service=res.is_service;
				vm.service_url=res.service_url;
				if(vm.is_personal_card==1 && vm.is_team_card==0){
					vm.card_way=1
				}
				if(vm.is_personal_card==0 && vm.is_team_card==1){
					vm.card_way=2
				}
				if(vm.is_personal_card==0 && vm.is_team_card==0){
					vm.is_empty=1
				}
			}
		})
	  },
	  methods:{
	  	apply_card:function(){
	  		var type=vm.card_way;
	  		if(vm.card_way=='1'){
				window.location.href="apply_info.html?type="+type;
			}
			if(vm.card_way=='2'){
				type=vm.card_way+','+vm.is_service;
				window.location.href="apply_team.html?type="+type;
			}
	  	}
	  }
	});
	
	function toggle(obj){
		$(obj).addClass('apply_now').siblings().removeClass('apply_now');	
		vm.card_way=$(obj).attr('data-id');
	}