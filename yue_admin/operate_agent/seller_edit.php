<?php

include_once 'common.inc.php';
$tpl = new SmartTemplate("seller_edit.tpl.htm");

$act = $_INPUT['act'] ? $_INPUT['act'] :'add';

$id = (int)$_INPUT['id'];
$seller_user_id  = trim((int)$_INPUT['seller_user_id']);
$admin_user_id = (int)$_INPUT['admin_user_id'];
$begin_time = $_INPUT['begin_time'];
$end_time = $_INPUT['end_time'];
$status = $_INPUT['status'];
$remark = $_INPUT['remark'];

$operate_obj = POCO::singleton('pai_mall_operate_agent_class');

$seller_info = $operate_obj->get_seller_info($id);

$list = $operate_obj->get_admin_list(false, $where, '0,1000');
foreach($list as $val)
{
    if($val['admin_user_id']==$seller_info['admin_user_id'])
    {
        $select .= "<option  value='$val[admin_user_id]' selected>$val[name]</option>";
    }
    else
    {
        $select .= "<option  value='$val[admin_user_id]'>$val[name]</option>";
    }
}
$seller_info['select'] = $select;
if($seller_info['begin_time'])
    $seller_info['begin_time'] = date("Y-m-d",$seller_info['begin_time']);

if($seller_info['end_time'])
    $seller_info['end_time'] = date("Y-m-d",$seller_info['end_time']);



if($_POST) {



    if (!$seller_user_id) {
        js_pop_msg("商家ID不能为空");
        exit;
    }

    if (!$begin_time) {
        js_pop_msg("开始时间不能为空");
        exit;
    }

    if (!$end_time) {
        js_pop_msg("结束时间不能为空");
        exit;
    }

    $bt = strtotime($begin_time);
    $et = strtotime($end_time);

    if($bt>=$et)
    {
        js_pop_msg("结束时间要大于开始时间");
        exit;
    }


    switch ($act) {
        case 'add':


            $insert_data['seller_user_id'] = $seller_user_id;
            $insert_data['admin_user_id'] = $admin_user_id;
            $insert_data['begin_time'] = $bt;
            $insert_data['end_time'] = $et;
            $insert_data['add_time'] = time();
            $insert_data['status'] = $status;
            $insert_data['remark'] = $remark;

            $ret = $operate_obj->add_seller($insert_data);

            if($ret['result']==1)
            {
                echo "<script>alert('添加成功');parent.location.href='seller_list.php';</script>";
            }
            else
            {
                echo "<script>alert('".$ret['message']."');</script>";
            }


            break;

        case 'edit':


            if (!$id) {
                js_pop_msg("更新ID不能为空");
                exit;
            }


            $update_data['admin_user_id'] = $admin_user_id;
            $update_data['begin_time'] = $bt;
            $update_data['end_time'] = $et;
            $update_data['status'] = $status;
            $update_data['remark'] = $remark;

            $ret = $operate_obj->update_seller($update_data, $id);

            if($ret['result']==1)
            {
                echo "<script>alert('更新成功');parent.location.href='seller_list.php';</script>";
            }
            else
            {
                echo "<script>alert('".$ret['message']."');</script>";
            }

            break;

    }

    exit;

}


$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->assign($seller_info);
$tpl->assign('act', $act);
$tpl->output();

?>