<?php
/**临时测试
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/9/1
 * Time: 15:48
 */
include_once('common.inc.php');

//啊裕
$pai_payment_obj = POCO::singleton('pai_payment_class');
//我们的订单类
$event_order_obj = POCO::singleton('pai_event_order_report_class');
$request_time = time();
//时间戳 例如传入今天的时间戳会查询昨天的订单 返回值为数组
$ret    = $pai_payment_obj->collect_daily_sell_stats_report($request_time);
//时间戳 例如传入今天的时间戳会查询昨天的订单 返回值为数组
$result = $event_order_obj->event_order_ret_by_date($request_time);
print_r($ret);
echo "<br/>";
print_r($result);
exit;
/*$pai_pa_dt_register_obj = POCO::singleton( 'pai_pa_dt_register_class' );

$list = $pai_pa_dt_register_obj->get_pa_dt_register_list($b_select_count = false,'dwaenqgxjt52',$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*');
print_r($list);
exit;*/
/*$sql_str ="CREATE TABLE IF NOT EXISTS `pai_admin_db`.`pai_pa_dt_url_qrcode_tbl` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `url` varchar(100) NOT NULL,
  `curl` text NOT NULL,
  `qrcode_img_url` varchar(100) NOT NULL,
  `add_time` int(10) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `apply_for_name` varchar(100) NOT NULL,
  `puid` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=gbk;";
db_simple_getdata($sql_str, TRUE, 101);
echo "成功";
exit;*/
// +---------------------------------------------------------------------------------------------------------------------
// |获取商家的等级
// |@param int $seller_user_id 商家ID
// |@param int $type_id 分类ID
// +---------------------------------------------------------------------------------------------------------------------
function get_rating_level_by_seller_user_id($seller_user_id,$type_id)
{
    $mall_obj = POCO::singleton('pai_mall_seller_class');
    $rating_result =pai_mall_load_config('seller_rating');//加载等级文件
    $seller_user_id = (int)$seller_user_id;
    $type_id = (int)$type_id;
    if($seller_user_id <1 || $type_id <1) return '--';
    $result = $mall_obj->get_seller_rating($seller_user_id);
    if(strlen($result) <1) return '--';
    $result = explode(",",$result);
    if(!is_array($result) || empty($result)) return '--';
    $rating_level = 0;
    foreach($result as $key=>$v)
    {
        list($type_val,$rating_level_val) = explode('-',$v);
        if($type_id == $type_val)
        {
            $rating_level = $rating_level_val;
            break;
        }
    }
    if($type_id <1 || $rating_level <0) return '--';
    return $rating_result[$type_id][$rating_level]['text'];
}

