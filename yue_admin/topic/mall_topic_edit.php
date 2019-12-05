<?php

include_once 'common.inc.php';
include_once 'top.php';
//引入省数据
include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/locate_file.php';
include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php';
$tpl = new SmartTemplate("mall_topic_edit.tpl.htm");

$act            = $_INPUT['act'] ? $_INPUT['act'] :'add';
$topic_id       = (int)$_INPUT['id'];
$title          = $_INPUT['title'];
$content        = $_POST['content'];
$sort           = $_INPUT['sort'];
$cover_image    = $_INPUT['cover_image'];
$display_type   = $_INPUT['display_type'];
$type           = $_INPUT['topic_type'];
$content_type   = $_INPUT['content_type'];
$issue_id       = $_INPUT['issue_id'];
$is_button      = $_INPUT['is_button'];
$location_id    = $_INPUT['location_id'];
$tpl_type       = $_INPUT['tpl_type'] ? $_INPUT['tpl_type'] : 'none';
$version_type   = $_INPUT['version_type'] ? $_INPUT['version_type'] : 'old';


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
            $insert_data['content']          = str_replace("http://image16-c.poco.cn","http://img16.poco.cn",$content);
            $insert_data['cover_image']      = str_replace("http://image16-c.poco.cn","http://img16.poco.cn",$cover_image);
            $insert_data['add_time']         = time();
            $insert_data['author']           = $nickname;
            $insert_data['display_type']     = $display_type;
            $insert_data['author_id']        = $yue_login_id;
            $insert_data['sort']             = $sort;
            $insert_data['type']             = $type;
            $insert_data['content_type']     = $content_type;
            $insert_data['issue_id']         = $issue_id;
            $insert_data['location_id']      = $location_id;
            $insert_data['tpl_type']         = $tpl_type;
            $insert_data['is_button']        = $is_button;
            $insert_data['version_type']     = "new";
            $topic_id = $topic_obj->add_topic($insert_data);

        }
        break;

    case 'edit':
        $topic_info = $topic_obj->get_topic_info($topic_id);
        $topic_info['province_id'] = substr($topic_info['location_id'],0,6);


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

            $update_data['title'] = $title;
            $update_data['content'] = str_replace("http://image16-c.poco.cn","http://img16.poco.cn",$content);
            $update_data['cover_image'] = str_replace("http://image16-c.poco.cn","http://img16.poco.cn",$cover_image);
            $update_data['sort'] = $sort;
            $update_data['display_type'] = $display_type;
            $update_data['type']             = $type;
            $update_data['content_type']     = $content_type;
            $update_data['issue_id']         = $issue_id;
            $update_data['location_id']      = $location_id;
            $update_data['tpl_type']         = $tpl_type;
            $update_data['is_button']        = $is_button;



            $topic_obj->update_topic($update_data, $topic_id);

        }
        break;
}


