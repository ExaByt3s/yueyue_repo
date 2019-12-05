<?php
/**
 * 商家等级评定
 * @author ljl
 */
include_once 'common.inc.php';

$goods_obj = POCO::singleton('pai_mall_goods_class');
$seller_obj = POCO::singleton('pai_mall_seller_class');
$type_id = (int)$_INPUT['type_id'];
$sumit_type = $_INPUT['submit_type'];
$jump_url = urldecode($_INPUT['jump_url']);
$user_id = $_INPUT['user_id'];
$seller_rating = ($_INPUT['seller_rating'] === '') ? 0 : $_INPUT['seller_rating'];

if(  ! $user_id )
{
    js_pop_msg('user_id不能为空');
    
}
if( ! $type_id )
{
    js_pop_msg('不能没有type_id');
}
if( ! $submit_type)
{
    js_pop_msg('提交的类型不能为空');
}
if( ! $jump_url )
{
    js_pop_msg('跳转的url不能为空');
}


$seller_rating_config = pai_mall_load_config('seller_rating');
if(empty($seller_rating_config[$type_id]))
{
    js_pop_msg('这个type_id:'.$type_id."没有找到相应的配置");
}

$type_id_rating_config = $seller_rating_config[$type_id];
if(isset($_INPUT['seller_rating']) )
{
    if($seller_rating !=='' )
    {
        foreach($type_id_rating_config as $k => $v)
        {
            if($v['value'] == $seller_rating)
            {
                $type_id_rating_config[$k]['selected'] = 1;
            }
        }
    }
}


if($_INPUT['do_update'])
{
    
    $rs = $seller_obj->update_seller_rating($user_id,$type_id,$seller_rating);
    if($rs)
    {
        echo json_encode(1);
    }
    exit;
    
    
}

//获取操作日志
$task_log_obj = POCO::singleton('pai_task_admin_log_class');
$log_list = $task_log_obj->get_log_by_type(array('type_id'=>3005,'action_id'=>$user_id));
if($log_list)
{
    foreach($log_list as $key => $val)
    {
        $log_list[$key]['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
        $log_list[$key]['user_name'] = get_user_nickname_by_user_id($val['admin_id']);
    }
}

$type_name = $goods_obj->get_goods_typename_for_type_id($type_id);



$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."seller_rating.tpl.htm" );
$tpl->assign('type_id_rating_config',$type_id_rating_config );
$tpl->assign('type_id',$type_id);
$tpl->assign('type_name',$type_name);
$tpl->assign('submit_type',$submit_type);
$tpl->assign('jump_url',$jump_url);
$tpl->assign('user_id',$user_id);
$tpl->assign('log_list',$log_list);

$tpl->output();



