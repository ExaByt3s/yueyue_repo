<?php
/*
 * //商家资料页编辑异步操作模块
 *
 *
 *
 */


$t_teacher     = trim($_INPUT['t_teacher']);
$t_experience   = trim($_INPUT['t_experience']);

if(empty($t_teacher))
{
    $arr['msg'] = 't_teacher empty';
    $arr['status'] = -23;
    echo json_encode($arr);
    exit;
}
if(empty($t_experience))
{
    $arr['msg'] = 't_experience empty';
    $arr['status'] = -24;
    echo json_encode($arr);
    exit;
}


$data['t_teacher']     = iconv('utf-8', 'gbk//IGNORE',$t_teacher);
$data['t_experience']    =  iconv('utf-8', 'gbk//IGNORE',$t_experience);
?>
