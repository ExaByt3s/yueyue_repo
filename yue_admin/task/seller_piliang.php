<?php

include_once 'common.inc.php';
exit('die');
$mall_basic_check_obj = POCO::singleton('pai_mall_certificate_basic_class');
//$mall_basic_check_obj->set_debug();

$old_seller = POCO::singleton('pai_task_seller_class');
$seller_list = $old_seller->get_seller_all_list();
$type = array(
              1=>'12',
              2=>'5',
              3=>'3',
              5=>'3,12',
              6=>'2',
			  );

$new_seller = POCO::singleton('pai_mall_seller_class');
//$new_seller->set_debug();
foreach($seller_list as $val)
{
	////
	$task_profile_obj = POCO::singleton('pai_task_profile_class');
	$profile_info = $task_profile_obj->get_profile_info($val['user_id'], $val['service_id']);
	////
	$get_db_data[] = array(
	                       'user_id'=>$val['user_id'],
	                       'type_id'=>$type[$val['service_id']],
	                       'data'=>array(
						                 'introduce'=>$profile_info['bio_content'],
						                 'avatar'=>$profile_info['avatar'],
						                 'cover'=>$profile_info['cover'],
										 ),
	                       );
/*	
    $re = $new_seller->get_seller_info($val['user_id'],2);
	echo $val['user_id'].'-->'.$re['seller_data']['seller_id']."<br>";
*/
}
//exit;

//print_r($get_db_data);			  
//exit;
//�����̼�����demo
foreach($get_db_data as $k => $v)
{
    $rs = $mall_basic_check_obj->batch_open_service($v['user_id'],$v['type_id'],$v['data']);
    if($rs['status'] != 1)
    {
        echo "������Ϣ:";
        echo $rs['msg'];
        echo "</br>";
    }
	else
	{
		echo "user_id:".$v['user_id']."&nbsp;&nbsp;&nbsp;&nbsp;-->".$v['type_id']."&nbsp;&nbsp;&nbsp;&nbsp;ok<br>";
	}
}
exit(time().'ok');


/////////////////////////////////////�����Ʒdemo
$goods_obj = POCO::singleton('pai_mall_goods_class');
//$goods_obj->set_debug();
$goods_data = array(
                    'store_id' => 1,//����ID
                    'type_id' => 31,//��Ʒtype_id 31Ϊģ��
                    'default_data' => array(
					                        'titles'=>time()."��Ʒ����",
											),
                    'system_data' => array(
					                        '66f041e16a60928b05a7e228a89c3799'=>rand(1,5),//ÿ�β��ܳ���
											),
                    'prices_de' => array(
					                        76=>100,//��Сʱ
					                        77=>201,//��Сʱ
					                        87=>302,//һ��
											),
                    'img' => array(
								   array('img_url'=>'http://image16-d.poco.cn/yueyue/20150707/201507071415434925164_260.jpg?1024x768_120'),//����
								   array('img_url'=>'http://image16-d.poco.cn/yueyue/20150707/2015070714203073541651_260.jpg?1024x768_120'),
								   ),											
					);

$goods_id=$goods_obj->add_goods($goods_data);
print_r($goods_id);
echo "<br>";
///////////////////////////////////
?>