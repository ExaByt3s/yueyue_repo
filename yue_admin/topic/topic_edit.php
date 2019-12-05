<?php

include_once 'common.inc.php';
include_once 'top.php';
//引入省数据
include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/locate_file.php';
include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php';
$tpl = new SmartTemplate("topic_edit.tpl.htm");

$act            = $_INPUT['act'] ? $_INPUT['act'] :'add';
$id             = (int)$_INPUT['id'];
$title          = $_INPUT['title'];
$content        = $_POST['content'];
$sort           = $_INPUT['sort'];
$cover_image    = $_INPUT['cover_image'];
$display_type    = $_INPUT['display_type'];
$type    = $_INPUT['type'];
$content_type    = $_INPUT['content_type'];
$issue_id    = $_INPUT['issue_id'];
$is_button    = $_INPUT['is_button'];
$location_id    = $_INPUT['location_id'];
$tpl_type       = $_INPUT['tpl_type'] ? $_INPUT['tpl_type'] : 'none';
$version_type   = $_INPUT['version_type'] ? $_INPUT['version_type'] : 'old';
$topic_obj = POCO::singleton('pai_topic_class');
//省列表
$province_list = change_assoc_arr($arr_locate_a);
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
			
			if(!$content)
			{
				js_pop_msg("内容不能为空");
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
            $insert_data['version_type']        = $version_type;
			$topic_obj->add_topic($insert_data);
			
			echo "<script>alert('添加成功');parent.location.href='topic_list.php';</script>";
		}
	break;
	
	case 'edit':
		$topic_info = $topic_obj->get_topic_info($id);
		if (!empty($topic_info['location_id'])) 
		{
			$prov_id = substr($topic_info['location_id'], 0 , 6);
			foreach ($province_list as $key => $vo) 
		    {
		       if ($vo['c_id'] == $prov_id) 
		       {
		          $province_list[$key]['selected_prov'] = "selected='true'";
		       }
		    }
		    $city_list = ${'arr_locate_b'.$prov_id};
		    if (!empty($city_list) && is_array($city_list) ) 
		    {
		      $city_list = change_assoc_arr($city_list);
		      foreach ($city_list as $c_key => $vo) 
		      {
		       if ($vo['c_id'] == $topic_info['location_id']) 
		       {
		          $city_list[$c_key]['selected_city'] = "selected='true'";
		       }
		     }
		    }  		 
		}
		
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
			
			if(!$content)
			{
				js_pop_msg("内容不能为空");
				exit;
			}
			
			if(!$id)
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
            $update_data['version_type']     = $version_type;
			$topic_obj->update_topic($update_data, $id);
			echo "<script>alert('更新成功');parent.location.href='topic_list.php';</script>";
		}
	break;
}

/////////////////
$global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
$tpl->assign ( 'global_header_html', $global_header_html );
/////////////////
$tpl->assign('province_list', $province_list);
$tpl->assign('city_list', $city_list);
$tpl->assign($topic_info);
$tpl->assign('act', $act);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->output();

?>