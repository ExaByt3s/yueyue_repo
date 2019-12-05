<?php

/**
 * 微信专题活动
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-12 11:47:30
 * @version 1
 */
function _ctlact_topic_style($attribs)
{
	global $tpl;
	global $my_app_pai;
	global $yue_login_id;
	//global $id;
	include_once ("/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php");
	$cms_system_obj = POCO::singleton ( 'cms_system_class' );
	$model_style_v2 = POCO::singleton ( 'pai_model_style_v2_class' );
	$model_card_obj = POCO::singleton ( 'pai_model_card_class' );
	$pic_obj = POCO::singleton ( 'pai_pic_class' );
	$cameraman_card_obj = POCO::singleton ( 'pai_cameraman_card_class' );
	
	if (! is_array ( $attribs ))
	{
		return false;
	}
	//print_r($ret);exit;
	//表示关联表单
	if ($attribs ['content_type'] == 'rank')
	{
		//获取榜单数据
		$total_count = $cms_system_obj->get_record_list_by_issue_id ( true, $attribs ['issue_id'] );
		$list = $cms_system_obj->get_record_list_by_issue_id ( false, $attribs ['issue_id'], "0,{$total_count}" );
		//选择单风格
		if ($attribs ['tpl_type'] == 'cover')
		{
			$topic_tpl = $my_app_pai->getView ( '/disk/data/htdocs232/poco/pai/webcontrols/act_topic_style_1.htm', true );
		}
		//多风格
		elseif ($attribs ['tpl_type'] == 'custom')
		{
			$topic_tpl = $my_app_pai->getView ( '/disk/data/htdocs232/poco/pai/webcontrols/act_topic_style_2.htm', true );
		}
		if (is_array ( $list ))
		{
			foreach ( $list as $key => $vo )
			{
				if ($vo ['role'] == 'model')
				{
					if ($vo ['content'])
					{
						$content_arr = explode ( '|', $vo ['content'] );
						$list [$key] ['price'] = $content_arr [0];
						$list [$key] ['alter_price'] = $content_arr [1];
					}
					else
					{
						$ret = $model_style_v2->get_model_style_by_user_id ( $vo ['user_id'] );
						$list [$key] ['price'] = $ret [0] ['price'] . '元';
					}
					
					$model_info = $model_card_obj->get_model_card_info ( $vo ['user_id'] );
					$cover_img =  $model_info ['cover_img'];
					
				}
				else
				{
					$cameraman_info = $cameraman_card_obj->get_cameraman_card_info ( $user_id );
					$cover_img =  $cameraman_info ['cover_img'];
					
				}
				
				if (empty ( $cover_img ))
				{
					$pic_array = $pic_obj->get_user_pic ( $vo ['user_id'], $limit = '0,10' );
					foreach ( $pic_array as $a => $b )
					{
						
						$num = explode ( '?', $b ['img'] );
						$num = explode ( 'x', $num [1] );
						$num_v2 = explode ( '_', $num [1] );
						
						$width = $num [0];
						$height = $num_v2 [0];
						
						if ($width < $height)
						{
							$cover_img = $b ['img'];
							
							break;
						}
					}
				}
				
				if(empty($cover_img))
				{
					$cover_img = $pic_array[0]['img'];
				}
				
				$list [$key] ['img_url'] = yueyue_resize_act_img_url ( $cover_img, '320' );
			
			}
			# code...
		}
		//print_r($list);exit;
		$topic_tpl->assign ( $ret );
		$topic_tpl->assign ( 'list', $list );
		$topic_html = $topic_tpl->result ();
		unset ( $ret );
		unset ( $list );
		return $topic_html;
	}

}
?>