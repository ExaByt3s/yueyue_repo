<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/event_act.php');

$cms_obj = new cms_system_class();
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$mall_seller_obj = POCO::singleton('pai_mall_seller_class');


$cms_id = 1049;
$ico_result = $cms_obj->get_last_issue_record_list(false, '0,100', 'place_number DESC', $cms_id);

var_dump($ico_result);
foreach($ico_result AS $key=>$val)
{
    $r_data = array(
        'keywords' => $val['user_id'],
    );
    $tmp_result = $task_goods_obj->user_search_goods_list($r_data, '0,1');
    //$tmp_result = $mall_seller_obj->user_search_seller_list($r_data, '0,1');
    //var_dump($tmp_result);
    if(!$tmp_result[total] || $tmp_result['data'][0]['is_show'] != 1)
    {
        echo $key;
        var_dump($val);
        echo $val['user_id'];
        echo "<BR>-----------------------------------<BR>";
        var_dump($tmp_result);
        echo "<BR>-----------------------------------<BR>";
        echo "<BR><BR><BR>";
    }

}

exit();
$list_code = array("904000","907935","918677","925486","931515","932366","945404","951819","952680","954953","964061","998291","fnyueyue","shuoshuo11","YLYUEYUE","yueusbq","yueusgp01","yueusgp02","yueusgp03","yueusgp04","yueusgp05","yueusgp06","yueusgp07","yueusgp11","yueusgp12","YUEUSGP14","yueusgp20","yueusl01","yueusl05","YUEUSL40","yueusm01","yueusm02","yueusm03","Yueusm04","yueussq01","yueussq03","Yueussq05","yueussq06","yueussq07","yueussq08","yueussq09","yueussq10","yueust01","yueust02","yueust03","yueust04","yueust05","yueust06","Yueust07","yueust08","yueust09","yueust10","yueust11","yueust12","yueust13","yueust14","yueust15","Yueust16","yueust17","yueust18","yueust19","yueust20","yueusxy01","yueusxy02","yueusxy03","yueusxy04","Yueusxy05","yueusxy06","yueusxy07","yueusxy08","yueusxy09","yueusxy10","yueusxy102","yueusxy11","yueusxy110","yueusxy111","yueusxy12","yueusxy123","yueusxy126","yueusxy13","yueusxy131","yueusxy132","yueusxy133","yueusxy134","yueusxy136","yueusxy137","yueusxy14","yueusxy15","yueusxy16","yueusxy163","yueusxy17","yueusxy18","yueusxy19","YueusXY20","yueusxy200","yueusxy21","yueusxy22","yueusxy23","yueusxy24","yueusxy25","yueusxy26","yueusxy27","yueusxy28","yueusxy29","yueusxy30","yueusxy32","yueusxy33","yueusxy34","yueusxy35","yueusxy36","yueusxy37","yueusxy39","yueusxy40","yueusxy42","yueusxy44","yueusxy47","yueusxy48","yueusxy49","yueusxy50","yueusxy51","yueusxy52","yueusxy53","yueusxy54","yueusxy55","yueusxy66","yueusxy80","yueusxy85","YUEUSZTZ01","YUEUSZTZ02","YUEUSZTZ03","YUEUSZTZ04","YUEUSZTZ05","YUEUSZTZ06","yueusztz07","YUEUSZTZ08","YUEUSZTZ09","YUEUSZTZ10","yueusztz100","yueusztz101","YUEUSZTZ102","YUEUSZTZ103","yueusztz104","Yueusztz105","YUEUSZTZ106","yueusztz107","YUEUSZTZ108","YUEUSZTZ11","YUEUSZTZ110","YUeusztz111","YUEUSZTZ116","YUEUSZTZ12","YUEUSZTZ123","YUEUSZTZ126","YUEUSZTZ128","YUEUSZTZ13","YUEUSZTZ130","YUEUSZTZ131","YUEUSZTZ132","YUEUSZTZ133","YUEUSZTZ134","YUEUSZTZ135","YUEUSZTZ139","YUEUSZTZ140","YUEUSZTZ141","YUEUSZTZ142","YUEUSZTZ143","YUEUSZTZ144","YUEUSZTZ145","YUEUSZTZ147","YUEUSZTZ148","YUEUSZTZ149","YUEUSZTZ15","YUEUSZTZ150","YUEUSZTZ151","YUEUSZTZ152","YUEUSZTZ153","YUEUSZTZ154","YUEUSZTZ155","YUEUSZTZ156","YUEUSZTZ16","YUEUSZTZ160","YUEUSZTZ166","YUEUSZTZ17","YUEUSZTZ19","YUEUSZTZ20","YUEUSZTZ21","YUEUSZTZ22","YUEUSZTZ23","yueusztz233","YUEUSZTZ239","YUEUSZTZ24","yueusztz25","yueusztz251","YUEUSZTZ26","YUEUSZTZ27","YUEUSZTZ271","YUEUSZTZ28","yueusztz29","YUEUSZTZ30","yueusztz32","YUEUSZTZ33","YUEUSZTZ34","YUEUSZTZ35","YUEUSZTZ36","YUEUSZTZ37","YUEUSZTZ38","YUEUSZTZ39","YUEUSZTZ42","YUEUSZTZ43","YUEUSZTZ45","YUEUSZTZ46","YUEUSZTZ47","Yueusztz48","YUEUSZTZ49","YUEUSZTZ50","YUEUSZTZ51","YUEUSZTZ52","YUEUSZTZ54","YUEUSZTZ55","YUEUSZTZ56","YUEUSZTZ57","YUEUSZTZ58","YUEUSZTZ59","YUEUSZTZ60","YUEUSZTZ61","YUEUSZTZ62","YUEUSZTZ63","YUEUSZTZ64","YUEUSZTZ65","yueusztz66","yueusztz67","YUEUSZTZ68","YUEUSZTZ69","YUEUSZTZ70","Yueusztz71","YUEUSZTZ72","YUEUSZTZ73","YUEUSZTZ74","YUEUSZTZ76","YUEUSZTZ77","YUEUSZTZ78","YUEUSZTZ79","yueusztz80","YUEUSZTZ81","YUEUSZTZ82","yueusztz83","YUEUSZTZ84","Yueusztz85","YUEUSZTZ86","yueusztz87","YUEUSZTZ88","YUEUSZTZ89","YUEUSZTZ90","YUEUSZTZ91","YUEUSZTZ92","YUEUSZTZ93","Yueusztz94","YUEUSZTZ95","YUEUSZTZ96","YUEUSZTZ97","yueusztz98","YUEYUE01","yueyue01at","yueyue02","yueyue02pj","YUEYUE04","YUEYUE05","yueyue05qy","yueyue06","yueyue07","YUEYUE09","Yueyue10","yueyue12","YUEYUE13","yueyue14","Yueyue15","yueyue16","yueyue17","yueyue18","yueyue19","YUEYUE1BAO","yueyue23","YUEYUE25","YUEYUE26","YUEYUE27","yueyue27dw","YUEYUE3BAO","yueyue5bao","YUEYUE6BAO","yueyue7bao","yueyue7baosj","yueyue8bao","YUEYUE8BAOkv","yueyuea01","yueyuea02","yueyuea03","yueyuea04","YUEYUEA06","yueyuea08","yueyuea09","yueyueb01","YUEYUEB02","Yueyueb03","yueyueb04","YUEYUEB06","yueyueb07","yueyueb08","yueyueb09","YUEYUEB10","yueyueb11","YUEYUEB12","yueyueb13","yueyueb14","yueyueb15","yueyueb17","YUEYUEB18","yueyueb19","yueyueb20","yueyueb22","YUEYUEB23","YUEYUEB24","YUEYUEBJ","yueyuec01","Yueyuec06","yueyuec07","yueyuec11","yueyuec15","yueyuec16","yueyuec19","Yueyuec20","yueyuec22","yueyuec27","yueyuec33","yueyuec34","yueyuec37","yueyuec39","yueyuec42","yueyuecq","YUEYUEJGA11","yueyuejgb22","YUEYUEJGC33","yueyuely5","yueyuex1","yueyuex11","yueyuex12","yueyuex13","yueyuex15","yueyuex16","yueyuex17","yueyuex3","yueyuexa");
foreach($list_code AS $key=>$val)
{
    $sql_str = "SELECT channel_module, channel_oid
                FROM pai_coupon_db.coupon_ref_order_tbl
                WHERE coupon_sn IN
                (
                SELECT coupon_sn FROM pai_coupon_db.coupon_exchange_ref_coupon_tbl WHERE package_sn = '{$val}' AND is_settle=1
                ) ";
    $result = db_simple_getdata($sql_str, FALSE, 101);
    echo "<table>";
    if($result)
    {
        echo "<tr><td colspan=9>$val</td></tr>";
        echo "<tr><td>订单ID</td><td>订单号</td><td>购买人ID</td><td>售出人ID</td><td>商品类型</td><td>总价</td><td>折扣</td><td>来源</td><td>下单时间</td></tr>";
        foreach($result AS $k=>$v) {
            //var_dump($v);
            $order_info = get_order_info($v['channel_oid']);
            //var_dump($order_info);
            if ($order_info) {
                echo "<tr>";
                echo "<td>" . $order_info['order_id'] . "</td>";
                echo "<td>" . $order_info['order_sn'] . "</td>";
                echo "<td>" . $order_info['buyer_user_id'] . "</td>";
                echo "<td>" . $order_info['seller_user_id'] . "</td>";
                echo "<td>" . $order_info['type_name'] . "</td>";
                echo "<td>" . $order_info['total_amount'] . "</td>";
                echo "<td>" . $order_info['discount_amount'] . "</td>";
                echo "<td>" . $order_info['referer'] . "</td>";
                echo "<td>" . date('Y-m-d H:i:s', $order_info['add_time']) . "</td>";
                echo "</tr>";
            }
        }
    }
    echo "</table>";
    //exit();
}


