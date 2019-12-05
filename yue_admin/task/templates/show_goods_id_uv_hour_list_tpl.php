<!DOCTYPE html>
<head>
    <meta charset="gbk">
    <title>ĳ���uvʱ������</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h3>
        <?php echo $date;?>,uvʱ������,����(<?php echo $list['date'][$date];?>)
    </h3>
    <!-- ΪECharts׼��һ���߱���С����ߣ���Dom -->
    <div id="main" style="height:400px"></div>
    <!-- ECharts���ļ����� -->
    <script src="js/echarts2/dist/echarts.js"></script>
    <script type="text/javascript">
        // ·������
        require.config({
            paths: {
                echarts: 'js/echarts2/dist'
            }
        });
        
        // ʹ��
        require(
            [
                'echarts',
                'echarts/chart/bar' // ʹ����״ͼ�ͼ���barģ�飬�������
            ],
            function (ec) {
                // ����׼���õ�dom����ʼ��echartsͼ��
                var myChart = ec.init(document.getElementById('main')); 
                
                var option = {
                    tooltip: {
                        show: true
                    },
                    legend: {
                        data:['ʱ��uv����']
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
                            "name":"ʱ��uv����",
                            "type":"bar",
                            "data":[
								<?php echo $rs_str;?>
                            ]
                        }
                    ]
                };
        
                // Ϊecharts����������� 
                myChart.setOption(option); 
            }
        );
    </script>
</body>