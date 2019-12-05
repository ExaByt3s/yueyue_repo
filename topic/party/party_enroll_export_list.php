<?php
/**
 * 报名列表生成excel
 * 
 * 含有场次
 * 
 */
set_time_limit(0);
ini_set("memory_limit","256M");
ignore_user_abort(true);
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$details_obj = POCO::singleton('event_details_class');
$enroll_obj = POCO::singleton('event_enroll_class');
$cmd_obj = POCO::singleton('event_commend_act_class');
$system_obj  = POCO::singleton('event_system_class');
$event_table_obj    = POCO::singleton('event_table_class');
$relate_obj = POCO::singleton ('pai_relate_poco_class');

$event_id = $_GET["event_id"];
if(empty($event_id))
{
    header("location:http://event.poco.cn/event_list.php");
}
$event_info = $details_obj->get_event_by_event_id($event_id);
if(empty($event_info))
{
    header("location:http://event.poco.cn/event_list.php");
}

//查询对应的poco_id
if(!empty($yue_login_id))
{
    $poco_login_id = $relate_obj->get_relate_poco_id($yue_login_id);
}
else
{
    $poco_login_id = 0;
}


//登录用户是否这个活动的发起者
if($event_info['user_id']!=$poco_login_id)
{
    $is_event_admin = $system_obj->check_is_admin($poco_login_id);
    if(!$is_event_admin)
    {
        header("location:http://event.poco.cn/event_list.php");
    }
}



//报名网友类型
$status_type = trim($_GET["status_type"]);
if(!empty($status_type))
{
    if($status_type=='all')
        $status = '';
    elseif($status_type=='zhengshi')
        $status = '0';
    else 
        $status = '';
}else{
    $status = '';
}

Header("Content-type: text/html; charset=gb2312");
Header("Content-type: application/octet-stream");
Header("Accept-Ranges: bytes");
Header("Content-type:application/vnd.ms-excel");
if($status=='0')
{
    Header("Content-Disposition:attachment;filename= 活动报名列表_正选_".date("Ymd").".xls");
}else{
    Header("Content-Disposition:attachment;filename= 活动报名列表_全部_".date("Ymd").".xls");
}



$enroll_list = $enroll_obj->get_enroll_list_by_event_id($event_id,$status,false,'');
$count = count($enroll_list);
if (empty($count))
{
    $content  	       = "没有找到你符合你查找要求的记录\n";
}
else
{
    $content 		  = " 场次 \t 用户ID \t 用户名 \t 头衔 \t 频道积分 \t 空间地址 \t 联系电话 \t EMAIL \t 联系地址 \t 真实姓名 \t 身份证 \t 报名人数 \t 报名状态 ";

    $content 		 .= " \n";

    //报名状态名
    $enroll_status_name_arr=array(
    "0"=>"成功",
    "1"=>"候补",
    "3"=>"观望中"
    );
  
    //获取场次列表
    $event_table = $event_table_obj->get_event_table($event_id);
    $table_config_arr = array("零","一","二","三","四","五","六","七","八","九","十");
    
    
    
    foreach($event_table as $k=>$val)
    {
        $event_table[$k]['data_mark'] = ((int)$k)+1;
        $event_table[$k]['site_name'] = $table_config_arr[$event_table[$k]['data_mark']];//对应场次名
        $table_id = $val['id'];
        
        //正选已支付
        $first = $enroll_obj->get_enroll_list_and_event_info(array("status"=>0,"event_id"=>$event_id,"table_id"=>$table_id), $type_icon = $event_info['type_icon'], "0,2000","enroll_id asc");
        
        foreach($first as $key => $value)
        {
            $first[$key]['user_name'] = POCO::execute(array('member.get_user_nickname_by_user_id'), array($value['user_id']));
            //积分系统
            $first_retrun_arr = construct_part($value['user_id'],$event_info['type_icon'],$value['status'],$value['remark'],$enroll_status_name_arr);

            $content .= "第".$event_table[$k]['site_name']."场\t".$value['user_id']."\t".$first[$key]['user_name']." \t".$first_retrun_arr['level_title']." \t".$first_retrun_arr['credit_point']." \t".$first_retrun_arr['user_url']."\t'".$value['phone']."\t".$value['email']."\t".$first_retrun_arr['address']."\t".$first_retrun_arr['realname']."\t".$first_retrun_arr['idcard']."\t".$value['enroll_num']."\t".$first_retrun_arr['status_name'];


        
            $content .=" \n ";
            
        }
        

        //$event_table[$k]['first'] = $first;
        
        if($status_type=="all")
        {
            
        
            //候补已支付
            $backup = $enroll_obj->get_enroll_list_and_event_info(array("status"=>1,"event_id"=>$event_id,"table_id"=>$table_id), $type_icon = $event_info['type_icon'], "0,2000","enroll_id asc");

            
            foreach($backup as $key => $value)
            {
                $backup[$key]['user_name'] = POCO::execute(array('member.get_user_nickname_by_user_id'), array($value['user_id']));
                //积分系统
                $backup_retrun_arr =construct_part($value['user_id'],$event_info['type_icon'],$value['status'],$value['remark'],$enroll_status_name_arr);

                
                $content .= "第".$event_table[$k]['site_name']."场\t".$value['user_id']."\t".$backup[$key]['user_name']." \t".$backup_retrun_arr['level_title']." \t".$backup_retrun_arr['credit_point']." \t".$backup_retrun_arr['user_url']."\t'".$value['phone']."\t".$value['email']."\t".$backup_retrun_arr['address']."\t".$backup_retrun_arr['realname']."\t".$backup_retrun_arr['idcard']."\t".$value['enroll_num']."\t".$backup_retrun_arr['status_name'];


            
                $content .=" \n ";
                

            }
            

            //$event_table[$k]['backup'] = $backup;

            //报名未支付
            $onlooker = $enroll_obj->get_enroll_list_and_event_info(array("status"=>3,"event_id"=>$event_id,"table_id"=>$table_id), $type_icon = $event_info['type_icon'], "0,2000","enroll_id asc");
            
            foreach($onlooker as $key => $value)
            {
                $onlooker[$key]['user_name'] = POCO::execute(array('member.get_user_nickname_by_user_id'), array($value['user_id']));
                //积分系统
                $onlooker_retrun_arr =construct_part($value['user_id'],$event_info['type_icon'],$value['status'],$value['remark'],$enroll_status_name_arr);


                $content .= "第".$event_table[$k]['site_name']."场\t".$value['user_id']."\t".$onlooker[$key]['user_name']." \t".$onlooker_retrun_arr['level_title']." \t".$onlooker_retrun_arr['credit_point']." \t".$onlooker_retrun_arr['user_url']."\t'".$value['phone']."\t".$value['email']."\t".$onlooker_retrun_arr['address']."\t".$onlooker_retrun_arr['realname']."\t".$backup_retrun_arr['idcard']."\t".$value['enroll_num']."\t".$onlooker_retrun_arr['status_name'];

            
                $content .=" \n ";
            }
            
            //$event_table[$k]['onlooker'] = $onlooker;
        }
        
    }
    
  
    
}