function get_order_info($order_id)
{
        $sql_str = "SELECT * FROM
                    `mall_db`.`mall_order_tbl` WHERE `status` = 8 AND order_id=$order_id";
    return db_simple_getdata($sql_str, TRUE, 101);
}
exit();
$cms_id = 641;
$cms_obj = new cms_system_class();
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$ico_result = $cms_obj->get_last_issue_record_list(false, '0,10', 'place_number DESC', $cms_id);


foreach($ico_result AS $key=>$val)
{
    $r_data = array(
        'keywords' => $val['user_id'],
    );
    $tmp_result = $task_goods_obj->user_search_goods_list($r_data, '0,1');
    echo $val['user_id'];
    echo "<BR>-----------------------------------<BR>";
    var_dump($tmp_result);
    echo "<BR>-----------------------------------<BR>";
}

exit();
$list_coupons = get_coupons_num();
echo "<table>";
foreach($list_coupons AS $key=>$val)
{
    echo "<tr>";
    echo "<td>" . $val['package_sn'] . "</td>";
    echo "<td>" . $val['C'] . "</td>";
    echo "<td>" . get_use_coupons_num($val['package_sn']) . "</td>";
    echo "</tr>";
}
echo "</table>";





function get_coupons_num()
{
    $sql_str = "SELECT package_sn, COUNT(*)  AS C
                FROM `pai_coupon_db`.`coupon_exchange_tbl`
                WHERE cate_id = 5 GROUP BY package_sn ORDER BY C DESC";
    $result = db_simple_getdata($sql_str, FALSE, 101);
    return $result;
}

