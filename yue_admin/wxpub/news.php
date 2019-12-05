<?php
	include_once('./common.inc.php');
	$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
	$bind_id = $_COOKIE['cur_bind_id'];
	$act     = $_INPUT['act'];
	if( !in_array($act,array('list','add','add_act','modify','modify_act','del')) )
		$act = 'list';
	switch ($act) {
		case 'list':
			$search_arr['title'] 		= $_INPUT['title'];
			$search_arr['min_add_time'] = strtotime($_INPUT['min_add_time']);
			$search_arr['max_add_time'] = strtotime($_INPUT['max_add_time']);
			
			$tpl 		 = $my_app_pai->getView('news_list.tpl.htm');
			$page_obj    = POCO::singleton('show_page');
			$header_html = $my_app_pai->webControl('WxpubAdminPageHeader',$header_arr,true);
			$where_str   = "1";
		    if( $search_arr['title']!=='' ){

		        $where_str .= " AND title like '%{$search_arr['title']}%'";
		    
		    }
		    if( !empty($search_arr['min_add_time']) ){

		         $where_str .= " AND add_time>={$search_arr['min_add_time']}";

		    }
		    if( !empty($search_arr['max_add_time']) ){

		         $max_add_time = $search_arr['max_add_time'] + 86400;
		         $where_str   .= " AND add_time<{$max_add_time}";
		    
		    }
		    $total_count   = $weixin_helper_obj->get_news_list($bind_id,true,$where_str);
			$show_count    = 20; 
			$page_obj->setvar($search_arr);
			$page_obj->set($show_count, $total_count);
			$limit_str     = $page_obj->limit();
			$page_select   = $page_obj->output(true);
			$news_list 	   = $weixin_helper_obj->get_news_list($bind_id,false,$where_str,'news_id DESC',$limit_str);
			foreach ($news_list as $k => $v) {
				
				$news_list[$k]['add_time'] 			= date('Y-m-d H:i:s',$v['add_time']);
				$news_list[$k]['short_description'] = poco_cutstr($v['description'],20,'.....');

			}
			$tpl->assign($search_arr);
			$tpl->assign('news_list', $news_list);
			$tpl->assign('page_select', $page_select);
			$tpl->output();
			break;
		case 'add':
			$tpl = $my_app_pai->getView('news_edit.tpl.htm');
			$header_html = $my_app_pai->webControl('WxpubAdminPageHeader',$header_arr,true);
			$tpl->assign('act','add_act');
			$tpl->output();
			break;
		case 'add_act':
			$add_data['bind_id']     = $bind_id;
			$add_data['title']       = $_INPUT['title'];
			$add_data['description'] = $_INPUT['description'];
			$add_data['pic_url']     = $_INPUT['pic_url'];
			$add_data['url']         = $_INPUT['url'];
			$add_data['content']     = $_INPUT['content'];
			$news_id 				 = $weixin_helper_obj->add_news($add_data);
			if( $news_id > 0 ){

				pop_msg('添加成功');	

			}
			else{

				pop_msg('添加失败，请重试。');

			}
			break;				
		case 'modify':
			$tpl 	 	 = $my_app_pai->getView('news_edit.tpl.htm');
			$news_id 	 = $_INPUT['news_id'];
			$header_html = $my_app_pai->webControl('WxpubAdminPageHeader',$header_arr,true);
			$news_info   = $weixin_helper_obj->get_news_info($news_id);
			$tpl->assign('act','modify_act');
			$tpl->assign($news_info);
			$tpl->output();		
			break;
		case 'modify_act':
			//修改的时候 bind_id不能改
			$news_id 					= $_INPUT['news_id'];
			$modify_data['title']       = $_INPUT['title'];
			$modify_data['description'] = $_INPUT['description'];
			$modify_data['pic_url']     = $_INPUT['pic_url'];
			$modify_data['url']         = $_INPUT['url'];
			$modify_data['content']     = $_INPUT['content'];
			$ret = $weixin_helper_obj->modify_news($modify_data,$news_id);
			pop_msg('修改成功');
			break;				
		case 'del':
			$news_id = $_INPUT['news_id'];
			$ret 	 = $weixin_helper_obj->del_news($news_id);
			if( $ret > 0 ){

				pop_msg('删除成功',$_SERVER["HTTP_REFERER"]);	

			}
			else{

				pop_msg('删除失败，请重试。',$_SERVER["HTTP_REFERER"]);

			}			
			break;				
		default:
			break;
	}

?>