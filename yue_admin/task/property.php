<?php
include_once 'common.inc.php';
$task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
$task_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');

//加载配置文件
$status_name = pai_mall_load_config('goods_type_attribute_status');

$type_name = pai_mall_load_config('goods_type_attribute_type');
foreach($type_name as $k => $v)
{
    $type_name_arys[] = array('key'=>$k,'val'=>$v);
}

switch($action)
{
    case "ajax_do":
            $_POST['name'] = iconv('utf-8','gbk', $_POST['name']);
            $_POST['user_id'] = $yue_login_id;
            $rs = $task_goods_type_attribute_obj->do_save($_POST);
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
            $rs = $task_goods_type_attribute_obj->do_save($_POST);
            if($rs)
            {
                exit('<script>alert("保存成功");window.open("property.php?goods_type_id='.$_POST['goods_type_id'].'","_self")</script>');
            }else
            {
                exit('<script>alert("保存失败");window.open("property.php?goods_type_id='.$_POST['goods_type_id'].'","_self")</script>');
            }
        }
        $parents_id = $_GET['parents_id'] ? (int)$_GET['parents_id'] : '0';
        $goods_type_id = $_GET['goods_type_id'] ? (int)$_GET['goods_type_id'] : '';
        $id = $_GET['id'] ? (int)$_GET['id'] : '';
        $property_one = array();
        if($id)
        {
            $property_one = $task_goods_type_attribute_obj->get_property_info($id);
            if( ! empty($property_one) )
            {
                foreach($type_name_arys as &$v)
                {
                    if( $property_one['input_type_id'] == 1 || $property_one['input_type_id'] == 2 )
                    {
                        if($v['key'] != 1 && $v['key']!=2)
                        {
                            $v['disable'] = true;
                        }
                    }
                    if( $property_one['input_type_id'] > 2)
                    {
                        if( $v['key']<3 )
                        {
                            $v['disable'] = true;
                        }
                    }
                    if($v['key']==$property_one['input_type_id'])
                    {
                        $v['select_one'] = 1;
                    }
                }
                $one_property_list = $task_goods_type_attribute_obj->get_type_attribute_cate($property_one['id']);
                foreach($one_property_list as &$v)
                {
                    $one_property_list_key[$v['id']] = $v;
                }
            }
        }
        
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."property_save.tpl.htm" );
        
        $type_list = $task_goods_type_obj->get_type_cate();
        foreach($type_list as $k => &$v)
        {
            $v['level'] = str_repeat("&nbsp;", $v['level']*8);
            
            if( ! empty($property_one) )
            {
                //作类型id回选用
                if($v['id']==$property_one['goods_type_id'])
                {
                    $v['select_one'] = 1;
                }
            }
        }
        
        $tpl->assign( 'type_name' , $type_name_arys );
        $tpl->assign( 'type_list' , $type_list );
        $tpl->assign( 'property_one' , $property_one );
        $tpl->assign('goods_type_id',$goods_type_id);
        $tpl->assign('parents_id',$parents_id);
        $tpl->assign( 'id' , $id );
    break;
	case "del":
        $id = (int)$_GET['id'];
        $goods_type_id = (int)$_GET['goods_type_id'];
        $rs = $task_goods_type_attribute_obj->del_one($id);
        if($rs)
        {
           echo "<script>alert('删除成功');window.open('property.php?goods_type_id={$goods_type_id}','_self')</script>";
           exit;
        }else
        {
           echo "<script>alert('删除失败');window.open('property.php?goods_type_id={$goods_type_id}','_self')</script>";
           exit;
        }
    break;
	default://列表
        
        $tpl = new SmartTemplate (TASK_TEMPLATES_ROOT."property_list.tpl.htm" );
        //类型id
        $goods_type_id = $_GET['goods_type_id'] ? (int)$_GET['goods_type_id'] :'';
        $type_info = array();
        if( $goods_type_id !='' )
        {
           $type_info = $task_goods_type_obj->get_type_info($goods_type_id); 
           $list = $task_goods_type_attribute_obj->get_type_attribute_cate(0,0,array(),$goods_type_id);
        }
        
        if( ! empty($list))
        {
            foreach($list as &$v)
            {
                $v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $v['shuo_jing'] = str_repeat("&nbsp;", $v['level']*8);
                $v['status'] = $status_name[$v['status']];
                //顶级的是单选或者多选才能添加
                if( ( $v['input_type_id'] == 1 || $v['input_type_id'] == 2 ) )
                {
                    $v['can_add'] = true;
                }
                $v['input_type_id'] = $type_name[$v['input_type_id']];
            }
        }
        $tpl->assign('type_info',$type_info);
        $tpl->assign('goods_type_id',$goods_type_id);
        $tpl->assign ( 'list', $list );
	break;
}
$tpl->output ();
?>