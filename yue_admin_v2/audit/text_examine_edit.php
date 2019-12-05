<?php
/**
 * @desc:   文字审核操作类
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/28
 * @Time:   12:13
 * version: 1.0
 */

include_once('common.inc.php');
$text_examine_v2_obj = new pai_text_examine_v2_class();

$act = trim($_INPUT['act']);
$retUrl = $_SERVER['HTTP_REFERER'];

if(strlen($act) <1) js_pop_msg_v2('非法操作');
$ids = $_INPUT ['ids']; //传过来的文字一维ID
if (empty($ids)) js_pop_msg_v2('传过来的数据为空');

if($act == 'pass')//通过操作
{
    $ret = $text_examine_v2_obj->text_examine_pass($ids);
    $code = intval($ret['code']);
    if($code >0) js_pop_msg_v2('文字审核成功',false,$retUrl);
    js_pop_msg_v2('文字审核失败',false,$retUrl);
}
elseif($act == 'del') //删除文字
{
    $ret = $text_examine_v2_obj->text_examine_del($ids);
    $code = intval($ret['code']);
    if($code >0) js_pop_msg_v2('文字删除成功',false,$retUrl);
    js_pop_msg_v2('文字删除失败',false,$retUrl);
}
elseif($act == 'pass_to_del')//从已经审核文字到删除
{
    $ymonth = trim($_INPUT['ymonth']);//传递文字的年月过来
    if(strlen($ymonth)>0) $date = $ymonth.'-01';
    $ret = $text_examine_v2_obj->text_pass_to_del($ids,$date);
    $code = intval($ret['code']);
    if($code >0) js_pop_msg_v2('文字删除成功',false,$retUrl);
    js_pop_msg_v2('文字删除失败',false,$retUrl);
}
elseif($act == 'del_to_pass')//从删除的文字到审核的文字
{
    $ymonth = trim($_INPUT['ymonth']);//传递文字的年月过来
    if(strlen($ymonth)>0) $date = $ymonth.'-01';
    $ret = $text_examine_v2_obj->text_del_to_pass($ids,$date);
    $code = intval($ret['code']);
    if($code >0) js_pop_msg_v2('恢复文字成功',false,$retUrl);
    js_pop_msg_v2('恢复文字失败',false,$retUrl);
}

js_pop_msg_v2('非法操作',false,$retUrl);