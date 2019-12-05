<?php

$post = Array
(
    'action'=>'add',
    'type_id'=>'5',
    'store_id'=>'1',
    'default_data'=>Array
        (
            'titles'=>'商品名称',
            'prices'=>'价格',
            'unit'=>'单位',
            'stock_num'=>'库存',
            'location_id'=>'',
            'content'=>'图文详情',
        ),

    'system_data'=>Array
        (
            '093f65e080a295f8076b1c5722a46aa2'=>'课程目标',
            '072b030ba126b2f4b2374f342be9ed44'=>'2015-11-06', 
            '7f39f8317fbdb1988ef4c628eba02591'=>'上课时间',
            '44f683a84163b3523afe57c2e008bc8c'=>'03afdbd66e7929b125f8597834fa83a4',
            'fc490ca45c00b1249bbe3554a4fdf6fb'=>'上课地址',
            '3295c76acbf4caaed33c36b1b5fc2cb1'=>'线上联系方式',
            '2a38a4a9316c49e5a833517c45d31070'=>'课程周期',
            '7647966b7343c29048673252e490f736'=>'课程数量',
            '9fc3d7152ba9336a670e36d0ed79bc43'=>'02522a2b2726fb0a03bb19f2d8d9524d',
            '02522a2b2726fb0a03bb19f2d8d9524d'=>Array
                (
                    '0'=>'9be40cee5b0eee1462c82c6964087ff9',
                    '1'=>'5ef698cd9fe650923ea331c15af3b160',
                ),

            'caf1a3dfb505ffed0d024130f58c5cfa'=>'每课时长',
            '5737c6ec2e0716f3d8a7a5c4e0de0d9a'=>'课程亮点',
            'c058f544c737782deacefa532d9add4c'=>'备注',
            'e7b24b112a44fdd9ee93bdf998c6ca0e'=>'培训时间从',
            '52720e003547c70561bf5e03b95aa99f'=>'培训时间到',
            '735b90b4568125ed6c3f678819b6e058'=>'课程大纲',
        ),

    'upload_imgs_0'=>Array
        (
            '0'=>'http://image19-d.yueus.com/yueyue/20151105/20151105165954_591806_10002_38154_320.jpg?310x206_120',
            '1'=>'http://image19-d.yueus.com/yueyue/20151105/20151105165954_885194_10002_38155_320.jpg?352x220_120',
        ),

    'IP_ADDRESS'=>'116.6.198.215',
    'IP_ADDRESS1'=>'116.6.198.215',
    'request_method'=>'post',
    's'=>'c67a75f8a3c7b97c17785e291e2a70c5',
    'img'=>Array
        (
            '0'=>Array
                (
                    'img_url'=>'http://image19-d.yueus.com/yueyue/20151105/20151105165954_591806_10002_38154_320.jpg?310x206_120',
                ),

            '1'=>Array
                (
                    'img_url'=>'http://image19-d.yueus.com/yueyue/20151105/20151105165954_885194_10002_38155_320.jpg?352x220_120',
                ),

        ),

);
echo "<pre>";
print_r($post);