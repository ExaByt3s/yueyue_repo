<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('pai_topic_common.inc.php');

if($_GET['yue_user_id'])
{
    $user_id = $_GET['yue_user_id'];
}else{
    $user_id = $yue_login_id;
}

if(!$user_id) header("Location:login.php");

$sql_str = "SELECT * FROM pai_topic_db.pai_topic_user_info_tbl WHERE yue_user_id=$user_id";
$result = db_simple_getdata($sql_str, TRUE, 101);

$tpl = new SmartTemplate("preview.tpl.html");

if($result['img'])
{
    $tmp_img_list = unserialize($result['img']);
    $img_list = '';
    foreach($tmp_img_list AS $key=>$val)
    {
//        if($key == 0)
//        {
//            $result['icon'] = $val;
//        }else{
//            $num = $key-1;
//            $img_list[$num]['img_src'] = $val;
//        }
        $img_list[$key]['img_src'] = $val;
        
    }
}
;
$tpl->assign('img_list', $img_list);
$tpl->assign($result);
$tpl->output();
?>