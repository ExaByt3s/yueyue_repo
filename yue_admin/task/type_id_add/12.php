<?php

$post = Array
(
   'action'=>'add',
   'type_id'=>'12',
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
           '320722549d1751cf3f247855f937b982'=>'影棚地址',
           '1f0e3dad99908345f7439f8ffabdffc4'=>'使用面积',
           '98f13708210194c475687be6106a3b84'=>'202cb962ac59075b964b07152d234b70',
           '70efdf2ec9b086079795c442636b55fb'=>'3def184ad8f4755ff269862ea77393dd',
           '6f4922f45568161a8cdf4ad2299f6d23'=>'灯光/器材配套',
        ),

   'prices_de'=>Array
        (
           '72'=>'一小时',
           '73'=>'半天',
           '74'=>'一天',
        ),

   'upload_imgs_0'=>Array
        (
           '0'=>'http://image19-d.yueus.com/yueyue/20151105/20151105165345_73515_10002_38129_320.jpg?310x206_120',
           '1'=>'http://image19-d.yueus.com/yueyue/20151105/20151105165345_265879_10002_38130_320.jpg?352x220_120',
        ),

   'IP_ADDRESS'=>'116.6.198.215',
   'IP_ADDRESS1'=>'116.6.198.215',
   'request_method'=>'post',
   's'=>'c67a75f8a3c7b97c17785e291e2a70c5',
   'img'=>Array
        (
           '0'=>Array
                (
                   'img_url'=>'http://image19-d.yueus.com/yueyue/20151105/20151105165345_73515_10002_38129_320.jpg?310x206_120',
                ),

           '1'=>Array
                (
                   'img_url'=>'http://image19-d.yueus.com/yueyue/20151105/20151105165345_265879_10002_38130_320.jpg?352x220_120',
                ),

        ),

);
echo "<pre>";
print_r($post);