function construct_part($user_id,$type_icon,$status,$remark,$enroll_status_name_arr)
{
    //积分系统
    $credit_info = POCO::execute('credit_system.credit_system_get_user_all_info', array($user_id));
    switch ($type_icon)
    {
        case "vision":
            $point_info = $credit_info["photo"];
            break;
        case "taste":
            $point_info = $credit_info["food"];
            break;
        case "trip":
            $point_info = $credit_info["travel"];
            break;
        case "box":
            $point_info = $credit_info["photo"];
            break;
        case "feeling":
            $point_info = $credit_info["photo"];
            break;
        case "aichong":
            $point_info = $credit_info["photo"];
            break;
        case "player":
            $point_info = $credit_info["photo"];
            break;
        case "photo":
            $point_info = $credit_info["photo"];
            break;
        case "food":
            $point_info = $credit_info["food"];
            break;
        case "travel":
            $point_info = $credit_info["travel"];
            break;		
        case "pet":
            $point_info = $credit_info["pet"];
            break;	
        case "music":
            $point_info = $credit_info["music"];
            break;	
        case "party":
            $point_info = $credit_info["photo"];
            break;				
        case "salon":
            $point_info = $credit_info["photo"];
            break;	
        case "movie":
            $point_info = $credit_info["photo"];
            break;	
        case "green":
            $point_info = $credit_info["photo"];
            break;																							
        case "cook":
            $point_info = $credit_info["food_diy"];
            break;		
        case "car":
            $point_info = $credit_info["photo"];
            break;					
            
    }
    
    
    if(!empty($point_info))
    {
        //频道积分
        $credit_point = (int)$point_info['credit_point'];
        //等级
        $level_title = $point_info['title'];
    }else{
        $credit_point = "";
        $level_title = "";
    }
    
    $user_url = "http://my.poco.cn/id-".$user_id.".shtml";
    $status_name = $enroll_status_name_arr[$status];
    
    //联系地址
    $remark = unserialize($remark);
    $address  = $remark['address'];
    //真实姓名
    $realname  = $remark['realname'];
    $idcard  = $remark['idcard'];
    
    $return_arr = array("credit_point"=>$credit_point,"level_title"=>$level_title,"user_url"=>$user_url,"status_name"=>$status_name,"address"=>$address,"realname"=>$realname,"idcard"=>$idcard);
    return $return_arr;
}

echo $content;
echo "\n";

?>