function get_use_coupons_num($package_sn)
{
    $sql_str = "SELECT COUNT(is_used) AS C  FROM pai_coupon_db.coupon_ref_user_tbl
                WHERE coupon_sn IN (
                SELECT coupon_sn FROM pai_coupon_db.coupon_exchange_ref_coupon_tbl WHERE package_sn = '{$package_sn}'
                ) AND is_used = 1";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    return $result['C']?$result['C']:0;
}




exit();
$code_list = array(
    'guangdonggongmaozhiyejishuxueyuanbaiyun'=>'广东工贸职业技术学院白云校区',
    'guangdongjiaotongzhiyejishuxueyuan'=>'广东交通职业技术学院',
    'zhongkainongyegongchengxueyuan'=>'仲恺农业工程学院',
    'jinandaxue'=>'暨南大学',
    'guangdongwaiyuwaimaodaxuezhongnan'=>'广东外语外贸大学',
    'guangdonggongyedaxuelongdong'=>'广东工业大学龙洞校区',
    'guangdonggongyedaxuehuali'=>'广东工业大学华立学院',
    'guangdongwaiyuwaimaodaxuelizhoutian'=>'广东外语外贸大学',
    'zhongshandaxue'=>'中山大学',
    'guangzhoudaxue'=>'广州大学',
    'huanannongyedaxue'=>'华南农业大学',
    'huananshifandaxue'=>'华南师范大学',
    'guangdongyaoxueyuan'=>'广东药学院',
    'gong_ying_shang_dian_mian'=>'gong_ying_shang_dian_mian',
    'xian_xia_shang_hu_qu_dao'=>'xian_xia_shang_hu_qu_dao',
    'sheng_huo_sheng_qu'=>'sheng_huo_sheng_qu',
    'cbd'=>'cbd',
    'shang_chang_huo_dong'=>'shang_chang_huo_dong',
    'he_zuo_fang_huo_dong'=>'he_zuo_fang_huo_dong',
    'yue_yue_pin_pai_huo_dong'=>'yue_yue_pin_pai_huo_dong',
    'tj_yueyue_1'=>'tj_yueyue_1',
);

echo "<table>";
foreach($code_list AS $key=>$val)
{
    echo "<tr>";
    $weixin_info = extension_get_weixin_info($key);
    echo "<td>" . $val . "</td>";
    echo "<td>" . $weixin_info['subscribe'] . "</td>";
    echo "<td>" . $weixin_info['unsubscribe'] . "</td>";
    echo "<td>" . get_weixin_reg_num($key) . "</td>";
    echo "</tr>";
}
echo "</table>";


function extension_get_weixin_info($key)
{
    $search_key = 'qrscene_' . $key;
    $sql_str = "SELECT `Event` AS OP, COUNT(*) AS C FROM `pai_weixin_db`.`weixin_receive_tbl`
                WHERE MsgType = 'event' AND EventKey = '{$search_key}' GROUP BY `Event`";
    $result = db_simple_getdata($sql_str, FALSE, 101);
    foreach($result AS $key=>$val)
    {
        $return_info[$val['OP']] = $val['C'];
    }
    return $return_info;
}

function get_weixin_reg_num($key)
{
    $search_key = 'qrscene_' . $key;
    $sql_str = "SELECT COUNT(*) AS C  FROM pai_db.pai_bind_weixin_tbl
                WHERE open_id IN (
                SELECT FromUserName FROM pai_weixin_db.weixin_receive_tbl WHERE MsgType = 'event' AND EventKey = '{$search_key}'
                )";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    return $result['C']?$result['C']:0;
}
