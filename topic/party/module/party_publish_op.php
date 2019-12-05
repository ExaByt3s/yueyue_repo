<?php
/** 
 * 活动发布动作页页
 * 
 * author 星星
 * 
 * 2014-7-29
 */


define('G_SIMPLE_INPUT_CLEAN_VALUE',1);
include_once("./party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
$event_check_obj	= POCO::singleton('event_check_class');
$event_table_obj    = POCO::singleton('event_table_class');
$event_table_check_obj = POCO::singleton('event_table_check_class');
$event_details_obj = POCO::singleton('event_details_class');
$admin_user_obj = POCO::singleton('event_admin_user_class');
//新增支付版
$__mp_manage_branch = 1;//开发版为支付版，非开发版为非支付
$act= trim($_INPUT['act']);
$event_id = (int)$_INPUT['event_id'];


$data = array();

//信息处理
    //普通数据
    $data['title'] = trim($_INPUT['title']);//标题
    $data['type_icon'] = "photo";//约约活动写死为photo
    
    

   
    //处理地区
    $location_radio = (int)$_INPUT['location_radio'];
    if($location_radio==1)
    {
        //获取对应的location_id
        $locate_id_1 = $_INPUT['location_id1'];
        $locate_id_2 = $_INPUT['location_id2'];
        
        if(empty($locate_id_2))
        {
            $data['location_id'] = $locate_id_1;
        }
        else
        {
            $data['location_id'] = $locate_id_2;
        }

            
    }
    else
    {
        $data['location_id'] = "";
    }
    
    $data['address'] = trim($_INPUT['address']);//地址
    
    //费用
    if($act=="add" || $act=="preview")
    {
        $data['budget'] = $_INPUT['budget'];

    }
    
    //加入模式
    $data['join_mode'] = (int)$_INPUT['join_mode'];
    
    

    


    
    
    //取标签内容
    //退款方式
    $pay_type_arr = $_INPUT['pay_type'];
    $data['pay_type'] = (int)$pay_type_arr[0];
    

    //封面图item_id
    $cover_image_item_id=(int)$_INPUT['cover_image_item_id'];
    
    $setting_arr = array('cover_image_item_id'=>$cover_image_item_id);
    $data['setting'] = serialize($setting_arr);

//介绍处理

    //封面图
    $data['cover_image'] = trim($_INPUT['cover_image']);
    //主题
    $data['content'] = trim($_INPUT['content']);
    
    //模特，图文模块
    $text_part = $_INPUT['text'];
    $model_part = $_INPUT['model_part'];
    
    
    
    $part_num = count($text_part);
    
    for($i=0;$i<$part_num;$i++)
    {
        $other_info[$i]['text'] = trim($text_part[$i]);
        $tmp_img_mark = $model_part[$i];
        
        $tmp_img = $_INPUT['upload_imgs_'.$tmp_img_mark];
        
        
        if(empty($tmp_img))
        {
            
            $other_info[$i]['img'] = array();
        }
        else
        {
            foreach($tmp_img as $key => $value)
            {
                $other_info[$i]['img'][$key] = $value;
            }
        }
  
    }
    
    //图文模块图片
    $other_info_str = serialize($other_info);
    $data['other_info'] = $other_info_str; 
 
//活动流程，场次
    
    //总备注
    $data['remark'] = trim($_INPUT['remark']);
    //领队
    $leader_nickname = $_INPUT['leader_nickname'];//领队名字
    $leader_phone = $_INPUT['leader_phone'];//联系方式
    
    //领队
    foreach($leader_nickname as $key => $value)
    {
        $leader_info[$key]['name'] = $value;
        $leader_info[$key]['mobile'] = $leader_phone[$key];
        
    }


    $leader_info_str = serialize($leader_info);
    $data['leader_info'] = $leader_info_str; 


    //基础数据
    if($act=="add" || $act=="preview")
    {
        $data['user_id'] = $login_id;                              //创建人ID
    }
    $data['category'] = 2;                                     //活动大类（线上，线下）
    
    //上线后改
    $data['limit_num']      = 10;                                    //活动人数，0为无限
    $data['limit_enroll']   = 1;                                     //一次报名能报几个，默认1个
        
 
    $data['add_time']       = time();                                //添加时间
    $data['status']         = 0;                                     //活动状态，0为未开始，1进行中，2已结束
    //基础数据
    
    $data['last_update_time']=time();
    //var_dump($data);

//获取场次
$site_start_time = $_INPUT['site_start_time'];//场次开始时间
$site_end_time = $_INPUT['site_end_time'];//场次结束时间
$site_join_num = $_INPUT['site_join_num'];//场次人数
$table_id = $_INPUT['table_id'];//场次正式表ID（若未发布则没有）

//特殊处理游学院数据，上线后改

/********10月13号修改时间*****************/
    //整理场次结构
    foreach($site_start_time as $key => $value)
    {
        if($key>9)
        {
            break;//防止场次过多
        }
        $table_data[$key]['begin_time'] = strtotime(trim($site_start_time[$key]));
        $table_data[$key]['end_time'] = strtotime(trim($site_end_time[$key]));
        $table_data[$key]['num'] = $site_join_num[$key];
        $table_data[$key]['table_id'] = (int)$table_id[$key];
        
        //获取最后一场时间
        $end_time = $site_end_time[$key];
    }

    $data['start_time'] = strtotime(trim($site_start_time[0]));//第一场起始时间
    $data['end_time'] = strtotime(trim($end_time));//最后一场结束时间
    
    //场次数据
    $data['table_data'] = serialize($table_data);

/********10月13号修改时间*****************/

//避免修改时候变成官方
if($act=="add" || $act=="preview")
{
    $data['is_authority'] = $admin_user_obj->get_is_authority_by_user_id($login_id,$data['type_icon']);		//如果是则当是官方发布
}
//测试调整，上线后改
$system_obj = POCO::singleton('event_system_class');

$data       = $system_obj->merge_extra_input_data($data,$data['type_icon']);       //获取额外的内容




/******2015-1-28********/
//机构ID处理
$relate_user_obj = POCO::singleton ('pai_relate_poco_class');
$yue_user_id = $relate_user_obj->get_relate_yue_id($login_id);
$relate_org_obj = POCO::singleton ( 'pai_model_relate_org_class' );
$org_info = $relate_org_obj->get_org_info_by_user_id($yue_user_id);
$data['org_user_id'] = (int)$org_info['org_id'];
/******2015-1-28********/
//特殊处理，测试使用，之后可删
$match_publish_arr = array(66096046,175335503,65804368,174291459,177149128);
if(in_array($login_id,$match_publish_arr))
{
    $data['type_icon'] = 'yuepai';
}
//特殊处理，测试使用，之后可删


if($act=="add")
{
    
    $data['new_version'] = 2;
        

    echo "正在发布...";
    $check_id = $event_check_obj->add_event($data, 0);
    if(!empty($check_id))
    {
        echo '<SCRIPT LANGUAGE="JavaScript">parent.location="http://event.poco.cn/event_audit.php?c_id='.$check_id.'"</SCRIPT>';
    }

    
}
else if($act=="update")
{
    
    echo "编辑提交中...";
    
    if(empty($event_id))
    {
        echo '<SCRIPT LANGUAGE="JavaScript">alert("活动ID为空，出错");parent.location="http://event.poco.cn/"</SCRIPT>';
    }
    
    //检查是否在正式表
    $details_info = $event_details_obj->get_event_by_event_id($event_id);
    if(empty($details_info))
    {

        echo '<SCRIPT LANGUAGE="JavaScript">alert("活动ID出错");parent.location="http://event.poco.cn/event_list.php"</SCRIPT>';
        exit();
    }
    
    if($details_info['user_id']!=$login_id)
    {
        //判断是否管理人员
        $webcheck_patch = "/disk/data/htdocs232/poco/webcheck/";
        include_once $webcheck_patch."admin_function.php";
        $is_event_admin = admin_chk("event", "admin",  $login_id); //是否管理员
        if(!$is_event_admin)
        {
            //header("location:event_list.php");
            
            echo '<SCRIPT LANGUAGE="JavaScript">alert("出错，非活动发布者或者管理员");parent.location="http://event.poco.cn/event_list.php"</SCRIPT>';
            exit();
        }
        
    }
    
    
    $data['last_update_time']=$details_info['last_update_time'];
    $check_info = $event_check_obj->get_event_by_event_id($event_id);
    if(empty($check_info))
    {
        
        echo '<SCRIPT LANGUAGE="JavaScript">alert("审核表没有记录，操作有误");parent.location="http://event.poco.cn/event_list.php"</SCRIPT>';
        exit();
    }
    
    $check_id = $check_info['check_id'];
    $ret=$event_check_obj->update_event($data,$check_id);				//更新审核表
    if($ret)
    {
       
        echo '<SCRIPT LANGUAGE="JavaScript">parent.location="http://event.poco.cn/event_audit.php?c_id='.$check_id.'&edit=1"</SCRIPT>';
        exit();
    }
    else
    {
        echo '<SCRIPT LANGUAGE="JavaScript">alert("更新出错，请稍后再试");parent.location="http://event.poco.cn/event_list.php"</SCRIPT>';
        exit();
    }
    

     
    
    
}
else if($act=="preview")
{
    
    echo "正在跳往预览页，";
    //处理场次
    $data['cache_table_arr'] = $table_data;
    
    
    $time_mark_value = date("Ymdhis",time());
    $cache_key = "event_preview_".$login_id."_".$time_mark_value;
    $set_cache_res = POCO::setCache($cache_key,$data, array('life_time'=>864000));
    if($set_cache_res)
    {
        echo "页面跳转中...";
        //header("Location:http://event.poco.cn/event_browse.php?user_id=".$data['user_id']."&time=".$time_mark_value."&act=preview");
        
        echo '<SCRIPT LANGUAGE="JavaScript">parent.location="http://event.poco.cn/event_browse.php?user_id='.$login_id.'&time='.$time_mark_value.'&act=preview"</SCRIPT>';
    }
    else
    {
        echo"数据有误，请稍后再试";
        die();
    }
}

?>