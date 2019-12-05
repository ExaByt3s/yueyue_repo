<?php
	/**
	 * 菜单列表
	 * 
	 * @author Hai
	 * @copyright 2015-01-19
	 */	

	include_once('./common.inc.php');
	$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
	$act       = $_INPUT['act'];
	$bind_id   = $_COOKIE['cur_bind_id'];
	if( !in_array($act,array('list','set_sort','wx_menu_create','add','add_act','modify','modify_act','del')) )
		$act = 'list';
	$exec_type_list_arr = array(

		array('exec_type_title'=>'文本','exec_type'=>'text'),
		array('exec_type_title'=>'客服','exec_type'=>'transfer_customer_service'),
		array('exec_type_title'=>'图文','exec_type'=>'news'),
		array('exec_type_title'=>'认证','exec_type'=>'yue_credit2_step1')

	);	
	$menu_type_list_arr = array( 
		array( 'menu_type_title'=>'常规','menu_type'=>'normal'),
		array( 'menu_type_title'=>'链接','menu_type'=>'view'), 
		array( 'menu_type_title'=>'推送','menu_type'=>'click') 
	);
	switch ($act) {
		case 'list':
		$menu_list 	   = $weixin_helper_obj->get_menu_tree($bind_id);
		$menu_type_arr = array('normal'=>'常规','view'=>'链接','click'=>'推送'); 
		$tpl 		   = $my_app_pai->getView('menu_list.tpl.htm');
		$header_html = $my_app_pai->webControl('WxpubAdminPageHeader',$header_arr,true);
		foreach ($menu_list as $k => $v) {
			$menu_list[$k]['menu_type_name'] = $menu_type_arr[$v['menu_type']];
			foreach ($v['subclass'] as $k1 => $v1) {
				
				$menu_list[$k]['subclass'][$k1]['menu_type_name'] = $menu_type_arr[$v1['menu_type']];

			}

		}
		$tpl->assign('menu_list', $menu_list);
		$tpl->output();			
			break;
		case 'set_sort':
			$menu_id     = $_INPUT['menu_id'];
			$affect_rows = 0;
			foreach ( $menu_id as $k => $f_menu_id ) {
				
				$sort = $_INPUT['sort_'.$f_menu_id];
				$data = array('sort'=>$sort);
				$ret  = $weixin_helper_obj->update_menu($data,$f_menu_id);
				$ret&&$affect_rows++;

			}
			pop_msg('排序修改成功');
			break;
		case 'wx_menu_create':
			
			$ret  = $weixin_helper_obj->wx_menu_create($bind_id);
			dump($ret);
			if( $ret['errcode'] === 0 ){

				$weixin_helper_obj->menu_sync_cmd($bind_id);
				pop_msg('同步微信菜单成功');	

			}
			else{

				pop_msg('同步失败  请重试');

			}				
			break;

		case 'add':
			$tpl 		    = $my_app_pai->getView('menu_edit.tpl.htm');
			$where_str 		= " parent_id=0";
			$top_menu_list  = $weixin_helper_obj->get_menu_list($bind_id,false,$where_str,'sort ASC,menu_id ASC');
			foreach ($top_menu_list as $k => $v) {
				
				if( $v['menu_type'] != 'normal' )
					$top_menu_list[$k]['attr_disable'] = 'disabled';
				else
					$top_menu_list[$k]['attr_disable'] = '';

			}
			$tpl->assign('act','add_act');
			$tpl->assign('exec_type_list_arr',$exec_type_list_arr);
			$tpl->assign('menu_type_list_arr', $menu_type_list_arr);		
			$tpl->assign('top_menu_list', $top_menu_list);
			$header_html = $my_app_pai->webControl('WxpubAdminPageHeader',$header_arr,true);
			$tpl->output();	
			break;
		case 'add_act':
			$add_data['bind_id']     = $bind_id;
			$add_data['parent_id']   = $_INPUT['parent_id'];
			$add_data['menu_name']   = $_INPUT['menu_name'];
			$add_data['menu_type']   = $_INPUT['menu_type'];
			$add_data['menu_key']    = $_INPUT['menu_key'];

			switch ($add_data['menu_type']) {
				case 'normal':
					$add_data['menu_url']    = '';
					$add_data['menu_key']    = '';
					$add_data['exec_type']   = '';
					$add_data['exec_val']    = '';			
					break;
				case 'view':
					$add_data['menu_url']    = $_INPUT['menu_url'];
					$add_data['menu_key']    = '';
					$add_data['exec_type']   = '';
					$add_data['exec_val']    = '';						
					break;
				case 'click':
					$add_data['menu_url']    = '';
					$add_data['menu_key']    = $_INPUT['menu_key'];
					$add_data['exec_type']   = $_INPUT['exec_type'];
					$add_data['exec_val']    = $_INPUT['exec_val'];	
					break;									
				default:
					break;
			}

			$ret = $weixin_helper_obj->add_menu($add_data);
			if( $ret>0 ){

				pop_msg('添加菜单成功');	

			}
			else{

				pop_msg('添加失败  请重试');

			}	
			break;
		case 'modify':
			$tpl       = $my_app_pai->getView('menu_edit.tpl.htm');
			$menu_id   = $_INPUT['menu_id'];
			$menu_info = $weixin_helper_obj->get_menu_info($menu_id);
			$tpl->assign($menu_info);
			$where_str 		= " parent_id=0";
			$top_menu_list  = $weixin_helper_obj->get_menu_list($bind_id,false,$where_str,'sort ASC,menu_id ASC');
			$tpl->assign('act','modify_act');
			$tpl->assign('exec_type_list_arr',$exec_type_list_arr);
			$tpl->assign('menu_type_list_arr', $menu_type_list_arr);		
			$tpl->assign('top_menu_list', $top_menu_list);
			$header_html = $my_app_pai->webControl('WxpubAdminPageHeader',$header_arr,true);
			$tpl->assign('menu_id',$menu_id);
			$tpl->output();				
			break;
		case 'modify_act':
			$menu_id   = $_INPUT['menu_id'];
			$modify_data['parent_id']   = $_INPUT['parent_id'];
			$modify_data['menu_name']   = $_INPUT['menu_name'];
			$modify_data['menu_type']   = $_INPUT['menu_type'];

			switch ($modify_data['menu_type']) {
				case 'normal':
					$modify_data['menu_url']    = '';
					$modify_data['menu_key']    = '';	
					$modify_data['exec_type']   = '';
					$modify_data['exec_val']    = '';			
					break;
				case 'view':
					$modify_data['menu_url']    = $_INPUT['menu_url'];
					$modify_data['menu_key']    = '';	
					$modify_data['exec_type']   = '';
					$modify_data['exec_val']    = '';						
					break;
				case 'click':
					$modify_data['menu_url']    = '';
					$modify_data['menu_key']    = $_INPUT['menu_key'];	
					$modify_data['exec_type']   = $_INPUT['exec_type'];
					$modify_data['exec_val']    = $_INPUT['exec_val'];
					break;									
				default:
					break;
			}
			$ret = $weixin_helper_obj->update_menu($modify_data,$menu_id);

			if( $ret>0 ){

				pop_msg('更新成功');	

			}
			else{

				pop_msg('更新失败  请重试');

			}	
			break;
		case 'del':
			$menu_id  = $_INPUT['menu_id'];
			$ret 	  = $weixin_helper_obj->del_menu($menu_id);
			if( $ret>0 ){

				pop_msg('删除成功',$_SERVER["HTTP_REFERER"]);	

			}
			else{

				pop_msg('删除失败  请重试',$_SERVER["HTTP_REFERER"]);

			}	
			break;
		default:
			break;
	}


?>