if($_POST['act'])
{

    $topic_obj->del_topic_tpl($topic_id);

    $img_tpl = $_POST['img_tpl']; //图片模版
    $img_tpl_sort = $_POST['img_tpl_sort'];

    $text_tpl_title = $_POST['text_tpl_title'];
    $text_tpl_text = $_POST['text_tpl_text'];
    $text_tpl_sort = $_POST['text_tpl_sort'];

    $goods_tpl1_id = $_POST['goods_tpl1_id'];
    $goods_tpl1_text = $_POST['goods_tpl1_text'];
    $goods_tpl1_url = $_POST['goods_tpl1_url'];
    $goods_tpl1_tag = $_POST['goods_tpl1_tag'];
    $goods_tpl1_sort = $_POST['goods_tpl1_sort'];

    $goods_tpl2_sort = $_POST['goods_tpl2_sort'];
    $goods_tpl2_id = $_POST['goods_tpl2_id'];
    $goods_tpl2_text = $_POST['goods_tpl2_text'];
    $goods_tpl2_url = $_POST['goods_tpl2_url'];

    $goods_tpl3_sort = $_POST['goods_tpl3_sort'];
    $goods_tpl3_id = $_POST['goods_tpl3_id'];
    $goods_tpl3_text = $_POST['goods_tpl3_text'];
    $goods_tpl3_url = $_POST['goods_tpl3_url'];

    $list_tpl1_sort = $_POST['list_tpl1_sort'];
    $list_tpl1_img = $_POST['list_tpl1_img'];
    $list_tpl1_title = $_POST['list_tpl1_title'];
    $list_tpl1_subtitle = $_POST['list_tpl1_subtitle'];

    $list_tpl2_sort = $_POST['list_tpl2_sort'];
    $list_tpl2_img = $_POST['list_tpl2_img'];
    $list_tpl2_title = $_POST['list_tpl2_title'];
    $list_tpl2_subtitle = $_POST['list_tpl2_subtitle'];
    $list_tpl2_button = $_POST['list_tpl2_button'];

    $attr_goods_id_tpl = $_POST['attr_goods_id_tpl'];
    $attr_tpl_sort = $_POST['attr_tpl_sort'];

    //图片模版
    foreach($img_tpl as $k=>$img)
    {
        if($img)
        {
            $img_tpl_arr['img_url'] = $img;

            $insert_tpl_data['topic_id'] = $topic_id;
            $insert_tpl_data['tpl_type'] = "img_tpl";
            $insert_tpl_data['custom_data'] = serialize($img_tpl_arr);
            $insert_tpl_data['sort'] = $img_tpl_sort[$k];

            $topic_obj->add_topic_tpl($insert_tpl_data);
        }
    }

    //文案模版
    foreach($text_tpl_text as $k=>$text)
    {
        if($text)
        {
            $text_tpl_arr['title'] = $text_tpl_title[$k];
            $text_tpl_arr['text'] = $text;
            $insert_tpl_data['topic_id'] = $topic_id;
            $insert_tpl_data['tpl_type'] = "text_tpl";
            $insert_tpl_data['custom_data'] = serialize($text_tpl_arr);
            $insert_tpl_data['sort'] = $text_tpl_sort[$k];

            $topic_obj->add_topic_tpl($insert_tpl_data);
        }
    }

    //商品模版一

    foreach($goods_tpl1_id as $k=>$goods_id_arr)
    {
        if($goods_id_arr)
        {
            foreach ($goods_id_arr as $gk => $goods_id) {
                $goods_tpl1_arr[$gk]['goods_id'] = trim($goods_id);
                $goods_tpl1_arr[$gk]['goods_text'] = $goods_tpl1_text[$k][$gk];
                $goods_tpl1_arr[$gk]['goods_tag'] = $goods_tpl1_tag[$k][$gk];
                $goods_tpl1_arr[$gk]['goods_url'] = $goods_tpl1_url[$k][$gk];
            }

            $insert_tpl_data['topic_id'] = $topic_id;
            $insert_tpl_data['tpl_type'] = "goods_tpl1";
            $insert_tpl_data['custom_data'] = serialize($goods_tpl1_arr);
            $insert_tpl_data['sort'] = $goods_tpl1_sort[$k];

            $topic_obj->add_topic_tpl($insert_tpl_data);

            unset($goods_tpl1_arr);
        }
    }


    //商品模版二
    foreach($goods_tpl2_id as $k=>$goods_id)
    {
        if($goods_id)
        {
            $goods_tpl2_arr['goods_id'] = trim($goods_id);
            $goods_tpl2_arr['goods_text'] = $goods_tpl2_text[$k];
            $goods_tpl2_arr['goods_url'] = $goods_tpl2_url[$k];

            $insert_tpl_data['topic_id'] = $topic_id;
            $insert_tpl_data['tpl_type'] = "goods_tpl2";
            $insert_tpl_data['custom_data'] = serialize($goods_tpl2_arr);
            $insert_tpl_data['sort'] = $goods_tpl2_sort[$k];

            $topic_obj->add_topic_tpl($insert_tpl_data);
        }
    }

    //商品模版三
    foreach($goods_tpl3_id as $k=>$goods_id)
    {
        if($goods_id)
        {
            $goods_tpl3_arr['goods_id'] = trim($goods_id);
            $goods_tpl3_arr['goods_text'] = $goods_tpl3_text[$k];
            $goods_tpl3_arr['goods_url'] = $goods_tpl3_url[$k];

            $insert_tpl_data['topic_id'] = $topic_id;
            $insert_tpl_data['tpl_type'] = "goods_tpl3";
            $insert_tpl_data['custom_data'] = serialize($goods_tpl3_arr);
            $insert_tpl_data['sort'] = $goods_tpl3_sort[$k];

            $topic_obj->add_topic_tpl($insert_tpl_data);
        }
    }


    //列表模版一
    foreach($list_tpl1_img as $k=>$img)
    {
        if($img)
        {
            $list_tpl1_arr['img_url'] = $img;
            $list_tpl1_arr['title'] = $list_tpl1_title[$k];
            $list_tpl1_arr['subtitle'] = $list_tpl1_subtitle[$k];

            $insert_tpl_data['topic_id'] = $topic_id;
            $insert_tpl_data['tpl_type'] = "list_tpl1";
            $insert_tpl_data['custom_data'] = serialize($list_tpl1_arr);
            $insert_tpl_data['sort'] = $list_tpl1_sort[$k];

            $topic_obj->add_topic_tpl($insert_tpl_data);
        }
    }

    //列表模版二
    foreach($list_tpl2_img as $k=>$img)
    {
        if($img)
        {
            $list_tpl2_arr['img_url'] = $img;
            $list_tpl2_arr['title'] = $list_tpl2_title[$k];
            $list_tpl2_arr['subtitle'] = $list_tpl2_subtitle[$k];
            $list_tpl2_arr['img_url'] = $img;
            $list_tpl2_arr['button'] = $list_tpl2_button[$k];


            $insert_tpl_data['topic_id'] = $topic_id;
            $insert_tpl_data['tpl_type'] = "list_tpl2";
            $insert_tpl_data['custom_data'] = serialize($list_tpl2_arr);
            $insert_tpl_data['sort'] = $list_tpl2_sort[$k];

            $topic_obj->add_topic_tpl($insert_tpl_data);
        }
    }

    //商品属性模版
/*    foreach($attr_goods_id_tpl as $k=>$goods_id)
    {
        if($goods_id)
        {
            $attr_goods_arr['goods_id'] = trim($goods_id);

            $insert_tpl_data['topic_id'] = $topic_id;
            $insert_tpl_data['tpl_type'] = "attr_goods_tpl";
            $insert_tpl_data['custom_data'] = serialize($attr_goods_arr);
            $insert_tpl_data['sort'] = $attr_tpl_sort[$k];

            $topic_obj->add_topic_tpl($insert_tpl_data);
        }
    }*/

    echo "<script>alert('提交成功');parent.location.href='topic_list.php';</script>";
}


/////////////////
$global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
$tpl->assign ( 'global_header_html', $global_header_html );
/////////////////

$tpl_ret=$topic_obj->get_topic_tpl_list($topic_id);
$tpl_ret = poco_iconv_arr($tpl_ret,'GBK', 'UTF-8');
$tpl_json = json_encode($tpl_ret);

$tpl->assign('tpl_json', $tpl_json);

$tpl->assign($topic_info);
$tpl->assign('act', $act);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->output();

?>