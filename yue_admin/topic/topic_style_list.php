<?php
/**
 * ���ѡ��
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
            case 'ȫ��':
            $all = $vo;
                 break;
            case '����':
            $qx = $vo;
                 break;
            case '��ҵ':
            $sy = $vo;
                 break;
            case '����':
            $qix = $vo;
                 break;
            case '����':
            $qp = $vo;
                 break;
            case 'ŷ��':
            $om = $vo;
                 break;
            case '��ϵ':
            $hx = $vo;
                 break;
            case '��ϵ':
            $rx = $vo;
                 break;
            case '����':
            $fg = $vo;
                 break;
            case '��Ƭ':
            $jp = $vo;
                 break;
            case '�Ը�':
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