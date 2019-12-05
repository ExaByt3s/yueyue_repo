<?php
include_once("../../../manson_common.inc.php");

$page = $_REQUEST['page'];

$page_count = 10;

if(empty($page)) $page = 1;

$limit_start = ($page - 1)*$page_count;

$photowall_obj = new photowall_class();
$load_photo = $photowall_obj->get_photo_list(false,null,"{$limit_start},{$page_count}","order_pos DESC","big_img_url,small_img_url,des,add_time");


foreach ($load_photo as $key => $value)
{
	//$load_photo[$key]['id'] = $key;
	
	$load_photo[$key]['des'] = mb_convert_encoding($value['des'], "utf-8" , "GBK");
}


echo header("Access-Control-Allow-Origin:*");
echo json_encode($load_photo);
?>
