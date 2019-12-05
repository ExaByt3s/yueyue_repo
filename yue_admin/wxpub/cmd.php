<?php
	include_once('./common.inc.php');
	$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
	$bind_id = $_COOKIE['cur_bind_id'];
	$act     = $_INPUT['act'];
	if( !in_array($act,array('list','modify','modify_act','add','add_act','del')) )
		$act = 'list';

	$cmd_type_list_arr  = array( 
		
		array('cmd_type_title'=>'完全匹配','cmd_type'=>'keywords'),
		array('cmd_type_title'=>'模糊匹配','cmd_type'=>'keywords_contain')

	); 
	$exec_type_list_arr = array(

		array('exec_type_title'=>'文本','exec_type'=>'text'),
		array('exec_type_title'=>'客服','exec_type'=>'transfer_customer_service'),
		array('exec_type_title'=>'图文','exec_type'=>'news'),
		array('exec_type_title'=>'图片','exec_type'=>'image'),
		array('exec_type_title'=>'认证','exec_type'=>'yue_credit2_step1')

	);
	switch ($act) {
		case 'list':
			$search_arr['cmd_val'] = $_INPUT['cmd_val'];
			$tpl 		 = $my_app_pai->getView('cmd_list.tpl.htm');
			$page_obj    = POCO::singleton('show_page');
			$header_html = $my_app_pai->webControl('WxpubAdminPageHeader',$header_arr,true);
			$where_str   = "(cmd_type='keywords' OR cmd_type='keywords_contain')";
		    if( $search_arr['cmd_val']!=='' ){

		        $where_str .= " AND cmd_val like '%{$search_arr['cmd_val']}%'";
		    
		    }
		    $total_count   = $weixin_helper_obj->get_cmd_list($bind_id,true,$where_str);
			$show_count    = 20; 
			$page_obj->setvar($search_arr);
			$page_obj->set($show_count, $total_count);
			$limit_str     = $page_obj->limit();
			$page_select   = $page_obj->output(true);
			$cmd_list 	   = $weixin_helper_obj->get_cmd_list($bind_id,false,$where_str,'cmd_id DESC',$limit_str);
			$cmd_type_arr  = array( 'keywords'=>'完全匹配','keywords_contain'=>'模糊匹配' ); 
			$exec_type_arr = array( 'text'=>'文本','transfer_customer_service'=>'客服','news'=>'图文','yue_credit2_step1'=>'认证', 'image'=>'图片',);
			foreach ($cmd_list as $k => $v) {
				
				$cmd_list[$k]['cmd_type_desc']  = $cmd_type_arr[$v['cmd_type']];
				$cmd_list[$k]['short_cmd_val']  = poco_cutstr($v['cmd_val'],38,'.....');
				$cmd_list[$k]['exec_type_desc'] = $exec_type_arr[$v['exec_type']];

			}
			$tpl->assign($search_arr);
			$tpl->assign('cmd_list', $cmd_list);
			$tpl->assign('page_select', $page_select);
			$tpl->output();
			break;
		case 'add':
			$tpl = $my_app_pai->getView('cmd_keywords_edit.tpl.htm');
			$header_html = $my_app_pai->webControl('WxpubAdminPageHeader',$header_arr,true);
			$tpl->assign('cmd_type_list_arr',$cmd_type_list_arr);
			$tpl->assign('exec_type_list_arr',$exec_type_list_arr);
			$tpl->assign('act','add_act');
			$tpl->output();
			break;
		case 'add_act':
			$add_data['bind_id']    = $bind_id;
			$add_data['cmd_val']    = $_INPUT['cmd_val'];
			$add_data['cmd_type']   = $_INPUT['cmd_type'];
			$add_data['exec_type']  = $_INPUT['exec_type'];
			$add_data['exec_val']   = $_INPUT['exec_val'];
			$add_data['remark']     = $_INPUT['remark'];
			$cmd_id 				= $weixin_helper_obj->add_cmd($add_data);
			if( $cmd_id > 0 ){

				pop_msg('添加成功');	

			}
			else{

				pop_msg('添加失败，请重试。');

			}
			break;
		case 'modify':
			$cmd_type = $_INPUT['cmd_type'];
			switch ($cmd_type) {
				case 'subscribe':
					$tpl 	  = $my_app_pai->getView('cmd_subscribe_edit.tpl.htm');
					$cmd_info = $weixin_helper_obj->get_cmd_info_by_type($cmd_type,$bind_id);
					break;
				case 'default':
					$tpl 	  = $my_app_pai->getView('cmd_default_edit.tpl.htm');
					$cmd_info = $weixin_helper_obj->get_cmd_info_by_type($cmd_type,$bind_id);
					break;
				case 'keywords':
				case 'keywords_contain':
					$tpl 	  = $my_app_pai->getView('cmd_keywords_edit.tpl.htm');
					$cmd_id   = $_INPUT['cmd_id'];
					$cmd_info = $weixin_helper_obj->get_cmd_info($cmd_id);	
					$tpl->assign('act','modify_act');
					$tpl->assign('cmd_type_list_arr',$cmd_type_list_arr);
					$tpl->assign('exec_type_list_arr',$exec_type_list_arr);			
				default:
					break;
			}
			$header_html = $my_app_pai->webControl('WxpubAdminPageHeader',$header_arr,true);
			$tpl->assign($cmd_info);
			$tpl->output();			
			break;
		case 'modify_act':
			$cmd_type = $_INPUT['cmd_type'];
			switch ($cmd_type) {
				case 'subscribe':
				case 'default':
					$cmd_info    = $weixin_helper_obj->get_cmd_info_by_type($cmd_type,$bind_id);
					if( empty($cmd_info) ){

						$insert_data = array(

							'bind_id'   =>$bind_id,
							'exec_type' =>$_INPUT['exec_type'],
							'exec_val'  =>trim($_POST['exec_val']),
							'cmd_type'  =>$_INPUT['cmd_type']

						); 
						$ret = $weixin_helper_obj->add_cmd($insert_data);

					}
					else{

						$modify_data = array(

							'exec_val'=>trim($_POST['exec_val'])

						); 
						$ret = $weixin_helper_obj->modify_cmd($modify_data,$cmd_info['cmd_id']);

					}
					pop_msg('修改成功');	
					break;
				case 'keywords':
				case 'keywords_contain':
					$cmd_id 				   = $_INPUT['cmd_id'];
					$update_data['cmd_val']    = $_INPUT['cmd_val'];
					$update_data['cmd_type']   = $_INPUT['cmd_type'];
					$update_data['exec_type']  = $_INPUT['exec_type'];
					$update_data['exec_val']   = trim($_POST['exec_val']);
					$update_data['remark']     = $_INPUT['remark'];
					$ret = $weixin_helper_obj->modify_cmd($update_data,$cmd_id);
					pop_msg('修改成功');

				default:
					break;
			}			
			break;
		case 'del':
			$cmd_id = $_INPUT['cmd_id'];
			$ret 	 = $weixin_helper_obj->del_cmd($cmd_id);
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
