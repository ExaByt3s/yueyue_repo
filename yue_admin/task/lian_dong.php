<?php
include_once 'common.inc.php';
$task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
$task_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');

if($_POST)
{
    dump($_POST);
    
    foreach($_POST['detail'] as $k => $v)
    {
		if($v)
		{
			$parent_one = $task_goods_type_attribute_obj->get_property_info($k);
			$son_one = $task_goods_type_attribute_obj->get_property_info($v);
			if($v == 0)
			{
				$son_one['name'] = 'ว๋ักิ๑';
			}
			$compare_data[] = array(
			                        'info'=>"{$parent_one['name']}:{$son_one['name']}",
									'type_id'=>$k . "|" . $v,
									'type_md5'=>md5(md5($k)."|".md5($v)),
									);
		}
    }
}

$json_post_data = json_encode($_POST);
$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."lian_dong.tpl.htm" );
$data = $task_goods_type_obj->get_first_cate();
$json_data = json_encode($data);
$tpl->assign('post', $json_post_data );
$tpl->assign('compare_data',$compare_data);
$tpl->assign('json_data',$json_data);
$tpl->output ();
?>