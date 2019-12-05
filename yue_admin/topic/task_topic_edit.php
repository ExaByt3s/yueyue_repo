<?php

include_once 'common.inc.php';
include_once 'top.php';
//引入省数据
include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/locate_file.php';
include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php';
$tpl = new SmartTemplate("task_topic_edit.tpl.htm");

$act            = $_INPUT['act'] ? $_INPUT['act'] :'add';
$topic_id       = (int)$_INPUT['id'];
$title          = $_INPUT['title'];
$content        = $_POST['content'];
$cover_image    = $_INPUT['cover_image'];
$type    = $_INPUT['type'];



$topic_obj = POCO::singleton('pai_topic_class');



switch ($act)
{
    case 'add':
        if($_POST['act'])
        {
            if(!$title)
            {
                js_pop_msg("标题不能为空");
                exit;
            }

            if(!$cover_image)
            {
                js_pop_msg("封面图不能为空");
                exit;
            }


            $exp        =Array("/height=.{0,5}\s/i","/width=.{0,5}\s/i");
            $exp_o      =Array('','');
            $content    = preg_replace($exp,$exp_o,$content);


            $insert_data['title']            = $title;
            $insert_data['content']          = $content;
            $insert_data['type']          = $type;
            $insert_data['img']      = $cover_image;
            $insert_data['add_user_id']      = $yue_login_id;
            $insert_data['add_time']         = time();


            $topic_id = $topic_obj->add_task_topic($insert_data);

            echo "<script>alert('添加成功');parent.location.href='task_topic_list.php';</script>";

        }
        break;

    case 'edit':
        $topic_info = $topic_obj->get_task_detail($topic_id);



        if($_POST['act'])
        {
            if(!$title)
            {
                js_pop_msg("标题不能为空");
                exit;
            }

            if(!$cover_image)
            {
                js_pop_msg("封面图不能为空");
                exit;
            }


            if(!$topic_id)
            {
                js_pop_msg("更新ID不能为空");
                exit;
            }

            $update_data['title']            = $title;
            $update_data['content']          = $content;
            $update_data['img']      = $cover_image;
            $update_data['type']      = $type;



            $topic_obj->update_task_topic($update_data, $topic_id);

            echo "<script>alert('修改成功');parent.location.href='task_topic_list.php';</script>";


        }
        break;
}




/////////////////
$global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
$tpl->assign ( 'global_header_html', $global_header_html );
/////////////////


$tpl->assign($topic_info);
$tpl->assign('act', $act);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->output();

?>