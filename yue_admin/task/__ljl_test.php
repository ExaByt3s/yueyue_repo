<?php
include_once 'common.inc.php';
$obj = POCO::singleton('pai_mall_admin_user_class');
$rs = $obj->get_acl_user_cache(115203);
dump($rs);
exit;

$obj = POCO::singleton('pai_mall_admin_acl_class');

$rs = $obj->get_md5_row_and_child_list();
dump($rs);
exit;


$obj = POCO::singleton('pai_mall_api_class');
$rs = $obj->get_goods_id_activity_info(2129075);
dump($rs);
exit;

$obj = POCO::singleton('pai_mall_test_ljl_class');
$obj->update_all_parents_childs();
exit;
$rs = $obj->insert_admin_acl();
dump($rs);
exit;

exit;




$obj = POCO::singleton('pai_mall_certificate_service_class');
$rs = $obj->get_loation_id_and_place(115203);
dump($rs);
exit;


$goods_obj = POCO::singleton('pai_mall_goods_class');
dump('org:');
$goods_info = $goods_obj->get_goods_info_by_sql(2129075,115203);
dump($goods_info);
dump("now:");
$rs = $goods_obj->format_goods_data($goods_info);
dump($rs);
exit;

$obj = POCO::singleton('pai_mall_api_class');
$rs = $obj->get_goods_id_activity_info(2129075);
dump($rs);
exit;

$obj = POCO::singleton('pai_mall_goods_class');
$rs = $obj->get_goods_info(2128724);
dump($rs);
exit;
$goods_id_str = '2113952 ,2113285 ,2113949 ,2114033 ,2114253 ,2118666 ,2113868 ,2119722 ,2122898 ,2125264 ,2122662 ,2119707 ,2113266 ,2116316 ,2114559 ,2113286 ,2118241 ,2117520 ,2112746 ,2114008 ,2114265 ,2122033 ,2114264 ,2114007 ,2113816 ,2118905 ,2114266 ,2114268 ,2114035 ,2118243 ,2113817 ,2113951 ,2122691 ,2113708 ,2114267 ,2122686 ,2117518 ,2113869 ,2114034 ,2123077 ,2120071 ,2120069 ,2124069 ,2113267 ,2122230 ,2113871';
$goods_ary = explode(',',$goods_id_str);

foreach($goods_ary as $k => $v)
{
    $goods_info = $obj->get_goods_info($v);
    if( ! empty($goods_info) )
    {
        $type_id = $goods_info['goods_data']['type_id'];
        $goods_id = $v;
        $obj->exec_cmd_pai_mall_synchronous_goods($goods_id,$type_id,2);
        unset($type_id);
        unset($goods_id);
    }
    
}
exit('ok');
$rs = $obj->add_type_id_black_list(115203,43);
$rs = $obj->remove_type_id_black_list(115203,43);
dump($rs);
exit;


$obj = POCO::singleton('pai_mall_api_class');
$rs = $obj->get_goods_id_activity_info(2128724);
dump($rs);

$rs = $obj->get_goods_id_screenings_price_max_and_min(2128724,14477247420);
dump($rs);

exit;

$obj = POCO::singleton('pai_mall_test_ljl_class');
$rs = $obj->copy_goods_info_to_user_id(2125485,115203);
dump($rs);
exit;

$rs1 = $obj->seller_goods_total_point_and_goods_statistical_step_and_seller_step_new(106347);
dump($rs1);
$rs2 = $obj->seller_goods_total_point_and_goods_statistical_step_and_seller_step_new(104643);
dump($rs2);
exit;

$ls = $obj->test(new A());
dump($ls);
exit;
$res = array_reverse($ls);
dump($res);
exit;

dump($obj->get_goods_id_screenings_price_max_and_min(2125796,1447056410911049));
exit;

