<?php

//****************** pc版头部通用 start ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'face/face.tpl.htm');



// ================== pc版头部通用 end ==================

/*
     * 获取一个管理员下面有效的商家列表
     *
     * @param string $admin_user_id    管理员ID
     * @param string $b_select_count   是否返回总数：TRUE返回总数 FALSE返回列表
     * @param string $limit     分页
     * @return array|int
     */

$ret=$obj->get_valid_seller_list($yue_login_id,$b_select_count = false,$limit = '0,1000');

$goods_obj = POCO::singleton ( 'pai_mall_operate_agent_class' );

foreach ($ret as $k=>$value) {
  $icon = get_seller_user_icon($value['seller_user_id']);
  $name = get_seller_nickname_by_user_id($value['seller_user_id']);
  $good_list = $goods_obj->user_goods_list($value['seller_user_id'],array("show"=>1), false,  'goods_id DESC', '0,200');
  $ret[$k]['icons'] = $icon;
  $ret[$k]['nickname'] = $name;
  $ret[$k]['good_list'] = $good_list;
};

$tpl->assign('sellers',mall_output_format_data($ret));
$tpl->assign('yue_login_id',$yue_login_id);

?>
