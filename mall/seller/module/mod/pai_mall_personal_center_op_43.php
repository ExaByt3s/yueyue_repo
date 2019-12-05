<?php
/*
 * //商家资料页编辑异步操作模块
 *
 *
 *
 */


$ot_label = trim($_INPUT['ot_label']);
$ot_otherlabel = trim($_INPUT['ot_otherlabel']);


if(empty($ot_label))
{
    $arr['msg'] = 'hz_goodat empty';
    $arr['status'] = -31;
    echo json_encode($arr);
    exit;
}


$data['ot_label']     = iconv('utf-8', 'gbk//IGNORE',$ot_label);
$data['ot_otherlabel']     = iconv('utf-8', 'gbk//IGNORE',$ot_otherlabel);
?>
