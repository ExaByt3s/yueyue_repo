<?php
set_time_limit(0);

exit;
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$sql = "select * from pai_db.pai_cameraman_comment_log_tbl";
$comment_arr = db_simple_getdata($sql,false,101);

foreach($comment_arr as $val)
{
    $date_info = get_date_info($val['date_id']);

    if($date_info ['date_style']=='ŷ��')
    {
        $style_hash_arr = "67c6a1e7ce56d3d6fa748ab6d9af3fd7";
        $style_hash_name = array("ŷ��");
    }
    elseif($date_info ['date_style']=='����')
    {
        $style_hash_arr = "642e92efb79421734881b53e1e1b18b6";
        $style_hash_name = array("����");
    }
    elseif ($date_info ['date_style']=='����')
    {
        $style_hash_arr = "f457c545a9ded88f18ecee47145a72c0";
        $style_hash_name = array("��ˮ");
    }
    elseif ($date_info ['date_style']=='����')
    {
        $style_hash_arr = "b53b3a3d6ab90ce0268229151c9bde11";
        $style_hash_name = array("����");
    }
    elseif ($date_info ['date_style']=='����')
    {
        $style_hash_arr = "c0c7c76d30bd3dcaefc96f40275bdc0a";
        $style_hash_name = array("��װ","���ո���");
    }
    elseif ($date_info ['date_style']=='��ϵ')
    {
        $style_hash_arr = "2838023a778dfaecdc212708f721b788";
        $style_hash_name = array("��ϵ");
    }
    elseif ($date_info ['date_style']=='��ϵ')
    {
        $style_hash_arr = "2838023a778dfaecdc212708f721b788";
        $style_hash_name = array("��ϵ");
    }
    elseif ($date_info ['date_style']=='�Ը�')
    {
        $style_hash_arr = "a684eceee76fc522773286a895bc8436";
        $style_hash_name = array("����/�Ȼ���");
    }
    elseif ($date_info ['date_style']=='��Ƭ')
    {
        $style_hash_arr = "f457c545a9ded88f18ecee47145a72c0";
    }
    elseif ($date_info ['date_style']=='��ҵ')
    {
        $style_hash_arr = "72b32a1f754ba1c09b3695e0cb6cde7f";
        $style_hash_name = array("�Ա�","����","��չ","����");
    }
    else
    {
        $style_hash_arr = "b53b3a3d6ab90ce0268229151c9bde11";
    }

    $sql = "SELECT g.goods_id,g.user_id,p.name,p.data,p.goods_type_id FROM mall_db.mall_goods_detail_tbl AS p,mall_db.mall_goods_tbl AS g WHERE p.goods_id=g.goods_id AND p.goods_type_id=46 AND data='{$style_hash_arr}' AND g.user_id=".$val['model_user_id']." ORDER BY g.goods_id DESC LIMIT 1";
    $get_type = db_simple_getdata($sql,true,101);
    $goods_id = $get_type['goods_id'];
    if(!$goods_id)
    {
        $sql = "SELECT g.goods_id,g.user_id,p.name,p.data,p.goods_type_id FROM mall_db.mall_goods_detail_tbl AS p,mall_db.mall_goods_tbl AS g WHERE p.goods_id=g.goods_id AND p.goods_type_id=46 AND g.user_id=".$val['model_user_id']." ORDER BY g.goods_id DESC LIMIT 1";
        $get_type = db_simple_getdata($sql,true,101);
        $goods_id = $get_type['goods_id'];
    }

    $insert_arr['from_user_id'] = $val['model_user_id'];
    $insert_arr['to_user_id'] = $val['cameraman_user_id'];
    $insert_arr['order_id'] = 0;
    $insert_arr['goods_id'] = $goods_id;
    $insert_arr['overall_score'] = $val['overall_score'];
    $insert_arr['comment'] = $val['comment'];
    $insert_arr['is_anonymous'] = $val['is_anonymous'];
    $insert_arr['add_time'] = $val['add_time'];

    $insert_str=db_arr_to_update_str($insert_arr);

    $sql = "insert into mall_db.mall_comment_buyer_tbl set ".$insert_str;
    db_simple_getdata($sql,false,101);
    unset($insert_arr);
}

?>