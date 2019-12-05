<?php
/** 
 * 
 * 获取签到列表异步页
 * 
 * authro 星星
 * 
 * 2015-3-3
 */

include_once("../party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
define("G_DB_GET_REALTIME_DATA",1);
$enroll_obj     = POCO::singleton('event_enroll_class');
$details_obj = POCO::singleton('event_details_class');
$system_obj = POCO::singleton('event_system_class');
$activity_code_obj = POCO::singleton('pai_activity_code_class');
$relate_obj = POCO::singleton ('pai_relate_poco_class');
$pai_user_obj = POCO::singleton('pai_user_class');
$pai_user_icon_obj = POCO::singleton('pai_user_icon_class');

//$__mp_manage_branch = G_POCO_EVENT_PAY;//开发版为支付版，非开发版为非支付
$__mp_manage_branch = 1;
$event_id = (int)$_INPUT['event_id'];//活动ID
$table_id = (int)$_INPUT['table_id'];//场次ID
$type_icon = trim($_INPUT['type_icon']);//类型
$list_type = trim($_INPUT['list_type']);//数据的类型


$ajax_status = 1;

if(empty($event_id) || empty($table_id))
{
    $ajax_status = 0;
}

$event_info=$details_obj->get_event_by_event_id($event_id);

//查询对应的poco_id
if(!empty($yue_login_id))
{
    $poco_login_id = $relate_obj->get_relate_poco_id($yue_login_id);
}
else
{
    $poco_login_id = 0;
}




//判断登录人的角色
if(!empty($poco_login_id))
{
    define("G_DB_GET_REALTIME_DATA", 1 );
    if($event_info['user_id']==$poco_login_id)
    {
        $event_user_role = "author";
        //判断是否管理人员
        $is_event_admin = $system_obj->check_is_admin($poco_login_id);
        if($is_event_admin)
        {
            $event_user_role = "admin";
        }
    }
    else 
    {
        //判断是否管理人员
        $is_event_admin = $system_obj->check_is_admin($poco_login_id);
        if($is_event_admin)
        {
            $event_user_role = "admin";
        }
    }
}


/* if($yue_login_id==100021)
{
    echo $event_user_role;
} */


if($ajax_status==1)
{
    //初始化分页
    $page_obj =POCO::singleton('show_page'); 
    $show_count = 20;//每页显示数
    
    
    if($list_type=="first")
    {
        $status = 0;
    }
    else if($list_type=="backup")
    {
        
        $status = 1;
        
    }
    else if($list_type=="onlooker")
    {
        $status = 3;
    }
    

    if($list_type=="checked")
    {
        $where_str = "event_id = {$event_id} AND table_id = {$table_id}";
    }
    else
    {
        
        $where_str = "status = {$status} AND event_id = {$event_id} AND table_id = {$table_id}";

    }
    
    //检查该列人数
    
    if($list_type=="checked")
    {
        //签到列表特殊处理
        $checked_tmp_list = $enroll_obj->get_enroll_list($where_str,false,"");
        foreach($checked_tmp_list as $key => $value)
        {
            $implode_arr[] = $value['enroll_id'];
        }
        $implode_str = implode(",",$implode_arr);
        if(!empty($implode_str))
        {
            $check_count_where = "enroll_id IN ({$implode_str})";
            $list_count = $activity_code_obj->get_code_list(true,$check_count_where);
        }
        else
        {
            $list_count = 0;
        }
        
        
        
        $total_count = $activity_code_obj->count_code_is_checked(true,$implode_str,$limit='0,10',false);
        
        
        
    }
    else
    {
        $list_count = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array($status),$table_id);
        $total_count = $enroll_obj->get_enroll_list($where_str,true);
        
    }
    
    

    
    $page_obj->file = '';
    $page_obj->set($show_count, $total_count);
    $limit_str = $page_obj->limit();
    //获取内容

    if($list_type=="checked")
    {
        $list = $activity_code_obj->count_code_is_checked(false,$implode_str,$limit_str);
        //签到列表特殊处理
        foreach($list as $key => $value)
        {
            $enroll_info = $enroll_obj->get_enroll_by_enroll_id($value['enroll_id']);
            $list[$key]['user_id'] = $enroll_info['user_id'];
            $list[$key]['enroll_num'] = $value['c'];

        }
    }
    else
    {
        
        $list = $enroll_obj->get_enroll_list_and_event_info(array("status"=>$status,"event_id"=>$event_id,"table_id"=>$table_id), $type_icon, $limit_str,"enroll_id asc");

        
    }
    
    
    foreach($list as $key => $value)
    {
        
        $tmp_yue_id = $relate_obj->get_relate_yue_id($value['user_id']);
        $list[$key]['user_name'] = poco_cutstr($pai_user_obj->get_user_nickname_by_user_id($tmp_yue_id),20);
        $list[$key]['user_icon'] = $pai_user_icon_obj->get_user_icon($tmp_yue_id, 64);
        //检查是否有被扫码，则不出删除
        $list[$key]['scan_res'] = $activity_code_obj->check_code_scan($value['enroll_id']);
        
    }
    
    $page_select = str_replace('&nbsp;', '', $page_obj->output_pre10.$page_obj->output_pre.$page_obj->output_page.$page_obj->output_back.$page_obj->output_back10);
    //异步处理分页
    $page_reg = '/href=(?:\"|)(.*)(\?|&)p=(\d+)(?:\"| )/isU'; 
    preg_match_all($page_reg,$page_select,$data_arr);
    $page_arr =	$data_arr[3];
    foreach( $page_arr as $key=>$val ){	
        $page_select = str_replace($data_arr[0][$key],"href='javascript:void(0)' data-class=\"J_list_page_ajax\" data-type=\"{$list_type}\" data-table=\"{$table_id}\" data-event=\"{$event_id}\" data-page=\"{$val}\"  ",$page_select);	
    }
    $page_select = str_replace('color:red','color:#737373',$page_select);



    //构造结构
    
    $list_html = "";
    foreach($list as $key => $value)
    {
        if($event_user_role=="author")
        {
            if($list_type=="first")
            {
                
                $btn_html = '<input type="button" class="sh_btn" value="设为候补" data-class="J_backup_btn" data-mark="'.$value['enroll_id'].'" data-table="'.$value['table_id'].'"><br>';
                
                
            }
            else if($list_type=="backup")
            {
                
                $btn_html = '<input type="button" class="sh_btn" value="设为正选" data-class="J_first_btn" data-mark="'.$value['enroll_id'].'" data-table="'.$value['table_id'].'"><br>';
                
            }
            else if($list_type=="onlooker")
            {
                $check_allow = false; 
                $check_allow = check_allow($event_info,$value['user_id']);
                
                if(!$check_allow)
                {
                    if($value['is_accept']==0)
                    {
                        $btn_html ='<input type="button" class="sh_btn" value="通过申请" data-class="J_pass_btn" data-mark="'.$value['enroll_id'].'" data-table="'.$value['table_id'].'"><br>';
                    }
                }
              

                
            }
            
        }
        else if($event_user_role=="admin")
        {
            if($list_type=="first")
            {
                
                $btn_html = '<input type="button" class="sh_btn" value="设为候补" data-class="J_backup_btn" data-mark="'.$value['enroll_id'].'" data-table="'.$value['table_id'].'"><br>';
                if(!$value['scan_res'])
                {
                    $btn_html .= '<input type="button" class="sh_btn" value="删除" data-class="J_del_btn" data-mark="'.$value['enroll_id'].'" data-table="'.$value['table_id'].'" style="display:none;"><br>';
                }
                
                
                
            }
            else if($list_type=="backup")
            {
                
                $btn_html = '<input type="button" class="sh_btn" value="设为正选" data-class="J_first_btn" data-mark="'.$value['enroll_id'].'" data-table="'.$value['table_id'].'"><br>';  
                
                if(!$value['scan_res'])
                {
                    $btn_html .= '<input type="button" class="sh_btn" value="删除" data-class="J_del_btn" data-mark="'.$value['enroll_id'].'" data-table="'.$value['table_id'].'" style="display:none;"><br>';
                }
 
            }
            else if($list_type=="onlooker")
            {
                
                $check_allow = false;
                $check_allow = check_allow($event_info,$value['user_id']);                

                
                if($check_allow)
                {
                    $btn_html ='<input type="button" class="sh_btn" value="删除" data-class="J_del_btn" data-mark="'.$value['enroll_id'].'" data-table="'.$value['table_id'].'"><br>';
                }
                else
                {
                    if($value['is_accept']==0)
                    {
                        $btn_html ='<input type="button" class="sh_btn" value="通过申请" data-class="J_pass_btn" data-mark="'.$value['enroll_id'].'" data-table="'.$value['table_id'].'"><br><input type="button" class="sh_btn" value="删除" data-class="J_del_btn" data-mark="'.$value['enroll_id'].'" data-table="'.$value['table_id'].'">';
                        
                    }
                    else
                    {
                        $btn_html ='<input type="button" class="sh_btn" value="删除" data-class="J_del_btn" data-mark="'.$value['enroll_id'].'" data-table="'.$value['table_id'].'" style="display:none;"><br>';
                    }
                    
                }
            }
            
        }
        
        if($event_user_role=="admin" || $event_user_role=="author")
        {
            $phone_html = '<p>'.$value['phone'].'</p>';
        }
        else
        {
            $phone_html = "";
        }
        
        
        
            $list_html .='<li><div class="img"><img src="'.$value['user_icon'].'" /></div><div class="txt"><p class="name">'.$value['user_name'].'</p><p class="num">（'.$value['enroll_num'].'人）</p>'.$phone_html.'<p class="mb10">'.$btn_html.'</p></div></li>';
            
        
        
    }
    
    

}


