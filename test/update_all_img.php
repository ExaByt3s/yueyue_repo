<?php
set_time_limit(0);
ignore_user_abort(true);
ini_set('memory_limit', '1500M');

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$fun = $_INPUT['fun'];

$mall_db = "mall_db";

function mall_certificate_basic_tbl()
{
    global $mall_db;
    $sql = "select * from {$mall_db}.mall_certificate_basic_tbl";
    $arr = db_simple_getdata($sql, false);

    foreach ($arr as $val) {

        $data['company_license_img_url'] = replace_domain($val['company_license_img_url']);
        $data['brand_img_url'] = replace_domain($val['brand_img_url']);
        $data['heads_img_url'] = replace_domain($val['heads_img_url']);
        $data['tails_img_url'] = replace_domain($val['tails_img_url']);

        $update_str = db_arr_to_update_str($data);

        $sql = "update {$mall_db}.mall_certificate_basic_tbl set {$update_str} where basic_id=" . $val['basic_id'];
        db_simple_getdata($sql, false);
    }
}
//mall_certificate_basic_tbl();

function mall_certificate_service_img_tbl()
{
    global $mall_db;
    $sql = "select * from {$mall_db}.mall_certificate_service_img_tbl";
    $arr = db_simple_getdata($sql, false);

    foreach ($arr as $val) {

        $data['img_url'] = replace_domain($val['img_url']);

        $update_str = db_arr_to_update_str($data);

        $sql = "update {$mall_db}.mall_certificate_service_img_tbl set {$update_str} where img_id=" . $val['img_id'];
        db_simple_getdata($sql, false);
    }

}
//mall_certificate_service_img_tbl();


function mall_goods_img_tbl()
{
    global $mall_db;
    $sql = "select * from {$mall_db}.mall_goods_img_tbl";
    $arr = db_simple_getdata($sql, false);

    foreach ($arr as $val) {

        $data['img_url'] = replace_domain($val['img_url']);

        $update_str = db_arr_to_update_str($data);

        $sql = "update {$mall_db}.mall_goods_img_tbl set {$update_str} where goods_img_id=" . $val['goods_img_id'];
        db_simple_getdata($sql, false);
    }
}
//mall_goods_img_tbl();


function mall_goods_tbl()
{
    global $mall_db;
    $sql = "select * from {$mall_db}.mall_goods_tbl";
    $arr = db_simple_getdata($sql, false);

    foreach ($arr as $val) {

        $data['images'] = replace_domain($val['images']);

        $update_str = db_arr_to_update_str($data);

        $sql = "update {$mall_db}.mall_goods_tbl set {$update_str} where goods_id=" . $val['goods_id'];
        db_simple_getdata($sql, false);
    }
}
//mall_goods_tbl();


function mall_order_detail_tbl()
{
    global $mall_db;
    $sql = "select * from {$mall_db}.mall_order_detail_tbl";
    $arr = db_simple_getdata($sql, false);

    foreach ($arr as $val) {

        $data['goods_images'] = replace_domain($val['goods_images']);

        $update_str = db_arr_to_update_str($data);

        $sql = "update {$mall_db}.mall_order_detail_tbl set {$update_str} where order_detail_id=" . $val['order_detail_id'];
        db_simple_getdata($sql, false);
    }
}
//mall_order_detail_tbl();


function mall_seller_profile_img_tbl()
{
    global $mall_db;
    $sql = "select * from {$mall_db}.mall_seller_profile_img_tbl";
    $arr = db_simple_getdata($sql, false);

    foreach ($arr as $val) {

        $data['img_url'] = replace_domain($val['img_url']);

        $update_str = db_arr_to_update_str($data);

        $sql = "update {$mall_db}.mall_seller_profile_img_tbl set {$update_str} where profile_img_id=" . $val['profile_img_id'];
        db_simple_getdata($sql, false);
    }
}
//mall_seller_profile_img_tbl();


