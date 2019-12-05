<?php

$post = Array
(
    'action'=>'add',
    'type_id'=>'43',
    'store_id'=>'1',
    'default_data'=>Array
        (
            'titles'=>'商品名称',
            'prices'=>'价格',
            'unit'=>'单位',
            'location_id'=>'',
            'content'=>'图文详情'
        ),

    'system_data'=>Array
        (
            '07cdfd23373b17c6b337251c22b7ea57'=>Array
                (
                    '0'=>'0f49c89d1e7298bb9930789c8ed59d48',
                    '1'=>'46ba9f2a6976570b0353203ec4474217',
                ),

            '0e01938fc48a2cfb5f2217fbfb00722d'=>'注意事项',
            'fb7b9ffa5462084c5f4e7e85a093e6d7'=>'其他标签',
            'd709f38ef758b5066ef31b18039b8ce5'=>'详细地址',
        ),

    'prices_de'=>Array
        (
            '280'=>'一小时',
            '281'=>'一天',
            '282'=>'一次',
        ),

    'upload_imgs_0'=>Array
        (
            '0'=>'http://image19-d.yueus.com/yueyue/20151105/20151105171231_737571_10002_38169_320.jpg?310x206_120',
            '1'=>'http://image19-d.yueus.com/yueyue/20151105/20151105171231_880280_10002_38170_320.jpg?352x220_120',
        ),

    'IP_ADDRESS'=>'116.6.198.215',
    'IP_ADDRESS1'=>'116.6.198.215',
    'request_method'=>'post',
    's'=>'c67a75f8a3c7b97c17785e291e2a70c5',
    'img'=>Array
        (
            '0'=>Array
                (
                    'img_url'=>'http://image19-d.yueus.com/yueyue/20151105/20151105171231_737571_10002_38169_320.jpg?310x206_120'
                ),

            '1'=>Array
                (
                    'img_url'=>'http://image19-d.yueus.com/yueyue/20151105/20151105171231_880280_10002_38170_320.jpg?352x220_120'
                ),

        ),

);
echo "<pre>";
print_r($post);