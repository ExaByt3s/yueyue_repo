<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id        = $client_data['data']['param']['location_id'];
$limit              = $client_data['data']['param']['limit'];
$query              = $client_data['data']['param']['query'];

$pic_obj        = POCO::singleton ('pai_pic_class');

$sql_str_count = "SELECT COUNT(DISTINCTROW(L.user_id)) AS C
            FROM pai_db.pai_model_style_v2_tbl AS L, pai_db.pai_fulltext_tbl AS R 
            WHERE R.role='model' AND L.user_id=R.user_id";

$sql_str = "SELECT L.user_id, L.price, L.hour, R.nickname, R.date_num, R.score_num 
            FROM pai_db.pai_model_style_v2_tbl AS L, pai_db.pai_fulltext_tbl AS R 
            WHERE R.role='model' AND L.user_id=R.user_id";



if($query['tag'])
{
    $tag = $query['tag'];
    $sql_str .= " AND L.style LIKE '%{$tag}%' ";
    $sql_str_count .= " AND L.style LIKE '%{$tag}%' ";
}
if($query['price'])
{
    $price_array = explode("-", $query['price']);
    $sql_str .= " AND L.price >= $price_array[0] AND L.price <= $price_array[1] ";
    $sql_str_count .= " AND L.price >= $price_array[0] AND L.price <= $price_array[1] ";
}
if($query['hour'])
{
    $hour = $query['hour'];
    $sql_str .= " AND L.hour = '{$hour}' ";
    $sql_str_count .= " AND L.hour = '{$hour}' ";
}
if($query['key'])
{
    $key = ($query['key']);
    $key = urldecode($query['key']);
    $key = iconv('utf-8', 'gbk', $key);

    if($key == '内衣/比坚尼')
    {
        $sql_str .= " AND (R.`fulltext` LIKE '%内衣%' OR R.`fulltext` LIKE '%比坚尼%') ";
        $sql_str_count .= " AND (R.`fulltext` LIKE '%内衣%' OR R.`fulltext` LIKE '%比坚尼%') ";
    }elseif($key == '礼仪/车展'){
        $sql_str .= " AND (R.`fulltext` LIKE '%礼仪%' OR R.`fulltext` LIKE '%车展%') ";
        $sql_str_count .= " AND (R.`fulltext` LIKE '%礼仪%' OR R.`fulltext` LIKE '%车展%') ";
    }else{
        $sql_str .= " AND R.`fulltext` LIKE '%{$key}%' ";
        $sql_str_count .= " AND R.`fulltext` LIKE '%{$key}%' ";
    }

/*        $sql_str .= " AND R.`fulltext` LIKE '%{$key}%' ";
        $sql_str_count .= " AND R.`fulltext` LIKE '%{$key}%' ";*/


}

if(!is_numeric($query['key']))
{
    if($location_id)
    {
        $sql_str .= " AND R.location_id=$location_id ";
        $sql_str_count .= " AND R.location_id=$location_id ";
    }

    //只显示审核通过的用户
    $sql_str .= " AND R.is_show=1";
    $sql_str_count .= " AND R.is_show=1";
}else{
    //只显示审核通过的用户
    $sql_str .= " AND (R.is_show=1 OR R.is_show=3) ";
    $sql_str_count .= " AND (R.is_show=1 OR R.is_show=3) ";
}

//苹果店版本 开始
/**
if(version_compare($client_data['data']['version'], '2.1.10', '='))
{
$sql_str_count = "SELECT COUNT(DISTINCTROW(L.user_id)) AS C
            FROM pai_db.pai_model_style_v2_tbl AS L, pai_db.pai_fulltext_tbl AS R 
            WHERE R.role='model' AND L.user_id=R.user_id AND L.user_id IN (100006, 101870, 104941,100044) ";

$sql_str = "SELECT L.user_id, L.price, L.hour, R.nickname, R.date_num, R.score_num 
            FROM pai_db.pai_model_style_v2_tbl AS L, pai_db.pai_fulltext_tbl AS R 
            WHERE R.role='model' AND L.user_id=R.user_id AND L.user_id IN (100006, 101870, 104941,100044) ";
}
 * */
//苹果店版本 结束

$sql_str .= " GROUP BY L.user_id ";

if($query['order'] == 'number')
{
    $sql_str .= 'ORDER BY R.date_num DESC';
}elseif($query['order'] == 'comment'){
    $sql_str .= 'ORDER BY R.score_num DESC';
}else{
    $sql_str .= 'ORDER BY L.group_id ASC '; 
}
if($limit)
{
    $sql_str .= " LIMIT {$limit}";
}


$result = db_simple_getdata($sql_str_count, TRUE, 101);
$data['count'] = $result['C'];

//搜索模板
$data['mid']  = '122LT01002';
$data['dmid'] = 's001';


