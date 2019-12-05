<?php
/*
 * //商家资料页编辑异步操作模块
 *
 *
 *
 */


$ev_goodat = trim($_INPUT['ev_goodat']);
$ev_other = trim($_INPUT['ev_other']);


if(empty($ev_goodat))
{
    $arr['msg'] = 'ev_goodat empty';
    $arr['status'] = -33;
    echo json_encode($arr);
    exit;
}


$data['ev_goodat']     = iconv('utf-8', 'gbk//IGNORE',$ev_goodat);
$data['ev_other']     = iconv('utf-8', 'gbk//IGNORE',$ev_other);
?>
