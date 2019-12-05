<?php
/**
 * @desc:   模板选择
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/9
 * @Time:   10:38
 * version: 2.0
 */
include("common.inc.php");
$template_obj = POCO::singleton('pai_template_class');
$tpl = new SmartTemplate( AUDIT_TEMPLATES_ROOT."template_select.tpl.htm" );

$act = trim($_INPUT['act']);
$ids = $_INPUT['ids'];
$url= trim($_INPUT['url']);
$id  = intval($_INPUT['id']);

if($act == 'info') //获取数据
{
    $ret = $template_obj->get_template_info_by_id($id);
    $arr = array();
    if (!empty($ret) && is_array($ret))
    {
        $arr  = array
        (
            'msg' => $msg,
            'ret' => iconv('gb2312', 'UTF-8',$ret['tpl_detail'])
        );
    }
    echo json_encode($arr);
    exit;
}


$where_str = '';
$list = $template_obj->get_template_list(false, $where_str, 'sort_order DESC,id DESC', '0,10','*' );
$tpl->assign('ids', $ids);
$tpl->assign('url', $url);
$tpl->assign('list', $list);
$tpl->output();
