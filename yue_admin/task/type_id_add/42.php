<?php

$data = '';
//42 活动
$post = 
        Array
(
  'action' => 'add',
  'type_id' => '42',
  'store_id' => '1',
  'default_data' => Array
        (
          'titles' => '商品名称',
          'prices' => '商品价格',
          'unit' => '商品单位',
          'location_id' => '',
          'content' => '图文详情',
        ),

  'system_data' => Array
        (
          '39059724f73a9969845dfe4146c5660e' => 'd947bf06a885db0d477d707121934ff8', //主题类别
          'd947bf06a885db0d477d707121934ff8' => 'bca82e41ee7b0833588399b1fcd177c7', //主题类别详情
          '7a614fd06c325499f1680b9896beedeb' => '活动地址',
          '4734ba6f3de83d861c3176a6273cac6d' => '注意事项',
          '00ec53c4682d36f5c4359f4ae7bd7ba1' => '地址坐标',
        ),

  'contact_data' => Array
        (
          '143935150432' => Array
                (
                  'name' => '联系人',
                  'phone' => '联系方式',
                ),

          '14393515043255' => Array
                (
                  'name' => '联系人2',
                  'phone' => '联系方式 2',
                ),

        ),

  'prices_diy' => Array
        (
          '1439351504434665' => Array
                (
                  'name' => '场次名称',
                  'time_s' => '时间起始',
                  'time_e' => '时间起始',
                  'stock_num' => '名额',
                  'detail' => Array
                        (
                          'name' => Array
                                (
                                  '0' => '价格1名称',
                                  '1' => '价格2名称'
                                ),

                          'prices' => Array
                                (
                                  '0' => '价格',
                                  '1' => '价格'
                                ),

                        )

                ),

          '1439351504437852' => Array
                (
                  'name' => '场次名称',
                  'time_s' => '时间起始',
                  'time_e' => '时间起始',
                  'stock_num' => '名额',
                  'detail' => Array
                        (
                          'name' => Array
                                (
                                  '0' => '价格1名称',
                                  '1' => '价格2名称',
                                ),

                          'prices' => Array
                                (
                                  '0' => '价格',
                                  '1' => '价格',
                                )

                        )

                )

        ),

  'upload_imgs_0' => Array
        (
          '0' => 'http://image19-d.yueus.com/yueyue/20151105/20151105155421_71959_10002_37998_320.jpg?310x206_120',
          '1' => 'http://image19-d.yueus.com/yueyue/20151105/20151105155421_271312_10002_37999_320.jpg?352x220_120'
        ),

  'IP_ADDRESS' => '116.6.198.215',
  'IP_ADDRESS1' => '116.6.198.215',
  'request_method' => 'post',
  's' => 'c67a75f8a3c7b97c17785e291e2a70c5',
  'img' => Array
        (
          '0' => Array
                (
                  'img_url' => 'http://image19-d.yueus.com/yueyue/20151105/20151105155421_71959_10002_37998_320.jpg?310x206_120',
                ),

          '1' => Array
                (
                  'img_url' => 'http://image19-d.yueus.com/yueyue/20151105/20151105155421_271312_10002_37999_320.jpg?352x220_120',
                )

        )

);
echo "<pre>";
print_r($post);