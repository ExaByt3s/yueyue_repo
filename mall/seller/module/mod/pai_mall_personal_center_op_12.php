<?php
/*
 * //商家资料页编辑异步操作模块
 *
 *
 *
 */


$yp_area = trim($_INPUT['yp_area']);

if(empty($yp_area))
{
    $arr['msg'] = 'yp_area empty';
    $arr['status'] = -26;
    echo json_encode($arr);
    exit;
}


$data['yp_area']     = iconv('utf-8', 'gbk//IGNORE',$yp_area);
?>
