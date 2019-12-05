<?php

/**
 * йу╡ь╡ывВ
 * лют╡
 */

include_once('../common.inc.php');

$target_id = $_INPUT['target_id'];
$target_type = $_INPUT['target_type'];
$operate = $_INPUT['operate'];


$ret = get_api_result('customer/favor_operate.php',array(
    'user_id' => $yue_login_id,
    'target_id' => $target_id,
    'target_type' => $target_type,
    'operate' => $operate
    )
);


mall_mobile_output( $ret, false );

?>