// +--------------------------------------------------------------------------------------------------------------------
// | 手机号码获取订单数据
// +--------------------------------------------------------------------------------------------------------------------
/*
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/yue_function.php");
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//导出类
$mall_order_obj = POCO::singleton( 'pai_mall_order_class' );//订单类
$type_obj = POCO::singleton('pai_mall_goods_type_class');//商品品类
$pai_organization_obj = POCO::singleton("pai_organization_class");//机构库
$mall_comment_obj = POCO::singleton( 'pai_mall_comment_class' );//商城评价
$mall_certificate_service_obj = POCO::singleton('pai_mall_certificate_service_class');//服务审核人类
$user_obj = POCO::singleton('pai_user_class');//用户表
$coupon_obj = POCO::singleton( 'pai_coupon_class' );//优惠券类
$sql_str = "SELECT user_id,nickname FROM pai_db.pai_user_tbl WHERE cellphone IN (15109249370,15308639049,13268409027,15999768915,15917258323,15139247852,13502867794,15664937730,15011737547,15919937435,13268457081,15820447065,15710786370,13008425782,13560705741,18344475611,13189825279,18818935254,13259745207,15915485161,15889414891,15812724778,15658834446,13071414365,13724584245,13063534137,13532163441,13419923004,13450842996,15306832475,15018422441,15521942408,15596422398,15889501534,18320851451,13724680936,13790230691,13419912742,13790265237,13123214764,15664846692,13590141925,13124682786,15216464017,13474478542,13431677409,15596422510,15118097255,13697784384,13267877861,15625712709)";
$list = db_simple_getdata($sql_str,FALSE,101);
if(!is_array($list)) $list = array();

$sql_tmp_str = '';
foreach($list as $key=>$val)
{
    if(strlen($sql_tmp_str)>0) $sql_tmp_str .= ',';
    $sql_tmp_str .= $val['user_id'];
}
echo $sql_tmp_str;
exit;
$where_str = '';
if(strlen($sql_tmp_str)>0)
{
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "buyer_user_id IN ({$sql_tmp_str})";
    $list = $mall_order_obj->get_order_full_list($type_id,-1,false, $where_str,'sign_time DESC,close_time DESC,order_id DESC',"0,99999999", $fields='*');
    if(!is_array($list)) $list = array();
    $data = array();
    foreach($list as $key=>$val)
    {
        $ref_order_price= '0.00';
        $data[$key]['order_id']    = $val['order_id'];
        $data[$key]['order_sn']    = $val['order_sn'];
        $data[$key]['seller_name'] = $val['seller_name'];
        $data[$key]['seller_user_id'] = $val['seller_user_id'];
        $data[$key]['seller_phone'] = $user_obj->get_phone_by_user_id($val['seller_user_id']);
        $data[$key]['buyer_name']  = $val['buyer_name'];
        $data[$key]['buyer_user_id']  = $val['buyer_user_id'];
        $data[$key]['buyer_phone']  = $user_obj->get_phone_by_user_id($val['buyer_user_id']);
        $data[$key]['order_num'] = $mall_order_obj->get_order_list(0, 8, TRUE,"buyer_user_id={$val['buyer_user_id']}");//交易次数
        $data[$key]['type_name']   = $val['type_name'];
        $data[$key]['cat_type_name'] = get_little_cate_by_goods_id($val['detail_list'][0]['goods_id']);
        $data[$key]['goods_id']  = $val['detail_list'][0]['goods_id'];
        $data[$key]['goods_name']  = $val['detail_list'][0]['goods_name'];
        $data[$key]['total_amount']= $val['total_amount'];
        $data[$key]['use_coupon_str'] = $val['is_use_coupon'] == 1 ? '是':'否';
        $data[$key]['discount_amount']= $val['discount_amount'];
        if($val['status'] == 8)
        {
            $ref_order_price = $coupon_obj->sum_ref_order_cash_amount_by_oid('mall_order', $val['order_id']);//完成单不同价格
        }
        $data[$key]['ref_order_price'] = $ref_order_price;
        $data[$key]['pay_time_str'] = $val['pay_time_str'];
        $data[$key]['sign_time'] = $val['sign_time'] != 0 ? date('Y-m-d H:i:s',$val['sign_time']) :'--';
        $service_time = date('Y-m-d H:i:s',$val['detail_list'][0]['service_time']);
        $service_address = $val['detail_list'][0]['service_address'];
        $data[$key]['service_time_str'] = $service_time;
        $data[$key]['service_address'] = $service_address;
        $data[$key]['status_str2'] = $val['status_str2'];
        $data[$key]['org_user_id'] = $val['org_user_id'] == 0 ? '无' : $pai_organization_obj->get_org_name_by_user_id($val['org_user_id']);
        $audit_name = $mall_certificate_service_obj->get_user_option_name($val['type_id'],$val['seller_user_id']);
        $data[$key]['audit_name'] = strlen($audit_name) >0 ? $audit_name : '无';
        $data[$key]['referer'] = $val['referer'];
        $seller_comment_ret = $mall_comment_obj->get_buyer_comment_info($val['order_id'],$val['detail_list'][0]['goods_id']);
        $buyer_comment_ret  = $mall_comment_obj->get_seller_comment_info($val['order_id'],$val['detail_list'][0]['goods_id']);
        $data[$key]['buyer_comment']  = trim($buyer_comment_ret['comment']) == '' ? '暂无':$buyer_comment_ret['comment'];
        $data[$key]['seller_comment']  = trim($seller_comment_ret['comment']) == '' ? '暂无':$seller_comment_ret['comment'];


    }
    unset($list);
    $fileName = '订单详情';
    //$title    = '订单详情列表';
    $headArr  = array("订单ID","订单编号","商家昵称","商家ID","商家手机号","买家昵称","买家ID","买家手机号","买家交易次数","商品品类","商品小分类","商品ID","商品名","交易额","是否使用优惠券","优惠券金额","已补贴金额","付款时间","签到时间","服务时间","服务地点","订单状态","商家机构归属","商家审核人员","来源","买家对商家评价","商家对买家评价");
    Excel_v2::start($headArr,$data,$fileName);
    exit;
}

function get_little_cate_by_goods_id($goods_id)
{
    $mall_goods_obj = POCO::singleton('pai_mall_goods_class');//商品表
    $goods_id = intval($goods_id);
    if($goods_id <1) return '--';
    $goods_info = $mall_goods_obj->get_goods_info($goods_id);
    if(is_array($goods_info) && !empty($goods_info))
    {
        $system_data = $goods_info['system_data'];
        if(!is_array($system_data)) $system_data = array();
        foreach($system_data as $system_next)
        {
            foreach($system_next['child_data'] as $cate_type)
            {
                if($cate_type['key'] == $system_next['value']) return $cate_type['name'];

            }
        }
    }
    return '--';
}
//获取订单来源
function referer($referer='')
{
    $referer = trim($referer);
    $sql_str ="SELECT referer FROM mall_db.mall_order_tbl WHERE referer !='' GROUP BY referer";
    $ret = db_simple_getdata($sql_str,false,101);
    if(!is_array($ret)) $ret = array();
    if(strlen($referer) >0)
    {
        foreach($ret as &$v)
        {
            $v['selected'] = $referer==$v['referer'] ? true : false;
        }
    }
    return $ret;
}
exit;
*/



