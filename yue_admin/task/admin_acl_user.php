<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
//require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/Excel_v2.class.php');
include_once 'common.inc.php';

$task_admin_acl_user_obj = POCO::singleton('pai_mall_admin_user_class');
$task_admin_acl_obj = POCO::singleton('pai_mall_admin_acl_class');

switch($action)
{
    case 'save': //编辑权限
        
        if($_POST)
        {
            $_POST['acl'] = implode(",",$_POST['acl']);
            $rs = $task_admin_acl_user_obj->do_save($_POST);
            if($rs)
            {
               js_pop_msg('操作成功',false,"admin_acl_user.php");
            }else
            {
               js_pop_msg('操作失败',false,"admin_acl_user.php");
            }
        }
        $id = (int)$_INPUT['id'];
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."admin_acl_user_save.tpl.htm" );
        
        $user_info = $task_admin_acl_user_obj->get_one($id);
        
        //取无限级分类
        $acl_list = array();
        $acl_list = $task_admin_acl_obj->get_acl_cate();
        if( ! empty($acl_list) )
        {
            foreach($acl_list as $k => &$v)
            {
                 $v['shuo_jing'] = str_repeat("&nbsp;",$v['level']*8);
                 if($user_info)
                 {
                     $acl_ary = explode(",",$user_info['acl']);
                     if( ! empty($acl_ary) )
                     {
                        if(in_array($v['id'],$acl_ary))
                        {
                            $v['is_selected'] = 1;
                        }
                     }   
                     
                 }    
            }
        }
        
        $tpl->assign('user_info',$user_info);
        $tpl->assign('acl_list',$acl_list);
    break;
    case 'del':
        $id = (int)$_INPUT['id'];
        if( ! $id )
        {
            js_pop_msg('id不能为空',false,"admin_acl_user.php");
        }
        $rs = $task_admin_acl_user_obj->del_one($id);
        if($rs)
        {
            js_pop_msg('删除成功',false,"admin_acl_user.php");
        }else
        {
            js_pop_msg('删除失败',false,"admin_acl_user.php");
        }
    break;
    //列表页
    default://商家申请列表
        $status = isset($_INPUT['status']) && $_INPUT['status']!=='' ? (int)$_INPUT ['status'] : '';
        $keyword = $_INPUT['keyword'];
        $acl = $_INPUT['acl'];
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."admin_acl_user.tpl.htm" );
        
        //where 条件
		$where = 1;
        
        if($acl)
        {
            $where .= " and FIND_IN_SET({$acl},acl)";
        }
        
        if( $keyword )
        {
            $where .= " and user_id = "."'".(int)$keyword."'"." ";
        }
        
        if( $status !=='')
        {
            $where .= " AND status='{$status}'";
        }
        
		$total_count = $task_admin_acl_user_obj->get_admin_list(true, $where);		
		$page_obj = new show_page ();
		$show_count = 20;
		$page_obj->setvar ( array (
            "status" => $status,
            'keyword'=>$keyword,
            'acl'=>$acl,
        ) );
		$page_obj->set ( $show_count, $total_count );		
        
		$list = $task_admin_acl_user_obj->get_admin_list(false, $where, "id desc", $page_obj->limit());
        
		$add_time_color = '';
		
		foreach($list as $key => &$val)
		{
            if($val['status'] == 0)
            {
                $val['status_name'] = '无效';
            }else if($val['status'] == 1)
            {
                $val['status_name'] = '有效';
            }
            
            $val['user_name'] = get_user_nickname_by_user_id($val['user_id']);
            
            $id_ary = explode(",",$val['acl']);
            if( ! empty($id_ary) )
            {
                $can_do_list = '';
                $can_do_ary = array();
                foreach($id_ary as $ik => $iv)
                {
                    $acl_info = $task_admin_acl_obj->get_admin_acl_info($iv);
                    if( ! empty($acl_info) )
                    {
                       $can_do_ary[] = $acl_info['name'];
                    }
                    
                }
            }
            
            $val['can_do_list'] = implode(',',$can_do_ary);
        }
        //取无限级分类
        $acl_list = array();
        $acl_list = $task_admin_acl_obj->get_acl_cate();
        if( ! empty($acl_list) )
        {
            foreach($acl_list as $k => &$v)
            {
                 $v['shuo_jing'] = str_repeat("&nbsp;",$v['level']*8);
                 if($acl)
                 {
                    if($acl == $v['id'])
                    {
                        $v['is_selected'] = 1;
                    }
                 }   
            }
            
        }
        
        $tpl->assign('keyword',$keyword);
        $tpl->assign('status',$status);
        $tpl->assign ( 'page', $page_obj->output ( 1 ) );
        $tpl->assign('acl_list',$acl_list);
		$tpl->assign ( 'list', $list );
	break;
}
$tpl->output ();
?>