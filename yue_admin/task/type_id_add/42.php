<?php

$data = '';
//42 �
$post = 
        Array
(
  'action' => 'add',
  'type_id' => '42',
  'store_id' => '1',
  'default_data' => Array
        (
          'titles' => '��Ʒ����',
          'prices' => '��Ʒ�۸�',
          'unit' => '��Ʒ��λ',
          'location_id' => '',
          'content' => 'ͼ������',
        ),

  'system_data' => Array
        (
          '39059724f73a9969845dfe4146c5660e' => 'd947bf06a885db0d477d707121934ff8', //�������
          'd947bf06a885db0d477d707121934ff8' => 'bca82e41ee7b0833588399b1fcd177c7', //�����������
          '7a614fd06c325499f1680b9896beedeb' => '���ַ',
          '4734ba6f3de83d861c3176a6273cac6d' => 'ע������',
          '00ec53c4682d36f5c4359f4ae7bd7ba1' => '��ַ����',
        ),

  'contact_data' => Array
        (
          '143935150432' => Array
                (
                  'name' => '��ϵ��',
                  'phone' => '��ϵ��ʽ',
                ),

          '14393515043255' => Array
                (
                  'name' => '��ϵ��2',
                  'phone' => '��ϵ��ʽ 2',
                ),

        ),

  'prices_diy' => Array
        (
          '1439351504434665' => Array
                (
                  'name' => '��������',
                  'time_s' => 'ʱ����ʼ',
                  'time_e' => 'ʱ����ʼ',
                  'stock_num' => '����',
                  'detail' => Array
                        (
                          'name' => Array
                                (
                                  '0' => '�۸�1����',
                                  '1' => '�۸�2����'
                                ),

                          'prices' => Array
                                (
                                  '0' => '�۸�',
                                  '1' => '�۸�'
                                ),

                        )

                ),

          '1439351504437852' => Array
                (
                  'name' => '��������',
                  'time_s' => 'ʱ����ʼ',
                  'time_e' => 'ʱ����ʼ',
                  'stock_num' => '����',
                  'detail' => Array
                        (
                          'name' => Array
                                (
                                  '0' => '�۸�1����',
                                  '1' => '�۸�2����',
                                ),

                          'prices' => Array
                                (
                                  '0' => '�۸�',
                                  '1' => '�۸�',
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