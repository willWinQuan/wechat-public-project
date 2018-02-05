/**/
var card_id = getPar_WSY('card_id');
var vm=new Vue({
	el:'#app',
	data:{
		card_number:'',
		time:'3个月',
		validity_end:'',
		updatetime:'',
		type:'',
		renew_num:'',
		renew_unit:'',
		batchcode:'',
		remark:''
	},
	created:function(){
		var id=card_id;
		console.log(id)
		var data={
			id:id
		}
		console.log(data)
		$.ajax({
			url:'/o2o/web/index.php?m=travel_mycard&a=renew_detail',
			data:data,
			type:'get',
			async:true,
			dataType:'json',
			success:function(res){
				console.log(res)
				vm.card_number=res.data.card_number;
				vm.validity_end=res.data.validity_end;
				vm.updatetime=res.data.updatetime;
				vm.renew_num=res.data.renew_num;
				vm.batchcode=res.data.batchcode;
				vm.remark=res.data.remark;
				if(res.data.pay_type==0){
					vm.type='还未支付'
				}
				if(res.data.pay_type==1){
					vm.type='微信支付'
				}
				if(res.data.pay_type==2){
					vm.type='会员卡余额支付'
				}
				if(res.data.pay_type==3){
					vm.type='零钱支付'
				}
				if(res.data.pay_type==4){
					vm.type='支付宝支付'
				}
				if(res.data.pay_type==5){
					vm.type='后台支付'
				}
				//
				if(res.data.renew_unit==1){
					vm.renew_unit='天';
				}
				if(res.data.renew_unit==2){
					vm.renew_unit='个月';
				}
			}
		})
	}
})