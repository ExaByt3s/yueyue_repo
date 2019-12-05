<?php
/*
 * //商家资料页编辑异步操作模块
 *
 *
 *
 */


$hz_experience = trim($_INPUT['hz_experience']);
$hz_team = trim($_INPUT['hz_team']);
$hz_goodat= trim($_INPUT['hz_goodat']);
$hz_othergoodat = trim($_INPUT['hz_othergoodat']);


if(empty($hz_experience))
{
    $arr['msg'] = 'hz_experience empty';
    $arr['status'] = -32;
    echo json_encode($arr);
    exit;
}

if(empty($hz_team))
{
    $arr['msg'] = 'hz_team empty';
    $arr['status'] = -27;
    echo json_encode($arr);
    exit;
}

if(empty($hz_goodat))
{
    $arr['msg'] = 'hz_goodat empty';
    $arr['status'] = -28;
    echo json_encode($arr);
    exit;
}


$data['hz_experience']     = iconv('utf-8', 'gbk//IGNORE',$hz_experience);
$data['hz_team']     = iconv('utf-8', 'gbk//IGNORE',$hz_team);
$data['hz_goodat']     = iconv('utf-8', 'gbk//IGNORE',$hz_goodat);
$data['hz_othergoodat']     = iconv('utf-8', 'gbk//IGNORE',$hz_othergoodat);
?>
