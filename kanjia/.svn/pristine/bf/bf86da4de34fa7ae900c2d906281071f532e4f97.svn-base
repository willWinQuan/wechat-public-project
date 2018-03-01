/**
 * Created by Administrator on 2017/6/5.
 */

$(function () {
    // 初始加载
    $.getJSON(
        "json",
        function (result) {
            $('.chart').css('width',$('.statistics_ul04').width()+"px");
            $(".qrsell_num").html(); // 入驻商家数
            $(".post_expense").html(); // 已提现金额
            $(".stay_expense_num").html(); // 待提现的订单（？）数
            $(".total_order_num").html(result.week.number[6][1]); // 今日订单数
            $(".total_consumption").html(result.week.amount[6][1]); // 今日订单金额
            $(".pay_total_order_num").html(result.week.numberPaid[6][1]); // 今日已付款订单数
            $(".yes_total_order_num").html(result.week.number[5][1]); // 昨日订单数
            $(".yes_pay_total_order_num").html(result.week.numberPaid[5][1]); // 昨日已付款订单数
            $(".this_month_order_num").html(result.month[1].number); // 本月订单数
            $(".this_month_consumption").html(result.month[1].amount); // 本月订单金额
            $(".this_month_order_amplitude").html(result.month[0].number); // 上月订单数
            $(".this_month_consumption_amplitude").html(result.month[0].amount); // 上月订单金额
//                var total_order_amplitude = data.addorder
//                if(total_order_amplitude >= 0){
//                    $(".total_order_amplitude").html(total_order_amplitude+"%");
//                    $(".total_order_amplitude").css("color","red");
//                    $(".total_order_amplitude").css("background","url(../img/dor_icon01.png) no-repeat right center");
//                }else{
//                    $(".total_order_amplitude").html(total_order_amplitude.substr(1)+"%");
//                    $(".total_order_amplitude").css("color","green");
//                    $(".total_order_amplitude").css("background","url(../img/dor_icon02.png) no-repeat right center");
//                }
//                var addpic = data.addpic;
//                if(addpic >= 0){
//                    $(".total_consumption_amplitude").html(addpic+"%");
//                    $(".total_consumption_amplitude").css("color","red");
//                    $(".total_consumption_amplitude").css("background","url(../img/dor_icon01.png) no-repeat right center");
//                }else{
//                    $(".total_consumption_amplitude").html(addpic.substr(1)+"%");
//                    $(".total_consumption_amplitude").css("color","green");
//                    $(".total_consumption_amplitude").css("background","url(../img/dor_icon02.png) no-repeat right center");
//                }
            chart1(result.week.week, result.week.number);
            chart2(result.week.week, result.week.numberPaid);
            chart3(result.week.week, result.week.amount, result.week.amountPaid);
            chart4(result.number);
            chart5(result.amount.days, result.amount.data);
            chart6(result.province.provinces, result.province.data);
        }
    );
});
function chart1(xData, sDate) {
    var myChart = echarts.init(document.getElementById('container_charts'));
    var option = {
        tooltip: {
            trigger: 'axis'
        },
        grid: {
            x: 40,
            y: 10,
            x2: 50,
            y2: 20
        },
        calculable: true,
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: xData
        },
        yAxis: {
            type: 'value'
        },
        series: {
            name: '订单',
            type: 'line',
            smooth: true,
            itemStyle: {
                normal: {
                    color: '#06A7E1',
                    areaStyle: {
                        color: 'rgba(205,237,249,0.6)'
                    },
                    lineStyle: {
                        color: '#06A7E1'
                    }
                }
            },
            data: sDate
        }
    };
    myChart.setOption(option);
}
function chart2(xData, sData) {
    var myChart2 = echarts.init(document.getElementById('pay_charts'));
    var option2 = {
        tooltip: {
            trigger: 'axis'
        },
        grid: {
            x: 40,
            y: 10,
            x2: 50,
            y2: 20
        },
        calculable: true,
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: xData
        },
        yAxis: {
            type: 'value'
        },
        series: {
            name: '已付款订单',
            type: 'line',
            smooth: true,
            itemStyle: {
                normal: {
                    color: '#06A7E1',
                    areaStyle: {
                        color: 'rgba(205,237,249,0.6)'
                    },
                    lineStyle: {
                        color: '#06A7E1'
                    }
                }
            },
            data: sData
        }
    };
    myChart2.setOption(option2);
}
function chart3(xData, sData1, sData2) {
    var myChart3 = echarts.init(document.getElementById('total_consumption_charts'));
    var option3 = {
        tooltip: {
            trigger: 'axis'
        },
        grid: {
            x: 50,
            y: 20,
            x2: 30,
            y2: 20
        },
        legend: {
            data: ['总订单金额', '已付款金额']
        },
        calculable: true,
        xAxis: {
            type: 'category',
            data: xData
        },
        yAxis: {
            type: 'value',
            splitArea: {show: true}
        },
        series: [
            {
                name: '总订单金额',
                type: 'bar',
                itemStyle: {
                    normal: {
                        color: '#06A7E1'
                    }
                },
                data: sData1
            }, {
                name: '已付款金额',
                type: 'bar',
                itemStyle: {
                    normal: {
                        color: '#E4214A'
                    }
                },
                data: sData2
            }
        ]
    };
    myChart3.setOption(option3);
}
function chart4(data) {
    var myChart4 = echarts.init(document.getElementById('consumption_cake_charts'));
    var option4 = {
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
            orient: 'vertical',
            x: 'left',
            data: ['未支付', '已支付']
        },
        calculable: true,
        series: [{
            name: '订单',
            type: 'pie',
            radius: '55%',
            center: ['50%', '60%'],
            data: [{
                value: data.other,
                name: '未支付',
                itemStyle: {
                    normal: {
                        color: '#06A7E1'
                    }
                }
            }, {
                value: data.paid,
                name: '已支付',
                itemStyle: {
                    normal: {
                        color: '#E4214A'
                    }
                }
            }]
        }]
    };
    myChart4.setOption(option4);
}
function chart5(xData, sData) {
    var myChart5 = echarts.init(document.getElementById('sales_volume'));
    var option5 = {
        tooltip: {
            trigger: 'axis'
        },
        grid: {
            x: 80,
            y: 10,
            x2: 40,
            y2: 20
        },
        calculable: true,
        xAxis: [
            {
                type: 'category',
                boundaryGap: false,
                data: xData
            }
        ],
        yAxis: [
            {
                type: 'value'
            }
        ],
        series: [
            {
                name: '订单',
                type: 'line',
                smooth: true,
                itemStyle: {
                    normal: {
                        color: '#06A7E1',
                        areaStyle: {
                            color: 'rgba(205,237,249,0.6)'
                        },
                        lineStyle: {
                            color: '#06A7E1'
                        }
                    }
                },
                data: sData
            }
        ]
    };
    myChart5.setOption(option5);
}
// +1号图表
function chart6(xData,yDate) {
    var myChart = echarts.init(document.getElementById('orders'));
    var option = {
        title: {
            text: "各省（地区）订单分析",
            subtext: "总订单数：单"
        },
        tooltip: {
            trigger: 'axis'
        },
        grid: {
            x: 40,
            y: 10,
            x2: 40,
            y2: 20
        },
        calculable: true,
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: xData
        },
        yAxis: {
            type: 'value'
        },
        series: {
            name: '订单',
            type: 'bar',
            smooth: true,
            itemStyle: {
                normal: {
                    color: '#06A7E1',
                    areaStyle: {
                        color: 'rgba(205,237,249,0.6)'
                    },
                    lineStyle: {
                        color: '#06A7E1'
                    }
                }
            },
            data: yDate
        }
    };
    myChart.setOption(option);
}
// 选择省的时候把市传过来
function getCitiesByProvince(province) {
    $.getJSON(
        "cities",
        {province:province},
        function (result) {
            // 先清空
            $("#cities:not(:first-child)").remove();
            for (var i=0;i<result.length;i++) {
                var option = "<option value=\""+result[i]+"\">"+result[i]+"</option>";
                $("#cities").append(option);
            }
        }
    );
}
// 搜索（+1号图表）
function search1() {
    $.getJSON(
        "getNumberOfProvince",
        {
            startDate:$("#TSbegintime1").val(),
            endDate:$("#TSendtime1").val(),
            province:$("#provinces").val(),
            city:$("#cities").val(),
            status:$("#order_status1").val()
        },
        function (result) {
            chart6(result.place,result.number);
        }
    );
}
// 搜索（+2号图表）
function search2() {
    $.getJSON(
        "volume",
        {
            startDate: $("#TSbegintime2").val(),
            endDate: $("#TSendtime2").val(),
            dateType: $("#dateType").val(),
            status: $("#order_status2").val()
        },
        function (result) {
            chart5(result.dates, result.data);
        }
    );
}