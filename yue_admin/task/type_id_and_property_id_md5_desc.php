<?php
include_once 'common.inc.php';

$goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
$goods_property_obj = POCO::singleton('pai_mall_goods_type_attribute_class');

$type_list = $goods_type_obj->get_type_cate(2,'all');
//dump($type_list);
foreach($type_list as $k => $v)
{
    echo 'type_id'.':'.$v['id'].', '.' md5:'.md5($v['id']).", name:".$v['name']."</br>";
    $property_list = $goods_property_obj->get_type_attribute_cate(0,0,array(),$v['id']);
    //dump($property_list);
    foreach($property_list as $kp => $vp)
    {
        if($vp['level'] == 0)
        {
            $vp['level'] = 0.5;
        }
        echo str_repeat("&nbsp", $vp['level']*8).'property_id:'.$vp['id'].", md5:".md5($vp['id']).", name:".$vp['name']."</br>";
    }
    
}


?>

