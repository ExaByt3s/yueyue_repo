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
//插入商家数据demo
foreach($get_db_data as $k => $v)
{
    $rs = $mall_basic_check_obj->batch_open_service($v['user_id'],$v['type_id'],$v['data']);
    if($rs['status'] != 1)
    {
        echo "错误信息:";
        echo $rs['msg'];
        echo "</br>";
    }
	else
	{
		echo "user_id:".$v['user_id']."&nbsp;&nbsp;&nbsp;&nbsp;-->".$v['type_id']."&nbsp;&nbsp;&nbsp;&nbsp;ok<br>";
	}
}
exit(time().'ok');


/////////////////////////////////////添加商品demo
$goods_obj = POCO::singleton('pai_mall_goods_class');
//$goods_obj->set_debug();
$goods_data = array(
                    'store_id' => 1,//店铺ID
                    'type_id' => 31,//商品type_id 31为模特
                    'default_data' => array(
					                        'titles'=>time()."商品名称",
											),
                    'system_data' => array(
					                        '66f041e16a60928b05a7e228a89c3799'=>rand(1,5),//每次不能超过
											),
                    'prices_de' => array(
					                        76=>100,//两小时
					                        77=>201,//四小时
					                        87=>302,//一天
											),
                    'img' => array(
								   array('img_url'=>'http://image16-d.poco.cn/yueyue/20150707/201507071415434925164_260.jpg?1024x768_120'),//封面
								   array('img_url'=>'http://image16-d.poco.cn/yueyue/20150707/2015070714203073541651_260.jpg?1024x768_120'),
								   ),											
					);

$goods_id=$goods_obj->add_goods($goods_data);
print_r($goods_id);
echo "<br>";
///////////////////////////////////
?>