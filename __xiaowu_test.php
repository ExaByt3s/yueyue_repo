<?php
ignore_user_abort(true);
set_time_limit(3600);
ini_set('memory_limit', '512M');
error_reporting(-1);
//define('G_PAI_ECPAY_DEV', 1);
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
// require_once(TASK_MALL_APPROOT.TASK_INCLUDE_ROOT.'basics.fun.php');

$op = trim($_INPUT['op']);
if ($op=='adfqw4ert2F2adg') {
    $order_obj = POCO::singleton('pai_mall_order_class');
    $order_info = $order_obj->get_order_full_info('581783993');
    var_dump($order_info);
    die();
    $send_data = array(
        'media_type' => 'text', // (text:����; notify:ϵͳ֪ͨ; card:��Ƭ)
        'content' => '���ã���ϲ����á�1Ԫ����񡱻50Ԫ�Ż݄����Ͻ���ѡ�������Ʒʹ�ðɣ�', // (����media_type=card�ģ����ϱ���)
    );
    POCO::singleton('pai_information_push')->message_sending_for_system(111917, $send_data, 10002, 'yuebuyer');
    die();

    $send_data = array(
        'media_type' => 'card', // (text:����; notify:ϵͳ֪ͨ; card:��Ƭ)
        'card_style' => 2, // (1:�м��н���Ǹ�ģ��; 2:ֻ��һ�������Ǹ�ģ��)
        'card_text1' => '1220093', // (����media_type=card�ģ����ϱ���)
        'card_title' => 'cev', // (����media_type=card�ģ��ײ�)
        'link_url' => 'yueyue://goto?type=inner_app&pid=1220093',
    );
    POCO::singleton('pai_information_push')->message_sending_for_system(111917, $send_data, 10002, 'yuebuyer');

    die();
    $order_obj = POCO::singleton('pai_mall_order_class');
    $order_info = $order_obj->get_order_full_info('34358627');
    // $order_info['seller_user_id'] = 129342;
    // $order_info['total_amount'] = 2111.00;
    // $order_info['pending_amount'] = 700.00;
    $order_info['detail_list'][0]['goods_id'] = 2131785;
    $mission_rst = POCO::singleton('pai_disposable_spread_class')->__laoxie_spread_mission_A($order_info);
    var_dump($mission_rst);
} elseif ($op=='8g1324hgqadr') {
    die();
    $c_back_list = array(
        array('user_id' => 100008, 'order_id' => 38939,  'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_8', ),
        array('user_id' => 198377, 'order_id' => 179570, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_28', ),
        array('user_id' => 106222, 'order_id' => 178859, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_38', ),
        array('user_id' => 102013, 'order_id' => 176297, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_38', ),
        array('user_id' => 212833, 'order_id' => 179607, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_38', ),
        array('user_id' => 106434, 'order_id' => 178896, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_38', ),
        array('user_id' => 165126, 'order_id' => 178686, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_38', ),
        array('user_id' => 109078, 'order_id' => 179102, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_38', ),
        array('user_id' => 114805, 'order_id' => 179070, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_48', ),
        array('user_id' => 106684, 'order_id' => 179118, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_48', ),
        array('user_id' => 354621, 'order_id' => 179525, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_68', ),
        array('user_id' => 208874, 'order_id' => 177774, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_68', ),
        array('user_id' => 212640, 'order_id' => 177829, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_68', ),
        array('user_id' => 356075, 'order_id' => 179507, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_68', ),
        array('user_id' => 355651, 'order_id' => 179589, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_68', ),
        array('user_id' => 208758, 'order_id' => 177777, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_68', ),
        array('user_id' => 356039, 'order_id' => 179542, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_68', ),
        array('user_id' => 104993, 'order_id' => 179548, 'give_code' => 'Y2015M11D09_CONSUMPTION_BACK_68', ),
    );
    $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
    foreach( $c_back_list as $c_back )
    {
        $give_code = $c_back['give_code'];
        $buyer_user_id = $c_back['user_id'];
        $order_id = $c_back['order_id'];
        $sub_rst = $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id);
        var_dump($sub_rst);
    }
} elseif ($op=='iahb4nfu1u3urhadf') {
    die();
    $mall_goods_obj = POCO::singleton('pai_mall_goods_class');
    $mall_type_attr_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
    $goods_info = $mall_goods_obj->get_goods_info(2129075);
    var_dump($goods_info);
    echo "<br />---------------------------------------------------------------------------------------------------------------<br />";
    foreach($goods_info['goods_data']['detail'] as $detail)
    {
        $rst = $mall_type_attr_obj->get_property_info($detail['type_id']);
        var_dump($rst);
}
} elseif ($op=='a903igjadskf23r') {
    die();
    $mall_seller_obj = POCO::singleton('pai_mall_seller_class');
    $rst = $mall_seller_obj->get_seller_info(132692, '');
    var_dump($rst);
} elseif ($op=='sdfasdf23e4asdf') {
    die();
    $pai_risk_obj = POCO::singleton('pai_risk_class');
    // $seller_list = $pai_risk_obj->get_scalping_white_list(false, '', '', '0,99999999');
    $seller_list = $pai_risk_obj->get_seller_list_for_check(false, '', 'type_id,id', '0,99999999');
    var_dump($seller_list);
} elseif ($op=='f3rasdf23rasdf') {
    die();
    $pai_risk_obj = POCO::singleton('pai_risk_class');
    $data = array(
        'remark' => 'cevi',
        'status' => 0,
        'status_time' => 0,
        'add_by' => 'system',
    );
    $rst = -1;
    // $seller_info = $pai_risk_obj->get_scalping_seller_info(132692, '');
    //
    // if (count($seller_info)>0 && is_array($seller_info)) {
    //     $data['status'] = 0;
    //     $data['status_time'] = 0;
    //     $rst = $pai_risk_obj->edit_scalping_seller(132692, $data);
    // }
    $rst = $pai_risk_obj->add_scalping_seller_auto(132692, $data);
    var_dump($rst);
} elseif ($op=='a123ew234f') {
    die();
    $pai_risk_obj = POCO::singleton('pai_risk_class');
    $mall_seller_obj = POCO::singleton('pai_mall_seller_class');
    $min_time = strtotime('yesterday');
    $max_time = strtotime('today')-1;

    $sync_rst = $pai_risk_obj->sync_order($min_time, $max_time);

    echo 'ͬ���������'. var_export($sync_rst, true) . ' ' . date("Y-m-d H:i:s")."\r\n\r\n";

    $where_str = "sign_time>={$min_time} AND sign_time<={$max_time}";
    $seller_user_list = $pai_risk_obj->get_seller_list_for_check(false, $where_str, 'type_id,id', '0,99999999');
    foreach($seller_user_list as $seller)
    {
        $seller_info =  $mall_seller_obj->get_seller_info($user_id,2);
        if( $seller_info['seller_data']['is_black']==1 ) continue;

        $check_rst = $pai_risk_obj->check_all_rule($seller['seller_user_id'], $min_time, $max_time, $seller['type_id']);
        $log_id = 0;
        if( $check_rst['is_scalping'] )//���ˢ������¼
        {
            $log_id = $pai_risk_obj->add_scalping_check_log($check_rst['seller_user_id'], $check_rst['rule_type_id'], $check_rst['rule_code_m'], $min_time, $max_time, $check_rst['type_id']);
            echo $log_id;
}

if($log_id>0)//���ˢ���̼Ҵ������¼
{
    $remark = date('Y-m-d H:i')." ϵͳ��飬Ʒ�ࣺ{$check_rst['rule_type_id']}��ʱ��".date('Y/m/d H:i:s', $min_time)."~".date('Y/m/d H:i:s', $max_time)."�����й���{$check_rst['rule_code_m']}������¼��{$log_id}��";
    // $pai_risk_obj->add_scalping_seller_auto($check_rst['seller_user_id'], array('remark' => $remark));
    echo $remark;
}
echo '�̼�ID'.$user_id.' �������'. var_export($check_rst, true) . ' ' . date("Y-m-d H:i:s")."\r\n";
}
} elseif ($op=='adasdfae234f') {
    die();
    $pai_risk_obj = POCO::singleton('pai_risk_class');
    $sync_rst = $pai_risk_obj->sync_order(1445270401, 1447343160);
    var_dump($sync_rst);
} elseif ($op=='95481jcdaf4') {
    die();
    //�����û�ע��ȯ
    $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
    $sql = "
        SELECT
        user_id
        FROM
        pai_db.`pai_user_tbl`
        WHERE add_time >= UNIX_TIMESTAMP('2015-11-11 00:00:00')
        AND add_time <= UNIX_TIMESTAMP('2015-11-11 11:00:00')
        ";
    $user_list = $coupon_give_obj->findBySql($sql);
    $user_list = array_map('array_pop', $user_list);
    $i = 0;
    foreach($user_list as $user_id)
    {
        $i++;
        // $rst = $coupon_give_obj->submit_queue('Y2015M11D09_USER_REG', '', $user_id, 0);
        var_dump($rst);
}
var_dump($i);
} elseif ($op=='test_tips') {
    die();
    $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
    $rst = $coupon_give_obj->show_tips_for_comment_interface('821716915');
    var_dump($rst);
    $rst = $coupon_give_obj->show_tips_for_comment_interface('961716874');
    var_dump($rst);
    $rst = $coupon_give_obj->show_tips_for_comment_interface('24254950');
    var_dump($rst);
} elseif ($op=='test_msg') {
    die();
    $message_info = array(
        'msg_type' => 2,
        'content' => '�װ����û����ã�ԼԼ�����������ζ������ѽ����������Ӧ���Ż�ȯ�������������ҵ� �� �Ż�ȯ���ڲ鿴����л����ԼԼ��֧�֡�',
        'to_url' => '',
        'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
        'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
        'link_url' => '/mall/user/topic/index.php?topic_id=801&online=1',
    );

    $content = trim($message_info['content']);
    $to_url = trim($message_info['to_url']);
    $msg_type = intval($message_info['msg_type']);
    $link_url = 'http://yp.yueus.com'.trim($message_info['link_url']);
    $wifi_link_url = 'http://yp-wifi.yueus.com'.trim($message_info['link_url']);
    $card_text1 = trim($message_info['card_text1']);
    $card_title = trim($message_info['card_title']);
    if( $msg_type==2 )
    {
        $send_data = array(
            'media_type' => 'card', // (text:����; notify:ϵͳ֪ͨ; card:��Ƭ)
            'card_style' => 2, // (1:�м��н���Ǹ�ģ��; 2:ֻ��һ�������Ǹ�ģ��)
            'card_text1' => $card_text1, // (����media_type=card�ģ����ϱ���)
            'card_title' => $card_title, // (����media_type=card�ģ��ײ�)
            'link_url' => 'yueyue://goto?type=inner_web&showtitle=2&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url),
        );
        $rst = POCO::singleton('pai_information_push')->message_sending_for_system(111917, $send_data, 10002, 'yuebuyer');
    }
    elseif( strlen($content)>0 )
    {
        $rst = send_message_for_10002(111917, $content, $to_url, 'yuebuyer');
    }
    var_dump($rst);
} elseif ($op=='test_add_to') {
    die();
    $pai_risk_obj = POCO::singleton('pai_risk_class');
    $sql = '
        SELECT
        *
        FROM
        ecpay_db.`pai_account_attentively_tbl`
        WHERE is_cancel = 0
        AND user_id NOT IN
        (SELECT
        r.user_id
        FROM
        pai_risk_db.`risk_scalping_seller_tbl` AS r
        WHERE r.STATUS <> 7)
        ';

    $accout_list = db_simple_getdata($sql, false, 101);
    $cur_time = time();
    $data = array(
        'add_by' => 'manual',
    );
    foreach ($accout_list as $accout) {
        $data['change_time'] = $accout['add_time'];
        $data['add_time'] = $cur_time;
        $user_id = $accout['user_id'];
        if ($accout['color']=='008000') {
            $data['status'] = 8;
            $data['status_time'] = $accout['add_time'];
            $data['remark'] = date('Y-m-d H:i:s', $accout['add_time'])." ����������ʷˢ���̼ң����ڲ���ϵͳ��־������ȷ��ˢ���̼ҡ�ϵͳ����";
        } elseif ($accout['color']=='FF0000') {
            $data['status'] = 0;
            $data['status_time'] = 1;
            $data['remark'] = date('Y-m-d H:i:s', $accout['add_time'])." ����������ʷˢ���̼ң����ڲ���ϵͳ��־������ȷ��ˢ���̼ҡ�ϵͳ����";
        } else {
            unset($data['status']);
            unset($data['status_time']);
            unset($data['remark']);
            echo 'error';
            continue;
        }

        $add_rst = $pai_risk_obj->add_scalping_seller($user_id, $data);
        var_dump($add_rst);
        if ($add_rst<=0) {
            $edit_rst = $pai_risk_obj->edit_scalping_seller($user_id, $data);
            var_dump($edit_rst);
        }
    }

    // $scalping_seller_list = $pai_risk_obj->get_scalping_seller_list(false, '', 'color,add_time', '0,99999999');
    // var_dump($scalping_seller_list);
} elseif ($op=='test_add_auto') {
    die();
    $pai_risk_obj = POCO::singleton('pai_risk_class');
    var_dump($pai_risk_obj->get_scalping_seller_list());

    var_dump($pai_risk_obj->get_scalping_seller_info(204666));
    var_dump($pai_risk_obj->get_scalping_seller_history(113054));
    die();
    $pai_risk_obj = POCO::singleton('pai_risk_class');
    $mall_seller_obj = POCO::singleton('pai_mall_seller_class');
    $min_time = strtotime('2015-10-20');
    $max_time = strtotime('2015-10-21')-1;

    $sync_rst = $pai_risk_obj->sync_order($min_time, $max_time);

    echo 'ͬ���������'. var_export($sync_rst, true) . ' ' . date("Y-m-d H:i:s")."\r\n";
    $where_str = "sign_time>={$min_time} AND sign_time<={$max_time}";
    $seller_user_id_arr = $pai_risk_obj->get_seller_list_for_check(false, $where_str, '', '0,99999999');

    foreach($seller_user_id_arr as $user_id)
    {
        $seller_info =  $mall_seller_obj->get_seller_info($user_id,2);
        if( $seller_info['seller_data']['is_black']==1 ) continue;
        $check_rst = $pai_risk_obj->check_all_rule($user_id, $min_time, $max_time);
        echo '�̼�ID'.$user_id.' �������'. var_export($check_rst, true) . ' ' . date("Y-m-d H:i:s")."\r\n";
    }
} elseif ($op=='risk_test') {
    die();
    $pai_risk_obj = POCO::singleton('pai_risk_class');
    $min_time = strtotime('yesterday');
    $max_time = strtotime('today')-1;
    // $sync_rst = $pai_risk_obj->sync_order($min_time, $max_time);
    // var_dump($sync_rst);
    $seller_list = array(
        134227,
        134222,
        133114,
        111445,
        133787,
        196425,
        103480,
        129963,
        194996,
        103586,
        175930,
        114694,
        104008,
        111264,
        119126,
        117620,
        116171,
        114242,
        114397,
        119116,
        122442,
        106457,
        119150,
        106553,
        114537,
        126168,
        121850,
        123520,
        132427,
        132404,
        106356,
        103515,
        121537,
        115547,
        114265,
        119146,
        106200,
        123026,
        119098,
        118041,
        106028,
        114112,
        132402,
        123023,
        118086,
        114246,
        117678,
        195202,
        124027,
        115195,
        117414,
        121887,
        116131,
        106331,
        117890,
        122196,
        117914,
        114653,
        122924,
        122510,
        123294,
        105983,
        115071,
        122940,
        128132,
        106587,
        132396,
        128101,
        106048,
        106424,
        126327,
        123040,
        122513,
        132921,
        195026,
        104374,
        100427,
        127978,
        172432,
        134763,
        131162,
        151132,
        100460,
        112883,
        107745,
        123930,
        122187,
        120632,
        120041,
        100420,
        117681,
        171292,
        100038,
        100223,
        132530,
        171311,
        119482,
        126150,
        161639,
        195397,
        112906,
        100208,
        116012,
        102938,
        152468,
        131172,
        178597,
        110047,
        160622,
        156684,
        154346,
        110205,
        186110,
        197647,
        122443,
        122422,
        121540,
        121500,
        198365,
        185932,
        194511,
        102651,
        154005,
        194260,
        198551,
        200292,
        100406,
        129666,
        123814,
        100008,
        110762,
        118519,
        173252,
        133523,
        110763,
        115612,
        134340,
        102100,
        130968,
        110986,
        175053,
        188720,
        101763,
        116072,
        103585,
        153906,
        134225,
        134718,
        104095,
        183392,
        131394,
        110208,
        181946,
        104369,
        195988,
        203910,
        198006,
        100216,
        124129,
        109897,
        204666,
        106347,
        113054);
    // foreach($seller_list as $seller)
    // {
    //     $check_rst = $pai_risk_obj->check_all_rule($seller, $min_time, $max_time);
    // }

    $check_log_list = $pai_risk_obj->get_check_log_list(0, false, "", 'seller_user_id ASC,check_time,log_id', '0,99999999');
    var_dump($check_log_list);
} elseif ($op=='c_coupon_multi') {
    die();
    $coupon_package_obj = POCO::singleton('pai_coupon_package_class');

    $package_sn_str = 'yueustz01,yueustz02,yueustz03,yueustz04,yueustz05,yueustz06,yueustz07,yueustz08,yueustz09,yueustz10,yueustz11,yueustz12,yueustz13,yueustz14,yueustz15,yueustz16,yueustz17,yueustz18,yueustz19,yueustz20,yueustz21,yueustz22,yueustz23,yueustz24,yueustz25,yueustz26,yueustz27,yueustz28,yueustz29,yueustz30,yueustz31,yueustz32,yueustz33,yueustz34,yueustz35,yueustz36,yueustz37,yueustz38,yueustz39,yueustz40,yueustz41,yueustz42,yueustz43,yueustz44,yueustz45,yueustz46,yueustz47,yueustz48,yueustz49,yueustz50';

    $package_sn_arr = explode(',', $package_sn_str);
    foreach ($package_sn_arr as $package_sn)
    {
        $cate_id = 252;
        $start_time = strtotime('2015-11-09 00:00:00');
        $end_time = strtotime('2015-11-30 23:59:59');
        $plan_number = 99999999;
        $batch_list = array(
            array('batch_id'=>609, 'quantity'=>1, 'coupon_days'=>0),
        );
        $more_info = array('scope_user_divide'=>'', 'package_title'=>'�һ���', 'package_remark'=>'2015��11�� ʮ��У԰���ƣ�11.09-11.30��');
        $ret = $coupon_package_obj->submit_package($package_sn, $cate_id, $start_time, $end_time, $plan_number, $batch_list, $more_info);
        var_dump($ret);
    }
} elseif ($op=='mall_order_test') {
    die();
    //$buyer_user_id = 116127;
    //$cur_time = time();

    // set_time_limit(600);
    // $op = trim($_INPUT['op']);

    //$order_obj = POCO::singleton('pai_mall_order_class');

    //$goods_id = intval($_GET['goods_id']);
    //$goods_obj = POCO::singleton('pai_mall_goods_class');
    //$goods_info = $goods_obj->get_goods_info($goods_id);

    //$goods_data = $goods_info['goods_data'];

    /**
     * ��������Ϣ
     */
    //$more_info = array(
    //  'referer' => '���԰���xiaowu',//������Դ
    //  'description' => '����xiaowu',
    //  );

    /**
     * ������ϸ��Ϣ
     */
    //$detail_list[] = array(
    //  'type_id'=> $goods_data['type_id'],//Ʒ��id
    //  'goods_id'=> $goods_id,//��Ʒid
    //  'goods_name'=> $goods_data['titles'],//��Ʒ����
    //  'prices_type_id'=> $goods_info['goods_prices_list'][0]['type_id'][0],//��Ʒ�۸��������id
    //  'prices_spec'=> '����',//��Ʒ���
    //  'goods_version'=> $goods_data['version'],//��Ʒ�汾
    //  'service_time'=> $cur_time,//����ʱ��
    //  'service_location_id'=> $goods_data['location_id'],
    //      'service_address' => '������Խ���������³�����һ��·xzwu',
    //      'service_people' => 3,
    //      //'prices' => 0,
    //      'quantity'=>1,
    //      );
    //
    //$order_sn = $order_obj->submit_order($buyer_user_id, $detail_list, $more_info);

    // var_export($order_sn);

    //$order_sn = '12237622';

    /**
     * �ύ֧��
     * @param string $order_sn
     * @param int $user_id ����û�ID
     * @param double $available_balance ҳ�浱ǰ���
     * @param int $is_available_balance �Ƿ�ʹ����0�� 1��
     * @param string $third_code ֧����ʽ alipay_purse tenpay_wxapp�����û�ʹ�����ȫ��֧��ʱ��Ϊ��
     * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
     * @param string $notify_url
     * @return array array('result'=>0, 'message'=>'', 'payment_no'=>'', 'request_data'=>'')
     * result 1��ȡ������֧����2���֧���ɹ�
     */
    //$res = $order_obj->submit_pay_order($order_sn, $buyer_user_id, 1, 0, 'alipay', '', 'http://ypays.yueus.com/__xiaowu_test.php', '');
    //var_dump($res);
} elseif ($op=='sync_subscribe_list') {
    die();
    $weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
    //ͬ����ע�б�
    //$update_rst = $weixin_helper_obj->sync_subscribe_list(2);
    //$update_rst = $weixin_helper_obj->sync_subscribe_list(1);
    var_dump($update_rst);
} elseif ($op=='sync_user_base_info') {
    die();
    $weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
    //ͬ���û���������, һ��ͬ��20000��OK
    //$update_rst = $weixin_helper_obj->sync_user_base_info(2,'' , '0,20000');
    //$update_rst = $weixin_helper_obj->sync_user_base_info(1, "language='zh_TW' or language='zh_HK'", '0,20000');
    //$update_rst = $weixin_helper_obj->sync_user_base_info(1, "country='China'", '0,20000');
    //$count = intval($_INPUT['asdffeq']);
    //$update_rst = $weixin_helper_obj->sync_user_base_info(2);
    var_dump($update_rst);
} elseif ($op=='get_material') {
    die();
    $weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
    //��ȡ�ز�
    //$rst = $weixin_helper_obj->sync_material(1, 'news');
    //var_dump($rst);
    //$rst = $weixin_helper_obj->sync_material(1, 'image');
    //var_dump($rst);
    //$rst = $weixin_helper_obj->sync_material(1, 'voice');
    //var_dump($rst);
    //$rst = $weixin_helper_obj->sync_material(1, 'video');
    //var_dump($rst);
    //$rst = $weixin_helper_obj->sync_material(2, 'news');
    //var_dump($rst);
    //$rst = $weixin_helper_obj->sync_material(2, 'image');
    //var_dump($rst);
    //$rst = $weixin_helper_obj->sync_material(2, 'voice');
    //var_dump($rst);
    //$rst = $weixin_helper_obj->sync_material(2, 'video');
    //$rst=$weixin_helper_obj->get_material_info(2, 'KKQ-qtCvHwytQvLdAaEYBx_jlfgbxwj8IVmPxpQzEkw');
    //$rst = $weixin_helper_obj->wx_get_reply_by_exec_image(2, '', array('exec_val'=>'KKQ-qtCvHwytQvLdAaEYBx_jlfgbxwj8IVmPxpQzEkw'));
    //$rst = $weixin_helper_obj->wx_get_reply_by_push(2, array('MsgType'=>'text', 'Content'=>'С�����', 'FromUserName'=>'asdf', 'ToUserName'=>'asdxdfaf'));
    //$rst = $weixin_helper_obj->reply_array_to_xml($rst);
    var_dump($rst);
} elseif ($op=='wx_test') {
    die();
    //Ԥ��Ⱥ����Ϣ
    $weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
    $weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
    $wx_user_base_info = $weixin_pub_obj->get_user_weixin_base_info_by_user_id($user_id);
    $is_subscribe = $wx_user_base_info['is_subscribe'];
    var_dump($rst);
    //$rst = $weixin_helper_obj->wx_mass_send(2, array('oMw0uuEncjlgK5DBlUj8M5Y0E8eU', 'oMw0uuDvMys1f7anv0ehWbNrrtM0'),'MpGI5MW4wJBHjclIs_vq67diSyk0rKy6QBLS3Z9q14M');
    //$rst = $weixin_helper_obj->wx_mass_send(1, array('oNHJqs5hIsluOn3MY3_acxE-ZYjg'),'ax4SYTXCloDuBgY3fR_PAGy7ilm8MK5UpEazmOJkKA0', true);
    //$rst = $weixin_helper_obj->mass_send_preview(2, 'oMw0uuEncjlgK5DBlUj8M5Y0E8eU', 'MpGI5MW4wJBHjclIs_vq67diSyk0rKy6QBLS3Z9q14M');
    //$rst = $weixin_helper_obj->mass_send(1, "language='zh_CN'", 'ax4SYTXCloDuBgY3fR_PAGy7ilm8MK5UpEazmOJkKA0', '����');
    //$rst = $weixin_helper_obj->mass_send(2, 70);
} else {
    die('op error!');
    die('op error!');
}