$rs = unserialize('a:2:{i:0;a:6:{s:4:"name";s:4:"标签";s:3:"key";s:8:"ot_label";s:4:"mess";s:0:"";s:5:"input";i:2;s:12:"data_type_id";i:1;s:10:"child_data";a:17:{i:0;s:8:"运动塑身";i:1;s:8:"音乐舞蹈";i:2;s:8:"艺术手工";i:3;s:8:"情调小资";i:4;s:8:"传统技艺";i:5;s:8:"商务协助";i:6;s:8:"创业导师";i:7;s:8:"婚礼策划";i:8;s:8:"营养规划";i:9;s:8:"情感咨询";i:10;s:8:"派对策划";i:11;s:8:"命理占星";i:12;s:4:"陪玩";i:13;s:4:"陪吃";i:14;s:6:"陪运动";i:15;s:6:"陪读书";i:16;s:4:"其他";}}i:1;a:5:{s:4:"name";s:8:"其他标签";s:3:"key";s:13:"ot_otherlabel";s:4:"mess";s:0:"";s:5:"input";i:3;s:12:"data_type_id";i:1;}}');
foreach($rs as $k => $v)
{
    if($v['key'] == 'ot_label' )
    {
        foreach($v['child_data'] as $ck => $cv)
        {
            if($ck == 16)
            {
                $rs[$k]['child_data'][$ck] = '摄影行家';
                $rs[$k]['child_data'][$ck+1] = '其他';
            }
        }
    }
    
}
echo serialize($rs);
dump($rs);
exit;

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
                                  '0' => '10',
                                  '1' => '100'
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
                                  '0' => '10',
                                  '1' => '100',
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

$obj = POCO::singleton('pai_mall_goods_class');
$rs = $obj->add_goods($post);
dump($rs);
exit;



$rs = $obj->get_mall_goods_check(2125485);
dump($rs);
dump("goods_info:");
$ls = $obj->get_goods_info(2125485);
dump($ls);

exit;
exit;
$rs1 = $obj->get_service_status_by_user_id(101615,false);
$rs2 = $obj->get_service_status_by_user_id(101615,false,"goods");
dump($rs1);
dump($rs2);
exit;




$rs = $obj->uv_check_is_one_day_repeat(2122798);
dump(md5(2122798));
dump($rs);
exit;

//$obj = POCO::singleton('pai_mall_goods_class');
//$ls = $obj->user_search_goods_list();
//dump($ls);
//exit;
//
$obj = POCO::singleton('pai_mall_certificate_service_class');
$ls = $obj->get_service_status_by_user_id(115203,true);
dump($ls);
exit;
$obj = POCO::singleton('pai_mall_seller_class');
$ls = $obj->user_search_seller_list();
dump($ls);
exit;
$obj = POCO::singleton('pai_mall_goods_class');
$ls = $obj->goods_data_for_front_packing($ls);
dump($ls);
exit;

$obj = POCO::singleton('pai_mall_statistical_class');
$rs = $obj->select_which_tbl(201512);
dump($rs);
exit;

$obj = POCO::singleton('pai_mall_goods_type_class');
$rs = $obj->get_type_cate(2,'all');
dump($rs);
exit;
echo "</br>";

$ls = $obj->uv_get_cache($goods_id);
dump($ls);

$obj->uv_batch_insert_goods_uv_log();
exit;


error_reporting(1);
set_time_limit(0);
ini_set('memory_limit', '256M');

$obj = POCO::singleton('pai_mall_goods_type_class');
$rs = $obj->get_first_cate();
//dump($rs);

$obj = POCO::singleton('pai_mall_goods_type_attribute_class');
$rs = $obj->property_for_search_get_data($type_id);
dump($rs);
exit;

$obj = POCO::singleton('pai_mall_seller_member_level_class');
$rs = $obj->seller_goods_total_point_and_goods_statistical_step_and_seller_step_new(121033);
dump($rs);
exit('ok');

//$obj = POCO::singleton('pai_mall_goods_type_class');
//$rs = $obj->get_first_cate();
//dump($rs);

$ary = array(
    't1'=>'t1',
    't2'=>'t2',
    '116'=>'116',
);
$ary2 = array(
    't1',
    't2',
    '116',
);

$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."ljl_test.tpl.htm" );

$tpl->assign ('ary',$ary2);

$tpl->output ();

?>

