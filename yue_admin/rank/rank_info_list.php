<?php
/**
 *榜单内容展示列表
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-25 09:40:59
 * @version 1
 */
 include('common.inc.php');
 include('/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php');
 //频道
 /*include_once("/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php");
 $cms_obj                  = new cms_system_class();
 $pai_user_obj             = POCO::singleton ( 'pai_user_class' );*/
 //榜单类
 $pai_rank_event_obj       = POCO::singleton ( 'pai_rank_event_class' );
 $score_rank_obj = POCO::singleton ( 'pai_score_rank_class' );
 $cms_parse_obj = POCO::singleton('pai_cms_parse_class');

 $comment_score_rank_obj = POCO::singleton('pai_comment_score_rank_class');

 $tpl = new SmartTemplate("rank_info_list.tpl.htm");


 $act = $_INPUT['act'] ? $_INPUT['act'] : 'list';
 $location_id    = $_INPUT['location_id'] ? intval($_INPUT['location_id']) : 101029001;
 $role           = trim($_INPUT['role'])  ? trim($_INPUT['role']) : 'cameraman';
 $ret            = $pai_rank_event_obj->get_rank_event_by_location_id_v2($location_id , $role);
 if(!is_array($list)) $list  = array();
 $list = $cms_parse_obj->cms_parse_by_array_for_rank_list($ret);
 if(!is_array($list)) $list = array();

 foreach ($list as $key => $vo) 
 {
    $user_list = $vo['user_list'];
    $where_in_str = '';
    foreach ($user_list as $next_key => $next_vo) 
    {

       if($next_vo['user_id'] > 0)
       {
          $user_list[$next_key]['user_icon'] = yueyue_resize_act_img_url($next_vo['user_icon'], 165);
          $score_arr = $score_rank_obj->get_score_rank ($next_vo['user_id']);
          //魅力
         $user_list[$next_key]['score'] = ( int ) $score_arr ['score'];
         $comment_score = $comment_score_rank_obj->get_comment_score_rank($next_vo['user_id']);
         $user_list[$next_key]['comment_score'] = $comment_score > 0 ? (int)$comment_score*2 : 6;
       }
       
    }
    if(is_array($comment_score_ret)) $user_list = combine_arr($list, $comment_score_ret, 'user_id');

    $list[$key]['user_list'] = $user_list;
    unset($user_list);
 }
 //print_r($list);

 $tpl->assign('location_id', $location_id);
 $tpl->assign('role', $role);
 $tpl->assign('list', $list);
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();