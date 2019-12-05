<?php

//****************** wap版 头部通用 start  ******************

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'topic/topic.tpl.html');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 底部公共文件引入
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$wap_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('wap_global_top', $wap_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap版 头部通用 end  ******************

//$ret['content'] = preg_replace('/(<img\s*?)src/',"$1data-lazyload-url",$ret['content']);

$ret['content'] .= $ret['content_v2'];

if($user_agent_arr['is_yueyue_app'] != 1)
{

    $pat = '/<a(.*?)href=[\'\"]?([^\'\" ]+).*?>/i';

    $ret['content'] = preg_replace_callback($pat,"mall_topic_page_replace_function",$ret['content']);
    //$ret['tpl_json'] = preg_replace_callback($pat,"mall_topic_page_replace_function",$ret['tpl_json']);

}

$output_arr['data'] = $ret;


$tpl->assign('ret',$ret);
$tpl->assign('share_text',$share_text);

?>