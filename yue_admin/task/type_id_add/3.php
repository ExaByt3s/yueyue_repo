<?php

$post = Array
(
    'action'=>'add',
    'type_id'=>'3',
    'store_id'=>'1',
    'default_data'=>Array
        (
            'titles'=>'商品名称',
            'prices'=>'价格',
            'unit'=>'单位',
            'location_id'=>'',
            'content'=>'图文详情',
        ),

    'system_data'=>Array
        (
            'a3f390d88e4c41f2747bfa2f1b5f87db'=>'14bfa6bb14875e45bba028a21ed38046',
            '7cbbc409ec990f19c78c75bd1e06f215'=>'化妆品',
            'e2c420d928d4bf8ce0ff2ec19b371514'=>'耗时',
        ),

    'prices_de'=>Array
        (
            '153'=>'单妆',
            '154'=>'跟妆半天',
            '155'=>'跟妆一天',
        ),

    'upload_imgs_0'=>Array
        (
            '0'=>'http://image19-d.yueus.com/yueyue/20151105/20151105164802_151312_10002_38115_320.jpg?310x206_120',
            '1'=>'http://image19-d.yueus.com/yueyue/20151105/20151105164802_382048_10002_38116_320.jpg?352x220_120',
        ),

    'IP_ADDRESS'=>'116.6.198.215',
    'IP_ADDRESS1'=>'116.6.198.215',
    'request_method'=>'post',
    's'=>'c67a75f8a3c7b97c17785e291e2a70c5',
    'img'=>Array
        (
            '0'=>Array
                (
                    'img_url'=>'http://image19-d.yueus.com/yueyue/20151105/20151105164802_151312_10002_38115_320.jpg?310x206_120',
                ),

            '1'=>Array
                (
                    'img_url'=>'http://image19-d.yueus.com/yueyue/20151105/20151105164802_382048_10002_38116_320.jpg?352x220_120',
                ),

        ),

);
echo "<pre>";
print_r($post);