$result = db_simple_getdata($sql_str, FALSE, 101);
//$data['count'] = count($result);
foreach($result AS $key=>$val)
{

    $data_val['user_id']    = $val['user_id'];
    $data_val['price']      = '￥' . $val['price'] . '.00';
    $data_val['hour']       = '(' . $val['hour'] . '小时)';
    $data_val['nickname']   = $val['nickname'];
    $data_val['yue_num']    = $val['date_num'] . '次';
    $data_val['credit']     = $val['score_num'];
    //$data_val['icon']       = get_user_icon($val['user_id'], $size = 468);
    //头像修改
    $data_val['icon'] = '';
    $pic_array = $pic_obj->get_user_pic($val['user_id'], $limit = '0,5');
    foreach($pic_array AS $k=>$v)
    {
        $num = explode('?', $v['img']);
        $num = explode('x', $num[1]);
        $num_v2 = explode('_', $num[1]);
        
        $width = $num[0];
        $height = $num_v2[0];
        
        if($width<$height)
        {
            $data_val['icon'] = str_replace("_260.", "_440.", $v['img']);
            break;
        }
        $data_val['icon'] = str_replace("_260.", "_440.", $v['img']);
    }

    $model_card_obj = POCO::singleton ( 'pai_model_card_class' );
    $model_card_info = $model_card_obj->get_model_card_info( $data_val['user_id']);
    if($model_card_info['cover_img']) $data_val['icon'] = $model_card_info['cover_img'];

    $data_val['url']            = 'http://yp.yueus.com/mobile/app?from_app=1#model_card/' . $val['user_id'];
    $data_val['url_wifi']       = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#model_card/' . $val['user_id'];
    $data_val['vid']            = $data_val['user_id'];
    $data_val['jid']            = "001";

    $data_val['url']  = "yueyue://goto?type=inner_app&pid=1220025&mid=122RO01001&user_id=" . $val['user_id'];
    unset($data_val['url_wifi']);

    //打折布点
    $obj = POCO::singleton ( 'pai_alter_price_class',array(true) );
    $tag = $obj->get_topic_user_tag($v['user_id']);
    if($tag)
    {
        $data_val['tips'] = $tag;
    }

    
    $data['list'][]         = $data_val;
    unset($data_val);
}
$pic_array = '';


$add_time = date('Y-m-d H:i:s');
$sql_str = serialize($sql_str);
$sql_str_1 = "INSERT INTO pai_log_db.pai_search_log_tbl(sql_str, add_time) 
            VALUES (:x_sql_str, '{$add_time}')";
sqlSetParam($sql_str_1, 'x_sql_str', $sql_str);
db_simple_getdata($sql_str_1, TRUE, 101);


/**
$topic_obj = POCO::singleton ('pai_topic_class');

$where = ' is_effect = 1 ';
$count = $topic_obj->get_topic_list(true, $where);

$data['count'] = $count;

$user_list['id']       = '100008';
$user_list['nickname'] = '董小姐';
$user_list['icon']     = 'http://yue-icon.yueus.com/10/100174_165.jpg?20141230';
$user_list['Price']    = '500(2小时)';
$user_list['yue_num']  = '已经拍摄100次';
$user_list['credit']   = '5';
$user_list['url']      = 'http://yp.yueus.com/mobile/app?from_app=1#model_card/' . $user_list['id'];
$user_list['url_wifi'] = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#model_card/' . $user_list['id'];

$data['list'][] = $user_list;
$data['list'][] = $user_list;
$data['list'][] = $user_list;
$data['list'][] = $user_list;
**/


if($data['count'] < 2)
{

    $data['mid'] = '122SE02001';
    $data['dmid'] = 's002';

    $cms_obj        = new cms_system_class();
    $pic_obj        = POCO::singleton ('pai_pic_class');
    switch($location_id)
    {
        case 101019001:
            $key =24;
            break;

        case 101001001:
            $key =25;
            break;

        case 101003001:
            $key = 26;
            break;

        case 101004001:
            $key =44;
            break;

        case 101022001:
            $key =49;
            break;

        default:
            $key =12;
            break;
    }
    $info = $cms_obj->get_last_issue_record_list(false, '0,18', 'place_number DESC', $key);
    $data['name']  = '约约热模';
    $data['query'] = '';

    foreach($info AS $k=>$v)
    {
        $record['user_id']      = $v['user_id'];
        $record['user_icon']    = $v['img_url'];
        $record['vid']    = $record['user_id'];
        $record['jid']    = "001";


        //头像修改
        $pic_array = $pic_obj->get_user_pic($v['user_id'], $limit = '0,5');
        foreach($pic_array AS $a=>$b)
        {

            $num = explode('?', $b['img']);
            $num = explode('x', $num[1]);
            $num_v2 = explode('_', $num[1]);

            $width = $num[0];
            $height = $num_v2[0];

            if($width<$height)
            {
                $record['user_icon'] = str_replace("_260.", "_440.", $b['img']);
                break;
            }
            $record['user_icon'] = str_replace("_260.", "_440.", $b['img']);
        }

        $record['nickname']     = $v['title'];
        $record['num']          = $v['remark'];
        $record['style']        = $v['remark'];

        $pic_score_obj          = POCO::singleton ('pai_score_rank_class');
        $result                 = $pic_score_obj->get_score_rank($v['user_id']);
        $record['num']          = $result['score'];
        $record['unit']         = '魅力';
        $record['style']        = '清新';


        $record['url']          = 'http://yp.yueus.com/mobile/app?from_app=1#model_card/' . $v['user_id'];
        $record['url_wifi']     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#model_card/' . $v['user_id'];
        $record['url']          = "yueyue://goto?type=inner_app&pid=1220025&mid=122RO01001&user_id=" . $v['user_id'];
        unset($record['url_wifi']);

        //打折布点
        $obj = POCO::singleton ( 'pai_alter_price_class',array(true) );
        $tag = $obj->get_topic_user_tag($v['user_id']);
        if($tag)
        {
            $data_val['tips'] = $tag;
            $data_val['tips_remark'] = $tag;
        }


        $data['user_list'][]    = $record;
        unset($record);
    }


} 
$options['data'] = $data;
$cp->output($options);
?>