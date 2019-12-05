<?php

set_time_limit(0);
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



/*$user_obj = POCO::singleton ( 'pai_user_class' );

$sql="SELECT user_id FROM pai_db.pai_user_tbl ORDER BY user_id DESC LIMIT 20000;";
$arr_user=db_simple_getdata($sql);
foreach($arr_user as $val)
{
    $arr1[] = $val['user_id'];
}


$str1 = implode(',',$arr1);

$sql = "select user_id from pai_db.pai_relate_poco_tbl where user_id in ($str1)";
$arr_re = db_simple_getdata($sql);
foreach($arr_re as $val)
{
    $arr2[] = $val['user_id'];
}

$ret =array_diff($arr1,$arr2);

//print_r($ret);
echo implode(',',$ret);*/

/*$sql="SELECT * FROM pai_db.pai_activity_code_tbl WHERE event_id=60094;";
$arr=db_simple_getdata($sql);

foreach($arr as $val)
{
    if($val['is_checked']==1)
    {
        $check = '已签到';
    }
    else
    {
        $check = '未签到';
    }
    $user_info = $user_obj->get_user_info($val['enroll_user_id']);
    $phone = $user_info['cellphone'];
    $add_time = date("Y-m-d H:i",$user_info['add_time']);
    echo $val['enroll_user_id'].','.$phone.','.$add_time.','.$check."<br />";
}*/

/*$event_obj = POCO::singleton ( 'event_details_class' );

$data['title'] = "标题5556"; //标题
$data['type_icon'] = "photo"; //分类标识 摄影为photo,美食为food
$data['start_time'] = "1447554600";//开始时间
$data['end_time'] = "1447565400";//结束时间
$data['cover_image'] = "http://img17.poco.cn/mypoco/myphoto/20151112/11/4276589420151112111553032_450.jpg?145x145_120";//封面图，尺寸传145
$data['location_id'] = "101029002";//地区ID
$data['address'] = "地址地址地址";//地址
$data['budget'] = "50";//价格
$data['content'] = "活动内容";//活动内容
$data['goods_id'] = "1234";//
$data['join_count'] = "12";//已参加人数
$data['limit_num'] = "123";//可参加人数

$ret=$event_obj->add_synchronous_event($data);

print_r($ret);*/


/*$sql="SELECT * FROM mall_db.mall_goods_statistical_tbl WHERE old_bill_finish_num!=0";
$arr = db_simple_getdata($sql);

foreach($arr as $val)
{
    $goods_id=$val['goods_id'];
    $old_bill_finish_num = $val['old_bill_finish_num'];
    //$sql = "select * from mall_db.mall_goods_tbl WHERE goods_id={$goods_id};";
    //$goods_info = db_simple_getdata($sql,true);

    $sql="select count(*) as num from mall_db.mall_comment_seller_tbl where goods_id={$goods_id} and order_id=0";
    $comment_arr = db_simple_getdata($sql,true);

    if($comment_arr['num']==0)
    {
        echo $goods_id.",".$comment_arr['num'].",".$old_bill_finish_num."<br />";
    }
}*/

/*$sql = "SELECT * FROM mall_db.mall_seller_profile_tbl WHERE old_bill_finish_num!=0;";
$arr = db_simple_getdata($sql);
foreach($arr as $val)
{
    $sql = "select goods_id from mall_db.mall_goods_tbl where user_id=".$val['user_id'];
    $goods_arr = db_simple_getdata($sql);
    foreach($goods_arr as $g_val)
    {
        $_goods[] = $g_val['goods_id'];
    }
    $goods_str = implode(',',$_goods);
    if(!$goods_str) $goods_str=0;

    $sql = "select sum(old_bill_finish_num) as num from mall_db.mall_goods_statistical_tbl where goods_id in ({$goods_str})";
    $statistical_arr = db_simple_getdata($sql,true);

    if($val['old_bill_finish_num']<$statistical_arr['num'])
    {
        echo $val['user_id'].",".$val['old_bill_finish_num'].",".$statistical_arr['num']."<br />";
    }


    unset($_goods);
}*/

$sql = "select * from test.test_event_info_stage_tbl where price_id=0";
$arr = db_simple_getdata($sql);
foreach($arr as $val)
{
    $goods_info=POCO::singleton('pai_mall_goods_class')->get_goods_info($val['goods_id']);

    $price_id = $goods_info['goods_data']['prices_de'][0]['prices_id'];


    foreach($goods_info['goods_data']['prices_de'] as $g_val)
    {
        $price_id = $g_val['prices_list_data'][0]['id'];
        $stage_id = $g_val['type_id'];
        $sql="update test.test_event_info_stage_tbl set price_id=$price_id where stage_id=".$stage_id;
        db_simple_getdata($sql);
    }
}


?>

