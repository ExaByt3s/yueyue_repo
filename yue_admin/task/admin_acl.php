<?php
include_once 'common.inc.php';

$task_admin_acl_obj = POCO::singleton('pai_mall_admin_acl_class');

switch($action)
{
    case 'ajax_do':
        //转下码
        $_POST['name'] = iconv('utf-8','gbk',$_POST['name']);
        $rs = $task_admin_acl_obj->do_save($_POST);
        if($rs)
        {
            echo json_encode(array("status"=>1));
            exit;
        }
        exit;
    break;
	case "save":
        if($_POST)
        {
            $rs = $task_admin_acl_obj->do_save($_POST);
            if($rs)
            {
                js_pop_msg('保存成功', true);
            }else
            {
                js_pop_msg('保存失败', true);
            }
        }
        
        $id = $_GET['id'] ? (int)$_GET['id'] : '';
        $acl_one = array();
        if($id)
        {
            $acl_one = $task_admin_acl_obj->get_admin_acl_info($id);
        }
        
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."admin_acl_save.tpl.htm" );
        
        $cate_list = $task_admin_acl_obj->get_acl_cate();
        
        
        foreach($cate_list as $k => &$v)
        {
            if( ! empty($acl_one) )
            {
                //作上一级回选用
                if($v['id']==$acl_one['parent_id'])
                {
                    $v['select_one'] = 1;
                }
                
            }
            $v['level'] = str_repeat("&nbsp;", $v['level']*8);
        }
        
        $tpl->assign( 'acl_one' , $acl_one );
        $tpl->assign( 'id' , $id);
        $tpl->assign( 'list' , $cate_list);
	break;
	case "del":
        $id = (int)$_GET['id'];
        $rs = $task_admin_acl_obj->del_one($id);
        if($rs)
        {
            js_pop_msg('删除成功',false,"admin_acl.php");
        }else
        {
            js_pop_msg('删除失败',false,"admin_acl.php");
        }
    break;
	default://列表
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."admin_acl_list.tpl.htm" );
        //取无限级分类
        $list = $task_admin_acl_obj->get_acl_cate();
        if( ! empty($list))
        {
            foreach($list as &$v)
            {
                $v['shuo_jing'] = str_repeat("&nbsp;", $v['level']*8);
            }
        }
        $tpl->assign ( 'list', $list );
	break;
}
$tpl->output ();
?>