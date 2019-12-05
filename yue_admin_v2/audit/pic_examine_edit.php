<?php
/**
 * @desc:   商品审核控制器
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/8
 * @Time:   14:02
 * version: 2.0
 */
include_once ('common.inc.php');
$pic_examine_v2_obj = new pai_pic_examine_v2_class();

$act = trim($_INPUT['act']);
$retUrl = $_SERVER['HTTP_REFERER'];

if(strlen($act) <1) js_pop_msg_v2('非法操作');
$ids = $_INPUT ['ids']; //传过来的图片一维ID

if (empty($ids)) js_pop_msg_v2('传过来的数据为空');

//动作操作
if($act == 'pass')//图片审核通过
{
    $ret = $pic_examine_v2_obj->pic_examine_pass($ids);
    $retID = intval($ret['code']);
    if($retID >0) js_pop_msg_v2('图片审核成功',false,$retUrl);
    js_pop_msg_v2('图片审核失败',false,$retUrl);
}
elseif($act == 'del')//图片审核删除
{
    $ids = trim($ids);//这里传过来是是个字符串122,333
    $img_type = trim($_INPUT['img_type']);
    $tpl_id = intval($_INPUT['tpl_id']);//模板ID
    $tpl_detail = trim($_INPUT['tpl_detail']);//模板内容，有内容模板ID作废
    if(!in_array($img_type,$type_img_arr)) js_pop_msg_v2('非法操作');
    $ret = $pic_examine_v2_obj->pic_examine_del($img_type,$ids,$tpl_id,$tpl_detail);
    $retID = $ret['code'];
    if($retID >0) js_pop_msg_v2('删除图片成功',true,"pic_examine_list.php?act={$img_type}");
    js_pop_msg_v2('删除图片失败',true,"pic_examine_list.php?act={$img_type}");
}
elseif($act == 'pass_to_del') //从通过去到删除
{
    $ymonth = trim($_INPUT['ymonth']);//传递图片的年月过来
    if(strlen($ymonth)>0) $date = $ymonth.'-01';
    $ret = $pic_examine_v2_obj->pic_pass_to_del($ids,$date);
    $retID = intval($ret['code']);
    //print_r($ret);exit;
    if($retID >0) js_pop_msg_v2('删除图片成功',false,$retUrl);
    js_pop_msg_v2('删除图片失败',false,$retUrl);
}
elseif($act == 'del_to_pass')//从删除到通过
{
    $ymonth = trim($_INPUT['ymonth']);//传递图片的年月过来
    if(strlen($ymonth)>0) $date = $ymonth.'-01';
    $ret = $pic_examine_v2_obj->pic_del_to_pass($ids,$date);
    $retID = intval($ret['code']);
    if($retID >0) js_pop_msg_v2('恢复图片成功',false,$retUrl);
    js_pop_msg_v2('恢复图片失败',false,$retUrl);
}
else
{
    js_pop_msg_v2('非法操作');
}