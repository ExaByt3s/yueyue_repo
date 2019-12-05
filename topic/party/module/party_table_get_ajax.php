<?php
/** 
 * 
 * 异步获取场次
 * 
 * author 星星
 * 
 * 2014-8-14
 * 
 */
include_once("../party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
$event_id = (int)$_INPUT['event_id'];
$event_table_obj = POCO::singleton('event_table_class');
$enroll_obj     = POCO::singleton('event_enroll_class');
$relate_obj = POCO::singleton ('pai_relate_poco_class');
$ajax_status = 1;
if(empty($event_id))
{
    $ajax_status = 0;
    
}

//查询对应的poco_id
if(!empty($yue_login_id))
{
    $poco_login_id = $relate_obj->get_relate_poco_id($yue_login_id);
}
else
{
    $ajax_status = 0;
    $poco_login_id = 0;
}



if(!empty($ajax_status))
{
    //获取场次
    $table_list = $event_table_obj->get_event_table($event_id);
    
    $table_config_arr = array("零","一","二","三","四","五","六","七","八","九","十");
    //查询支付情况
    $user_enroll_list = $enroll_obj->get_event_enroll_list($poco_login_id,$event_id,false,"");
    //处理报名数组信息
    foreach($user_enroll_list as $key => $value)
    {
        $tmp_enroll_list[$value['table_id']] = array($value['pay_status'],$value['enroll_id'],$value['enroll_num']);
    }
    
    /* if($yue_login_id==101428)
    {
        
        var_dump($user_enroll_list);
        
    } */
    
    foreach($table_list as $key => $value)
    {
        
        //判断当前这场是否已经开始，不允许点
        if($value['begin_time']<time())
        {
            $table_list[$key]['had_began'] = 1;//开始了
        }
        else
        {
            $table_list[$key]['had_began'] = 0;//开始了
        }
        
        //检查当前人的报名情况
        $tmp_duplicate = $enroll_obj->check_duplicate($poco_login_id,$event_id,$status="all", $value['id']);//记录用户该场的报名信息
        if($tmp_duplicate)
        {
            //判断是否已经支付
            if($tmp_enroll_list[$value['id']][0]>0)
            {
                $table_list[$key]['had_enroll'] = 2;//报名并且支付了
            }
            else
            {
                $table_list[$key]['had_enroll'] = 1;//仅报名未支付
            }
            //标记住报名ID
            $table_list[$key]['data_enroll'] = $tmp_enroll_list[$value['id']][1];
            $table_list[$key]['data_num'] = $tmp_enroll_list[$value['id']][2];
            
            
        }
        else
        {
            //设置报名ID为零
            $table_list[$key]['data_enroll'] = 0;
            $table_list[$key]['data_num'] = 0;
        }
        $table_list[$key]['data_mark'] = ((int)$key)+1;
        $table_list[$key]['site_name'] = $table_config_arr[$table_list[$key]['data_mark']];//对应场次名
        $table_list[$key]['site_name'] = iconv("GBK","UTF-8",$table_list[$key]['site_name']);
        
        /********10月13号修改时间*****************/
        $table_list[$key]['begin_time'] = iconv("GBK","UTF-8",date("m月d号 H:i",$table_list[$key]['begin_time']));
        //判断月跟日是否同一天
        $tmp_begin_day = date("md",$value['begin_time']);
        $tmp_end_day = date("md",$value['end_time']);
        if($tmp_begin_day==$tmp_end_day)
        {
            $table_list[$key]['end_time'] = iconv("GBK","UTF-8",date("H:i",$table_list[$key]['end_time'])); 
        }
        else
        {
           $table_list[$key]['end_time'] = iconv("GBK","UTF-8",date("m月d号 H:i",$table_list[$key]['end_time'])); 
        }
        unset($tmp_begin_day);
        unset($tmp_end_day);
        /********10月13号修改时间*****************/
        
        
    }
    
    //检查权限
    $join_mode_auth = $enroll_obj->check_join_mode_auth( $event_id,$poco_login_id );
    if($join_mode_auth)
    {
        $res = "Y";
    }
    else
    {
        $res = "N";
    }
}

$res_arr = array(
"ajax_status"=>$ajax_status,
"table_list"=>$table_list,
"res"=>$res

);

echo json_encode($res_arr);



?>