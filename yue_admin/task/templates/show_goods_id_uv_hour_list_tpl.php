<!DOCTYPE html>
<head>
    <meta charset="gbk">
    <title>某天的uv时点详情</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h3>
        <?php echo $date;?>,uv时点详情,总数(<?php echo $list['date'][$date];?>)
    </h3>
    <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
    <div id="main" style="height:400px"></div>
    <!-- ECharts单文件引入 -->
    <script src="js/echarts2/dist/echarts.js"></script>
    <script type="text/javascript">
        // 路径配置
        require.config({
            paths: {
                echarts: 'js/echarts2/dist'
            }
        });
        
        // 使用
        require(
            [
                'echarts',
                'echarts/chart/bar' // 使用柱状图就加载bar模块，按需加载
            ],
            function (ec) {
                // 基于准备好的dom，初始化echarts图表
                var myChart = ec.init(document.getElementById('main')); 
                
                var option = {
                    tooltip: {
                        show: true
                    },
                    legend: {
                        data:['时点uv总数']
                    },
                    xAxis : [
                        {
                            type : 'category',
                            data : [
								"01",
								"02",
								"03",
								"04",
								"05",
								"06",
								"07",
								"08",
								"09",
								"10",
								"11",
								"12",
								"13",
								"14",
								"15",
								"16",
								"17",
								"18",
								"19",
								"20",
								"21",
								"22",
								"23",
								"24",
							]
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series : [
                        {
                            "name":"时点uv总数",
                            "type":"bar",
                            "data":[
								<?php echo $rs_str;?>
                            ]
                        }
                    ]
                };
        
                // 为echarts对象加载数据 
                myChart.setOption(option); 
            }
        );
    </script>
</body>