<?php
include_once('./common.inc.php');
$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
$material_type_enum = array(
    'news'=>'图文', 
    'm_news'=>'多图文', 
    'image'=>'图片', 
    'voice'=>'语音', 
    'video'=>'视频', 
); 

$bind_id = $_COOKIE['cur_bind_id'];
$act     = $_INPUT['act'];

if( $act=='manually_sync')
{
    $news_rst = $weixin_helper_obj->sync_material($bind_id, $type = 'news');
    $image_rst = $weixin_helper_obj->sync_material($bind_id, $type = 'image');

    $tips_str_news = "同步图文素材失败，{$news_rst['message']}";
    if( $news_rst['err_code']==1 )
    {
        $tips_str_news = "同步图文素材成功，此次更新的数量为{$news_rst['quantity']}";
    }

    $tips_str_image = "同步图片素材失败，{$image_rst['message']}";
    if( $image_rst['err_code']==1 )
    {
        $tips_str_image = "同步图片素材成功，此次更新的数量为{$image_rst['quantity']}";
    }

    pop_msg($tips_str_news.'\r\n'.$tips_str_image, 'material.php');

    //$weixin_helper_obj->sync_material($bind_id, $type = 'voice');
    //$weixin_helper_obj->sync_material($bind_id, $type = 'video');
}

$where_str = '1';

$type = trim($_INPUT['type']);
if( strlen($type) )
{
    $where_str .= " AND material_type='{$type}'";
}

$tpl = $my_app_pai->getView('material_list.tpl.htm');
$header_html = $my_app_pai->webControl('WxpubAdminPageHeader',$header_arr,true);

$total_count = $weixin_helper_obj->get_material_list($bind_id, true, $where_str);

$page_obj    = POCO::singleton('show_page');

$page_obj->setvar(array('type'=>$type));
$page_obj->set(20, $total_count);

$material_list = $weixin_helper_obj->get_material_list($bind_id, false, $where_str, 'update_time DESC', $page_obj->limit());

foreach( $material_list as &$material_info )
{
    $material_info['update_time_str'] = date('Y-m-d H:i:s', $material_info['update_time']);
    $material_info['add_time_str'] = date('Y-m-d H:i:s', $material_info['add_time']);
    $material_info['type_show'] = $material_type_enum[$material_info['material_type']];
    $material_info['item_list'] = json_decode($material_info['item_content'], true);
    $item_list = json_decode($material_info['item_content'], true);
    if( $material_info['material_type']=='m_news' )//多图文, 内容整理
    {
        $material_info['title_list'] = array();
        $material_info['digest_list'] = array();
        $material_info['url_list'] = array();

        foreach( $item_list as $item_info)
        {
            $material_info['title_list'][] = array('title'=>iconv('utf-8', 'gbk', $item_info['title']));
            $material_info['digest_list'][] = array('digest'=>iconv('utf-8', 'gbk', $item_info['digest']));
            $material_info['url_list'][] = array('url'=>$item_info['url']);
        }

    }
    elseif( $material_info['material_type']=='news' )//单图文, 内容整理
    {
        $material_info['title'] = iconv('utf-8', 'gbk', $item_list[0]['title']);
        $material_info['digest'] = iconv('utf-8', 'gbk', $item_list[0]['digest']);
        $material_info['url'] = $item_list[0]['url'];
    }
    else//图片, 语音, 视频, 内容整理
    {
        $material_info['title'] = iconv('utf-8', 'gbk', $item_list['name']);
        $material_info['url'] = $item_list['url'];
    }
}

$page_select   = $page_obj->output(true);
$tpl->assign('material_list', $material_list);
$tpl->assign('type_enum', $material_type_enum);
$tpl->assign('type', $type);
$tpl->assign('page_select', $page_select);
$tpl->output();
exit;
?>