//检查是否符合要求
function check_allow($event_info,$user_id)
{
    $setting = unserialize($event_info['setting']);
    if($setting['enroll_mode']==1)
    {
        $check_allow = false;
    }
    else
    {

        if($event_info['join_mode']==0)
        {
            $check_allow = true;
        }
        else if($event_info['join_mode']==1)
        {
            $return = POCO::execute(array('friends.check_friend_exists',$event_info['user_id']), array($user_id));
            if(!$return)
            {
                //如果非好友就等于3  为观望中
                $check_allow = false;
        
            }
            else{

                $check_allow = true;

            }
                
        }
        else if($event_info['join_mode']==3)
        {
            $type_icon   = $event_info['type_icon'];
            $credit_info = POCO::execute('credit_system.credit_system_get_user_all_info', array($user_id));
            $join_level  = (int)$event_info['join_ids'];
            if($type_icon=="photo" || $type_icon=="food")
            {

                $point_info = $credit_info[$type_icon];
                if($point_info['level']<=$join_level){

                    $check_allow = false;
                
                }
                else{

                    $check_allow = true;

                }
            
            }
            else{

                $check_allow = true;

            }
        }
    }
    return $check_allow;
}



//该场总人报名数


$table_total_enroll_count = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(0,1),$table_id);


$res_arr = array(
"ajax_status"=>$ajax_status,
"list_count"=>$list_count,
"list_html"=>iconv("GBK","UTF-8",$list_html),
"page_html"=>iconv("GBK","UTF-8",$page_select),
"table_total_enroll_count"=>$table_total_enroll_count
);
echo json_encode($res_arr);

?>