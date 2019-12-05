<?php 

$post = Array
(
    'action'=>' add',
    'type_id'=>' 31',
    'store_id'=>' 1',
    'default_data'=>Array
        (
            'titles'=>' 商品名称',
            'prices'=>' 价格',
            'unit'=>' 单位',
            'location_id'=>'', 
            'content'=>' 图文详情',
        ),

    'system_data'=>Array
        (
            'd9d4f495e875a2e075a1a4a6e1b9770f'=>' 67c6a1e7ce56d3d6fa748ab6d9af3fd7',
            '66f041e16a60928b05a7e228a89c3799'=>' 每次拍摄人数不得超过',
            '16a5cdae362b8d27a1d8f8c7b78b4330'=>' 起拍件数',
        ),

    'prices_de'=>Array
        (
            '76'=>' 两小时',
            '77'=>' 四小时',
            '288'=>' 一件',
            '287'=>' 半天',
            '87'=>' 一天',
        ),

    'upload_imgs_0'=>Array
        (
            '0'=>' http://image19-d.yueus.com/yueyue/20151105/20151105164029_377526_10002_38083_320.jpg?310x206_120',
            '1'=>' http://image19-d.yueus.com/yueyue/20151105/20151105164029_541997_10002_38084_320.jpg?352x220_120',
        ),

    'IP_ADDRESS'=>' 116.6.198.215',
    'IP_ADDRESS1'=>' 116.6.198.215',
    'request_method'=>' post',
    's'=>' c67a75f8a3c7b97c17785e291e2a70c5',
    'img'=>Array
        (
            '0'=>Array
                (
                    'img_url'=>' http://image19-d.yueus.com/yueyue/20151105/20151105164029_377526_10002_38083_320.jpg?310x206_120'
                ),

            '1'=>Array
                (
                    'img_url'=>' http://image19-d.yueus.com/yueyue/20151105/20151105164029_541997_10002_38084_320.jpg?352x220_120',
                ),

        ),

);
echo "<pre>";
print_r($post);