<?php
/*
 * //商家资料页编辑异步操作模块
 *
 *
 *
 */



$p_goodat     = trim($_INPUT['p_goodat']);
$p_experience    = trim($_INPUT['p_experience']);



if(empty($p_goodat))
{
    $arr['msg'] = 'p_goodat empty';
    $arr['status'] = -11;
    echo json_encode($arr);
    exit;
}
if(empty($p_experience))
{
    $arr['msg'] = 'p_experience empty';
    $arr['status'] = -12;
    echo json_encode($arr);
    exit;
}


$data['p_goodat']     = iconv('utf-8', 'gbk//IGNORE',$p_goodat);
$data['p_experience']    =  iconv('utf-8', 'gbk//IGNORE',$p_experience);



?>