function mall_seller_profile_tbl()
{
    global $mall_db;
    $sql = "select * from {$mall_db}.mall_seller_profile_tbl";
    $arr = db_simple_getdata($sql, false);

    foreach ($arr as $val) {

        $data['cover'] = replace_domain($val['cover']);
        $data['avatar'] = replace_domain($val['avatar']);
        $data['introduce'] = replace_domain($val['introduce']);

        $update_str = db_arr_to_update_str($data);

        $sql = "update {$mall_db}.mall_seller_profile_tbl set {$update_str} where seller_profile_id=" . $val['seller_profile_id'];
        db_simple_getdata($sql, false);
    }
}
//mall_seller_profile_tbl();


function pai_pic_del_tbl()
{
    $sql = "select * from pai_db.pai_pic_del_tbl";
    $arr = db_simple_getdata($sql, false);

    foreach ($arr as $val) {

        $data['img'] = replace_domain($val['img']);

        $update_str = db_arr_to_update_str($data);

        $sql = "update pai_db.pai_pic_del_tbl set {$update_str} where id=" . $val['id'];
        db_simple_getdata($sql, false);
    }
}
//pai_pic_del_tbl();


function pai_pic_tbl()
{
    $sql = "select * from pai_db.pai_pic_tbl";
    $arr = db_simple_getdata($sql, false);

    foreach ($arr as $val) {

        $data['img'] = replace_domain($val['img']);

        $update_str = db_arr_to_update_str($data);

        $sql = "update pai_db.pai_pic_tbl set {$update_str} where id=" . $val['id'];
        db_simple_getdata($sql, false);
    }
}
//pai_pic_tbl();


function cms_record_tbl()
{
    $sql = "select log_id,img_url from pai_cms_db.cms_record_tbl WHERE img_url LIKE '%poco.cn%'";
    $arr = db_simple_getdata($sql, false);

    foreach ($arr as $val) {

        $data['img_url'] = replace_domain($val['img_url']);

        $update_str = db_arr_to_update_str($data);

        $sql = "update pai_cms_db.cms_record_tbl set {$update_str} where log_id=" . $val['log_id'];
        //echo $sql."<br />";
        db_simple_getdata($sql, false);
    }
}

function pai_rank_info_tbl()
{
    $sql = "select * from pai_cms_db.pai_rank_info_tbl";
    $arr = db_simple_getdata($sql, false);

    foreach ($arr as $val) {

        $data['img_url'] = replace_domain($val['img_url']);

        $update_str = db_arr_to_update_str($data);

        $sql = "update pai_cms_db.pai_rank_info_tbl set {$update_str} where id=" . $val['id'];
        //echo $sql."<br />";
        db_simple_getdata($sql, false);
    }
}





if(!$fun)
{
    die('ÇëÊäÈëfunction');
}

call_user_func($fun);

echo $fun." Íê³É";
//mall_certificate_basic_tbl();
//mall_certificate_service_img_tbl();
//mall_goods_img_tbl();
//mall_goods_tbl();
//mall_order_detail_tbl();
//mall_seller_profile_img_tbl();
//mall_seller_profile_tbl();
//pai_pic_del_tbl();
//pai_pic_tbl();
//cms_record_tbl();







function replace_domain($pic_url)
{
    $search_arr[0]="image16-c.poco.cn";
    $replace_arr[0]="image19-d.yueus.com";

    $search_arr[1]="image16-d.poco.cn";
    $replace_arr[1]="image19-d.yueus.com";

    $search_arr[2]="image17-c.poco.cn";
    $replace_arr[2]="image19-d.yueus.com";

    $search_arr[3]="img16.poco.cn";
    $replace_arr[3]="image19-d.yueus.com";

    $search_arr[4]="yue-icon.yueus.com";
    $replace_arr[4]="yue-icon-d.yueus.com";

    $search_arr[5]="seller-icon.yueus.com";
    $replace_arr[5]="seller-icon-d.yueus.com";

    return str_replace($search_arr,$replace_arr,$pic_url);
}
?>