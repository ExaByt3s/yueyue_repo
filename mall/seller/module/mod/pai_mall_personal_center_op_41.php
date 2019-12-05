<?php
/*
 * //商家资料页编辑异步操作模块
 *
 *
 *
 */


$ms_experience = trim($_INPUT['ms_experience']);
$ms_certification= trim($_INPUT['ms_certification']);


if(empty($ms_experience))
{
    $arr['msg'] = 'ms_experience empty';
    $arr['status'] = -29;
    echo json_encode($arr);
    exit;
}

if(empty($ms_certification))
{
    $arr['msg'] = 'ms_certification empty';
    $arr['status'] = -30;
    echo json_encode($arr);
    exit;
}


$data['ms_experience']     = iconv('utf-8', 'gbk//IGNORE',$ms_experience);
$data['ms_certification']     = iconv('utf-8', 'gbk//IGNORE',$ms_certification);
?>
