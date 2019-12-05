<?php 

/**
 * 约拍列表 
 * 
 */

	include_once 'common.inc.php';
 	$model_order_org_obj  = POCO::singleton('pai_order_org_class');
    //评价
    $model_comment_log_obj = POCO::singleton('pai_model_comment_log_class');
    $uid    = $_INPUT['uid']     ? $_INPUT['uid'] : 0;
    $yue_count = $model_order_org_obj->get_order_count_by_user_id(true, $uid);
    $list = $model_order_org_obj->get_order_count_by_user_id(false, $uid, '' , $limit = "3,{$yue_count}",'date_id DESC', $fields = '*');
    if (!empty($list) && is_array($list)) 
    {
        foreach ($list as $key => $vo) 
        {
           $list[$key]['date_style'] = iconv('GB2312','UTF-8', $vo['date_style']);
           $list[$key]['date_address'] = iconv('GB2312','UTF-8', $vo['date_address']);
           $list[$key]['cameraman_nick_name'] = iconv('GB2312','UTF-8',get_user_nickname_by_user_id($vo['from_date_id']));
           $model_coment_info = $model_comment_log_obj->get_model_comment_by_date_id($vo['date_id']);
           $list[$key]['comment_text'] = iconv('GB2312','UTF-8',$model_coment_info['comment']);
           $list[$key]['comment_desc'] = iconv('GB2312','UTF-8',poco_cutstr($model_coment_info['comment'], 20, '....'));
           $list[$key]['date_time'] = date('Y-m-d', $vo['date_time']);
        }
        # code...
    }
    $arr  = array
    (
    	'msg'  => 'success',
    	'list' => $list 
    );
    echo json_encode($arr);
    
 ?>