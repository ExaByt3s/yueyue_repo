<?php

include_once 'common.inc.php';

$tpl = new SmartTemplate("admin_edit.tpl.htm");

$act = $_INPUT['act'] ? $_INPUT['act'] :'add';

$id = (int)$_INPUT['id'];
$name  = trim($_INPUT['name']);
$admin_user_id = trim((int)$_INPUT['admin_user_id']);
$type_name = $_INPUT['type_name'];
$status = $_INPUT['status'];
$remark = $_INPUT['remark'];

$operate_obj = POCO::singleton('pai_mall_operate_agent_class');

$admin_info = $operate_obj->get_admin_info($id);


if($_POST) {

    if (!$name) {
        js_pop_msg("管理员不能为空");
        exit;
    }

    if (!$admin_user_id) {
        js_pop_msg("约约ID不能为空");
        exit;
    }


    switch ($act) {
        case 'add':


            $insert_data['name'] = $name;
            $insert_data['admin_user_id'] = $admin_user_id;
            $insert_data['type_name'] = $type_name;
            $insert_data['add_time'] = time();
            $insert_data['remark'] = $remark;

            $ret = $operate_obj->add_admin($insert_data);

            if($ret['result']==1)
            {
                echo "<script>alert('添加成功');parent.location.href='admin_list.php';</script>";
            }
            else
            {
                echo "<script>alert('$ret[message]');</script>";
            }


            break;

        case 'edit':


            if (!$id) {
                js_pop_msg("更新ID不能为空");
                exit;
            }


            $update_data['name'] = $name;
            $update_data['type_name'] = $type_name;
            $update_data['remark'] = $remark;

            $operate_obj->update_admin($update_data, $id);
            echo "<script>alert('更新成功');parent.location.href='admin_list.php';</script>";

            break;
    }

}


$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->assign($admin_info);
$tpl->assign('act', $act);
$tpl->output();

?>