<?php

$post = Array
(
    'action'=>'add',
    'type_id'=>'41',
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
            'c0e190d8267e36708f955d7ab048990d'=>'060ad92489947d410d897474079c1477',
            '077e29b11be80ab57e1a2ecabb7da330'=>'基本菜单',
            '6c9882bbac1c7093bd25041881277658'=>'19f3cd308f1455b3fa09a282e0d496f4',
            '502e4a16930e414107ee22b6198c578f'=>'餐厅名称',
            'cfa0860e83a4c3a763a7e62d825349f7'=>'餐厅地址',
            'a4f23670e1833f3fdb077ca70bbd5d66'=>'联系电话',
            'b1a59b315fc9a3002ce38bbe070ec3f5'=>'导航方式',
            'e56954b4f6347e897f954495eab16a88'=>'导航图片',
            'eda80a3d5b344bc40f3bc04f65b7a357'=>'预约要求',
            '8f121ce07d74717e0b1f21d122e04521'=>'温馨提示',
            'f7664060cc52bc6f3d620bcedc94a4b6'=>'不吃是一种罪',
        ),

    'prices_diy'=>Array
        (
            '1439351504434665'=>Array
                (
                    'name'=>'名称',
                    'time_s'=>'时间起始',
                    'time_e'=>'时间起始',
                    'stock_num'=>'名额',
                    'prices'=>'价格',
                ),

        ),

    'upload_imgs_0'=>Array
        (
            '0'=>'http://image19-d.yueus.com/yueyue/20151105/20151105170539_107195_10002_38156_320.jpg?310x206_120',
            '1'=>'http://image19-d.yueus.com/yueyue/20151105/20151105170539_392413_10002_38157_320.jpg?352x220_120',
        ),

    'IP_ADDRESS'=>'116.6.198.215',
    'IP_ADDRESS1'=>'116.6.198.215',
    'request_method'=>'post',
    's'=>'c67a75f8a3c7b97c17785e291e2a70c5',
    'img'=>Array
        (
            '0'=>Array
                (
                    'img_url'=>'http://image19-d.yueus.com/yueyue/20151105/20151105170539_107195_10002_38156_320.jpg?310x206_120',
                ),

            '1'=>Array
                (
                    'img_url'=>'http://image19-d.yueus.com/yueyue/20151105/20151105170539_392413_10002_38157_320.jpg?352x220_120',
                ),

        ),

);
echo "<pre>";
print_r($post);
