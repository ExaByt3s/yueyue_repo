<?php
/*
 * //商家资料页编辑异步操作模块
 *
 *
 *
 */


$m_height  = intval($_INPUT['m_height']);
$m_weight  = intval($_INPUT['m_weight']);
$b         = intval($_INPUT['b']);
$w         = intval($_INPUT['w']);
$h         = intval($_INPUT['h']);
$m_cups    = intval($_INPUT['m_cups']);
$m_cup     = trim($_INPUT['m_cup']);
$m_level   = intval($_INPUT['m_level']);
$m_experience = trim($_INPUT['m_experience']);

if($m_height <1)
{
    $arr['msg'] = 'm_height empty';
    $arr['status'] = -13;
    echo json_encode($arr);
    exit;
}
if($m_weight <1)
{
    $arr['msg'] = 'm_weight empty';
    $arr['status'] = -14;
    echo json_encode($arr);
    exit;
}
if($b <1)
{
    $arr['msg'] = 'b empty';
    $arr['status'] = -15;
    echo json_encode($arr);
    exit;
}
if($w <1)
{
    $arr['msg'] = 'w empty';
    $arr['status'] = -16;
    echo json_encode($arr);
    exit;
}
if($h <1)
{
    $arr['msg'] = 'h empty';
    $arr['status'] = -17;
    echo json_encode($arr);
    exit;
}
if($m_cups <1)
{
    $arr['msg'] = 'm_cups empty';
    $arr['status'] = -18;
    echo json_encode($arr);
    exit;
}
if(strlen($m_cup) <1)
{
    $arr['msg'] = 'm_cup empty';
    $arr['status'] = -19;
    echo json_encode($arr);
    exit;
}
if($m_level<1)
{
    $arr['msg'] = 'm_level empty';
    $arr['status'] = -20;
    echo json_encode($arr);
    exit;
}
if(empty($m_experience))
{
    $arr['msg'] = 'm_experience empty';
    $arr['status'] = -22;
    echo json_encode($arr);
    exit;
}


$data['m_height']    = $m_height;
$data['m_weight']    = $m_weight;
$data['m_bwh']       = $b.'-'.$w.'-'.$h;
$data['m_cups']      = $m_cups;
$data['m_cup']       = $m_cup;
$data['m_level']     = $m_level;
$data['m_experience']     = iconv('utf-8', 'gbk//IGNORE',$m_experience);
?>