<?php
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');



/* $user_arr = array("556691571","65804368");
$user_id_str = implode(",",$user_arr);

$sql = "select event_id from event_db.event_details_tbl where user_id in ({$user_id_str}) and add_time>1429245229 and new_version=2";
$event_list = db_simple_getdata($sql); */



    include_once ('/disk/data/htdocs232/poco/pai/yue_admin/cms/include/cms_db_class.inc.php');
    include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/include/cms_system_class.inc.php');
    
    $cms_obj = new cms_system_class();
    
    //$waipai1 = $cms_obj->get_record_list_by_issue_id(false, 279, "0,50","place_number ASC", null, "");
    //$waipai2 = $cms_obj->get_record_list_by_issue_id(false, 280, "0,50","place_number ASC", null, "");
    //$waipai3 = $cms_obj->get_record_list_by_issue_id(false, 281, "0,50","place_number ASC", null, "");
    
    
    $combine_arr=$cms_obj->get_record_list_by_rank_id($b_select_conut=false, 75, $limit="0,10000", $order_by="place_number ASC", $b_get_freeze=false, $where_str="");

    
    foreach($combine_arr as $k=>$val)
    {
        $content_arr = explode("|",$val['content']);
        
        $event_id = $content_arr[0];
        
        $event_arr[] = $event_id;
    }
    
/*      $event_arr[] = 47076;
     $event_arr[] = 47088;
     $event_arr[] = 47091; */

    $event_id_str = implode(",",$event_arr); 



    if(!$event_id_str)
    {
        $event_id_str = 0;
    }




    //支付成功返回文案
    return array(
        "big_waipai"=>"48410,48459,49168,50417,52477,54559,55339,56877,57350,58913,58945,59110,59028,59888,59994,60028,60409,60559",
        "one_waipai"=>$event_id_str,
    );


?>