// +--------------------------------------------------------------------------------------------------------------------
// | 发送活动信息测试
// +--------------------------------------------------------------------------------------------------------------------
exit;
//include_once('pai_event_mass_message_class.inc.php');
$event_mass_message_obj = POCO::singleton('pai_event_mass_message_class');//商品表
//$event_mass_message_obj = POCO::singleton('pai_event_mass_messsage_class');
/*$send_user_id = 100293;
$send_role = 'yuebuyer';
$user_arr = array(10293,10580);
$link_url = 'yueseller://goto?type=inner_web&url=http%3a%2f%2fyp.yueus.com%2fmall%2fuser%2ftopic%2findex.php%3ftopic_id%3d761%26online%3d1&wifi_url=http%3a%2f%2fyp-wifi.yueus.com%2fmall%2fuser%2ftopic%2findex.php%3ftopic_id%3d761%26online%3d1';
$wifi_url = 'yueseller://goto?type=inner_web&url=http%3a%2f%2fyp.yueus.com%2fmall%2fuser%2ftopic%2findex.php%3ftopic_id%3d761%26online%3d1&wifi_url=http%3a%2f%2fyp-wifi.yueus.com%2fmall%2fuser%2ftopic%2findex.php%3ftopic_id%3d761%26online%3d1';
$card_style = 1;
$card_title = '标题';
$card_text1 = '大标题XX';
$card_text2 = '副标题yy';
$ret =$event_mass_message_obj->start_mass_message_v2($send_user_id,$send_role,$user_arr,$content,'card',$link_url,$wifi_url,$card_style,$card_title,$card_text1,$card_text2);
print_r($ret);
exit;*/
$send_user_id = 100293;
$send_role = 'yueseller';
$user_arr = array(
    0=>100293,
    1=>100580
);
$content = '内容开始群发测试';
$ret = $event_mass_message_obj->start_mass_message($send_user_id,$send_role,$user_arr,$content) ;
var_dump($ret);
//测试发送角色
exit;


// +--------------------------------------------------------------------------------------------------------------------
// | 以商家去获取goods_id
// +--------------------------------------------------------------------------------------------------------------------


$sql_str = "select user_id,goods_id,status,is_black,is_show from mall_db.mall_goods_tbl WHERE type_id=31
AND user_id IN (125962,119860,119749,115612,114198,110047,108135,108032,106095,105491,104374,104369,103586,103585,103515,103514,102651,101555,100537,100427,100420,100406,100229,100223,100207,100168,100116);";
$result = db_simple_getdata($sql_str,false,101);
if(!is_array($result)) $result = array();
echo "<table border='1'>";
echo "<tr>";
echo "<th>商家ID</th>";
echo "<th>商品ID</th>";
echo "<th>商品状态</th>";
echo "<th>上下架</th>";
echo "<th>屏蔽显示</th>";
echo "</tr>";
foreach($result as $val)
{
    if($val['status'] == 0) $status_str = '未审核';
    if($val['status'] == 1) $status_str = '已通过';
    if($val['status'] == 2) $status_str = '未通过';
    if($val['status'] == 3) $status_str = '已删除';
    $is_show_str = $val['is_show'] == 1 ? '上架': '下架';
    $is_black_str = $val['is_black'] == 0 ? '未屏蔽': '屏蔽';
    echo "<tr>";
    echo "<th>{$val['user_id']}</th>";
    echo "<th>{$val['goods_id']}</th>";
    echo "<th>{$status_str}</th>";
    echo "<th>{$is_show_str}</th>";
    echo "<th>{$is_black_str}</th>";
    echo "</tr>";
}
echo "</table>";
exit;
$user_id = 100580;
$send_msg = '尊敬的约约用户，由于您的帐号涉嫌发送敏感信息被用户举报，系统已屏蔽您所发送的任何信息，如需申诉，请拨打 400-082-9003 联系客服处理！';
send_message_for_10002($user_id, $send_msg, '', 'all', 'sys_msg');
echo "{$user_id}发出";

