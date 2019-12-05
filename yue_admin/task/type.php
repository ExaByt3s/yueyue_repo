<?php
include_once 'common.inc.php';
$task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');

$status_name = pai_mall_load_config('goods_type_attribute_status');

$stock_type_name = pai_mall_load_config('stock_type');
foreach($stock_type_name as $k => $v)
{
    $stock_type_name_arys[] = array('key'=>$k,'val'=>$v);
}

switch($action)
{
    case 'ajax_do':
        //转下码
        $_POST['name'] = iconv('utf-8','gbk',$_POST['name']);
        //去除最后一个逗号
        $_POST['stock_type'] = substr($_POST['stock_type'],0,-1);
        $_POST['user_id'] = $yue_login_id;
        $rs = $task_goods_type_obj->do_save($_POST);
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
            $_POST['user_id'] = $yue_login_id;
            $rs = $task_goods_type_obj->do_save($_POST);
            if($rs)
            {
                exit('<script>alert("保存成功");window.open("type.php","_self");</script>');
                
            }else
            {
                exit('<script>alert("保存失败");window.open("type.php","_self");</script>');
            }
        }
        
        $id = $_GET['id'] ? (int)$_GET['id'] : '';
        $type_one = array();
        if($id)
        {
            $type_one = $task_goods_type_obj->get_type_info($id);
        }
        
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."type_save.tpl.htm" );
        
        $cate_list = $task_goods_type_obj->get_type_cate();
        
        if( ! empty($type_one) )
        {
            //拿分类纪录
            $one_cate_list = $task_goods_type_obj->get_type_cate($type_one['id']);
            
            foreach($one_cate_list as $k => $v)
            {
                $one_cate_list_key[$v['id']] = $v;
            }
        }
        
        foreach($cate_list as $k => &$v)
        {
            
            if( ! empty($type_one) )
            {
                //作上一级回选用
                if($v['id']==$type_one['parents_id'])
                {
                    $v['select_one'] = 1;
                }
                //自己的下级不能选
                if( ! empty($one_cate_list_key[$v['id']]))
                {
                    $v['no_use'] = true;
                }
            }
            $v['level'] = str_repeat("&nbsp;", $v['level']*8);
        }
        
        foreach($stock_type_name_arys as &$v)
        {
            if( ! empty($type_one) )
            {
                //作回选用
                $str_all = (string)$type_one['stock_type'];
                $str_find = (string)$v['key'];
                $rs = strpos($str_all,$str_find);
                if( $rs > -1 )
                {
                    $v['select_one'] = 1;
                }
            }
        }
        $tpl->assign( 'type_one' , $type_one );
        $tpl->assign( 'id' , $id);
        $tpl->assign('stock_type_name', $stock_type_name_arys );
        $tpl->assign( 'list' , $cate_list);
	break;
	case "del":
        $id = (int)$_GET['id'];
        $rs = $task_goods_type_obj->del_one($id);
        if($rs)
        {
            exit("<script>alert('删除成功');window.open('type.php','_self')</script>");
        }else
        {
            exit("<script>alert('删除失败');window.open('type.php','_self')</script>");
        }
    break;
	default://列表
        
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."type_list.tpl.htm" );
        //取无限级分类
        $list = $task_goods_type_obj->get_type_cate();
        if( ! empty($list))
        {
            foreach($list as &$v)
            {
                $v['status'] = $status_name[$v['status']];
                $stock_arys = explode(",",$v['stock_type']);
                $stock_type_str = "";
                foreach($stock_arys as $t)
                {
                    $stock_type_str .= $stock_type_name[$t].",";
                }
                $v['stock_type'] = substr($stock_type_str,0,-1);
                $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $v['shuo_jing'] = str_repeat("&nbsp;", $v['level']*8);
            }
        }
        $tpl->assign ( 'list', $list );
	break;
}
$tpl->output ();
?>