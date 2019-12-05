<?php
/**
 * 风格选择
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-02 12:26:04
 * @version 1
 */
include_once 'common.inc.php';
include_once 'top.php';
$tpl = new SmartTemplate("topic_style_list.tpl.htm");
$style = $_INPUT['style'] ? $_INPUT['style'] : '';
if ($style) 
{
	$style_arr = explode(',', $style);
	foreach ($style_arr as $key => $vo) 
	{
        switch ($vo) 
        {
            case '全部':
            $all = $vo;
                 break;
            case '清新':
            $qx = $vo;
                 break;
            case '商业':
            $sy = $vo;
                 break;
            case '情绪':
            $qix = $vo;
                 break;
            case '街拍':
            $qp = $vo;
                 break;
            case '欧美':
            $om = $vo;
                 break;
            case '韩系':
            $hx = $vo;
                 break;
            case '日系':
            $rx = $vo;
                 break;
            case '复古':
            $fg = $vo;
                 break;
            case '胶片':
            $jp = $vo;
                 break;
            case '性感':
            $xg = $vo;
                 break;
        }
	}
	# code...
}
$tpl->assign('all', $all);
$tpl->assign('qx', $qx);
$tpl->assign('sy', $sy);
$tpl->assign('qix', $qix);
$tpl->assign('qp', $qp);
$tpl->assign('om', $om);
$tpl->assign('rx', $rx);
$tpl->assign('hx', $hx);
$tpl->assign('fg', $fg);
$tpl->assign('jp', $jp);
$tpl->assign('xg', $xg);
$tpl->assign('list', $list);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->output();