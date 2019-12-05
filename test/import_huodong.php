<?php


include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$seller_obj = POCO::singleton('pai_mall_seller_class');
$basic_obj = POCO::singleton('pai_mall_certificate_basic_class');

$mall_type_obj = POCO::singleton('pai_mall_type_id_goods_data_class');


$sql = "select * from test.test_event_info_tbl where status=0 limit 1";
$import_event = db_simple_getdata($sql);

foreach($import_event  as $event_val)
{
    $event_id = $event_val['event_id'];


    $sql = "update test.test_event_info_tbl set status=2 where event_id=$event_id";
    db_simple_getdata($sql);


    $event_info = get_event_by_event_id($event_id);

    if(!$event_info['user_id'])
    {
        continue;
    }


    $act_type = act_type($event_info['title']);

    $ret = $basic_obj->batch_open_service($event_info['user_id'], 1, $data = array());
    print_r($ret);

    $ret = $basic_obj->batch_update_seller_type_id($event_info['user_id'], array(42));
    print_r($ret);


    foreach ($event_info['other_info_detail'] as $val) {
        $content .= $val['text'] . "<br />";
        foreach ($val['img'] as $img) {
            $content .= "<img src=\"$img[img_l]\" /><br /><br />";
        }
        $content .= "<br />";
    }

    foreach ($event_info['leader_info_detail'] as $k => $val) {
        $contact_data[$k]['name'] = $val['name'];
        $contact_data[$k]['phone'] = $val['phone'];
    }


    foreach ($event_info['table_info'] as $k => $val) {
        $num = $k + 1;

        $price_arr['name'] = array("第{$num}场价格");
        $price_arr['prices'] = array($event_info['budget']);

        $tk = time().rand(100000,999999);
        $table_info[$tk]['name'] = "第{$num}场";
        $table_info[$tk]['time_s'] = date("Y-m-d H:i:s",$val['begin_time']);
        $table_info[$tk]['time_e'] = date("Y-m-d H:i:s",$val['end_time']);
        $table_info[$tk]['stock_num'] = $val['num'];
        $table_info[$tk]['detail'] = $price_arr;

    }


    $store_id = $seller_obj->get_seller_store_id($event_info['user_id']);


    if($event_info['other_info_detail'][0]['img'][0]['img_l'])
    {
        $cover_image = $event_info['other_info_detail'][0]['img'][0]['img_l'];
    }
    else
    {
        $cover_image = $event_info['cover_image'];
    }

    $post =
        Array
        (
            'action' => 'add',
            'type_id' => '42',
            'store_id' => $store_id,
            'default_data' => Array
            (
                'titles' => $event_info['title'],
                'prices' => $event_info['budget'],
                'unit' => '',
                'location_id' => $event_info['location_id'],
                'content' => $content,
            ),

            'system_data' => Array
            (
                '39059724f73a9969845dfe4146c5660e' => $act_type['b_category'], //主题类别
                'd947bf06a885db0d477d707121934ff8' => $act_type['s_category'], //主题类别详情
                '7a614fd06c325499f1680b9896beedeb' => $event_info['address'],
                '4734ba6f3de83d861c3176a6273cac6d' => $event_info['remark'],
                '00ec53c4682d36f5c4359f4ae7bd7ba1' => $event_info['location_id'],
            ),

            'contact_data' => $contact_data,

            'prices_diy' => $table_info,


            'IP_ADDRESS' => '116.6.198.215',
            'IP_ADDRESS1' => '116.6.198.215',
            'request_method' => 'post',
            's' => 'c67a75f8a3c7b97c17785e291e2a70c5',
            'img' => Array
            (
                '0' => Array
                (
                    'img_url' => $cover_image,
                )
            )

        );

    print_r($post);

    unset($content);

    $goods_id = $mall_type_obj->type_id_good_data_insert($post,$is_pass = 0,$is_show = 0);

    var_dump($goods_id);

    $sql = "update test.test_event_info_tbl set status=1,goods_id='$goods_id' where event_id=$event_id";
    db_simple_getdata($sql);

    $goods_info = POCO::singleton('pai_mall_goods_class')->get_goods_info($goods_id);
    foreach($goods_info['goods_data']['prices_de'] as $k=>$val)
    {
        $table_id = $event_info['table_info'][$k]['id'];
        $stage_id = $val['type_id'];
        $price_id= $val['prices_list_data'][0]['id'];
        $sql = "insert into test.test_event_info_stage_tbl set goods_id='{$goods_id}',table_id='{$table_id}',stage_id='{$stage_id}',event_id='{$event_id}',price_id='{$price_id}'";
        db_simple_getdata($sql);
    }

}



function act_type($title)
{
    if(preg_match('/室内/',$title))
    {
        $ret['b_category'] = "d947bf06a885db0d477d707121934ff8";
        $ret['s_category'] = "41f1f19176d383480afa65d325c06ed0";
    }
    elseif(preg_match('/室外/',$title))
    {
        $ret['b_category'] = "d947bf06a885db0d477d707121934ff8";
        $ret['s_category'] = "8bf1211fd4b7b94528899de0a43b9fb3";
    }
    elseif(preg_match('/旅拍/',$title))
    {
        $ret['b_category'] = "d947bf06a885db0d477d707121934ff8";
        $ret['s_category'] = "a02ffd91ece5e7efeb46db8f10a74059";
    }
    elseif(preg_match('/人像|私房|外拍/',$title))
    {
        $ret['b_category'] = "d947bf06a885db0d477d707121934ff8";
        $ret['s_category'] = "bca82e41ee7b0833588399b1fcd177c7";
    }elseif(preg_match('/美食/',$title))
    {
        $ret['b_category'] = "24b16fede9a67c9251d3e7c7161c83ac";
        $ret['s_category'] = "";
    }elseif(preg_match('/宠物/',$title))
    {
        $ret['b_category'] = "ffd52f3c7e12435a724a8f30fddadd9c";
        $ret['s_category'] = "";
    }elseif(preg_match('/亲子/',$title))
    {
        $ret['b_category'] = "ad972f10e0800b49d76fed33a21f6698";
        $ret['s_category'] = "";
    }elseif(preg_match('/户外/',$title))
    {
        $ret['b_category'] = "f61d6947467ccd3aa5af24db320235dd";
        $ret['s_category'] = "";
    }
    else
    {
        $ret['b_category'] = "d947bf06a885db0d477d707121934ff8";
        $ret['s_category'] = "bca82e41ee7b0833588399b1fcd177c7";
    }

    return $ret;
}
//
?>
<script> setTimeout("location.reload()",1000);</script>


