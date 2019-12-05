<?php

include_once 'common.inc.php';
include_once 'top.php';
//引入省数据
include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/locate_file.php';
include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php';
$tpl = new SmartTemplate("promotion_topic_edit.tpl.htm");

$act            = $_INPUT['act'] ? $_INPUT['act'] :'add';
$topic_id       = (int)$_INPUT['id'];
$title          = $_INPUT['title'];
$content        = $_POST['content'];
$begin_time     = $_INPUT['begin_time'];
$end_time       = $_INPUT['end_time'];
$city           = $_INPUT['city'];
$goods_type     = $_INPUT['goods_type'];
$event_type     = $_INPUT['event_type'];
$cover_image    = $_INPUT['cover_image'];



$topic_obj = POCO::singleton('pai_topic_class');

foreach($city as $k=>$location_id)
{
    if($location_id==0)
    {
        unset($city[$k]);
    }
}

$event_city = implode(",",$city);
$join_type = implode(",",$goods_type);



if(!$event_city)
{
    $event_city=0;
}

$city_conf = $topic_obj->promotion_city_conf;
$type_conf = $topic_obj->promotion_type_conf;

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

            if(!$begin_time)
            {
                js_pop_msg("开始时间不能为空");
                exit;
            }

            if(!$end_time)
            {
                js_pop_msg("结束时间不能为空");
                exit;
            }


            if(!$join_type)
            {
                js_pop_msg("参加品类不能为空");
                exit;
            }


            $exp        =Array("/height=.{0,5}\s/i","/width=.{0,5}\s/i");
            $exp_o      =Array('','');
            $content    = preg_replace($exp,$exp_o,$content);


            $insert_data['title']            = $title;
            $insert_data['content']          = $content;
            $insert_data['img']      = $cover_image;
            $insert_data['add_time']         = time();
            $insert_data['begin_time']       = strtotime($begin_time);
            $insert_data['end_time']       = strtotime($end_time);
            $insert_data['event_city']     = $event_city;
            $insert_data['event_type']     = $event_type;
            $insert_data['join_type']     = $join_type;


            $topic_id = $topic_obj->add_promotion_topic($insert_data);

            echo "<script>alert('添加成功');parent.location.href='promotion_topic_list.php';</script>";

        }
        break;

    case 'edit':
        $topic_info = $topic_obj->get_promotion_detail($topic_id);
        $topic_info['begin_time'] = date('Y-m-d',$topic_info['begin_time']);
        $topic_info['end_time'] = date('Y-m-d',$topic_info['end_time']);

        $event_city_arr = explode(",",$topic_info['event_city']);
        $join_type_arr = explode(",",$topic_info['join_type']);


        foreach($event_city_arr as $val)
        {
            $event_city_key_arr[$val]=1;
        }

        foreach($join_type_arr as $val)
        {
            $join_type_key_arr[$val]=1;
        }

        foreach($city_conf as $key=>$val)
        {
            if($event_city_key_arr[$key])
            {
                $city_str .= "<input type=\"checkbox\" name=\"city[]\" value=\"$key\" checked=\"checked\" />$val";
            }
            else
            {
                $city_str .= "<input type=\"checkbox\" name=\"city[]\" value=\"$key\"  />$val";
            }
        }

        foreach($type_conf as $key=>$val)
        {
            if($join_type_key_arr[$key])
            {
                $goods_type_str .= "<input type=\"checkbox\" name=\"goods_type[]\" value=\"$key\" checked=\"checked\" />$val";
            }
            else
            {
                $goods_type_str .= "<input type=\"checkbox\" name=\"goods_type[]\" value=\"$key\"  />$val";
            }
        }



        $topic_info['city'] = $city_str;
        $topic_info['goods_type'] = $goods_type_str;

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


            if(!$join_type)
            {
                js_pop_msg("参加品类不能为空");
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
            $update_data['begin_time']       = strtotime($begin_time);
            $update_data['end_time']       = strtotime($end_time);
            $update_data['event_city']     = $event_city;
            $update_data['event_type']     = $event_type;
            $update_data['join_type']     = $join_type;



            $topic_obj->update_promotion_topic($update_data, $topic_id);

            echo "<script>alert('修改成功');parent.location.href='promotion_topic_list.php';</script>";


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