exit;
/*$pai_rank_event_v3_class = POCO::singleton( 'pai_rank_event_v3_class' );

$list = $pai_rank_event_v3_class->get_cms_rank_info_list(862);
print_r($list);
exit;*/


/*$sql_str ="DELETE FROM pai_log_db.pai_mall_all_message_log WHERE add_id=100293";
$ret = db_simple_getdata($sql_str,true,101);
print_r($ret);*/
//$sql_str = "DELETE FROM pai_log_db.text_examine_log WHERE DATE_FORMAT(add_time,'%Y-%m-%d')<'2015-09-28'";
exit;


//include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


/*include_once('rank_common.inc.php');
$cms_rank_obj = new pai_cms_rank_class();//榜单类
$where_str ="location_id=101001001 AND type_id=99";
$list = $cms_rank_obj->get_main_rank_list(false,'',0,0,'',0,$where_str,'switch ASC,`order` DESC,`id` DESC,`add_time` DESC', "0,99999999");
if(!is_array($list)) $list = array();

foreach($list as $v)
{
    $location_id = 101024001;//新疆
    $ret = $cms_rank_obj->create_main_rank($location_id,$v['page_type'], $v['module_type'],$v['type_id'],$v['versions_id'],$v['title'],$v['order'],$v['link'], $v['img_url'],$v['remark'],$v['switch']);
    $main_id = intval($ret['code']);
    //$main_id = $v['id'];//上级ID
    $rank_info_list = $cms_rank_obj->get_rank_info_list(false,$v['id'],'','',0,'','switch ASC,`order` DESC,`id` DESC,`add_time` DESC','0,99999999');
    if(!is_array($rank_info_list)) $rank_info_list = array();
    foreach($rank_info_list as $val)
    {
        echo $main_id;
        $ret = $cms_rank_obj->create_info_rank($main_id, $val['type'],$val['rank_type'],$val['rank_id'],$val['cms_type'],$val['pid'], $val['title'], $val['content'], $val['img_url'],$val['link_url'] , $val['remark'], $val['order'], $val['switch']);
        var_dump($ret);
    }
}

exit;*/

//$user_arr = array(124176,124183,124185,124229,124230,126115,132421,132425,132427);
//$org_id = 111715;


/*$user_arr = array(126133,126152,126166,126168,126310,126314,126327,126342,132396,132398,132400,132402,132403,132404,132428);
$org_id = 119879;*/

//
/*$user_arr = array(128094,128101,128106,128125,128129,128132);
$org_id = 124214;
foreach($user_arr as $user_id)
{
    $user_id = intval($user_id);
    $sql_str ="INSERT INTO pai_user_library_db.model_relation_org_tbl (user_id,org_id,priority) VALUE ({$user_id},{$org_id},1)";
    echo $sql_str.'<br/>';

}*/
/*$rank_event_v3_obj = POCO::singleton( 'pai_rank_event_v3_class' );
$list = $rank_event_v3_obj->get_cms_rank_by_location_id('category_index');
echo "<pre>";
print_r($list);
exit;

include_once ('rank_common.inc.php');

$obj = new pai_cms_rank_class();

$select_data['page_type'] = 'index';
$result = $obj->get_main_rank($select_data);
print_r($result);


$obj->create_info_rank(1, 'general','测试标题', '测试内容');*/
//$obj->create_main_rank('101029001', 'index', 'type_1', '测试标题', 99, 'http://www.poco.cn', 'http://image17-c.poco.cn/best_pocoers/20150901/26622015090112121624287253_478.jpg', '备注');