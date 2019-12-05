<?php 

/* 
 *文字审核控制器
 *
*/
	include_once 'common.inc.php';
    check_authority(array('text_examine'));
    $text_examine_obj = POCO::singleton ('pai_text_examine_class');
    $act   = $_INPUT ['act'] ? $_INPUT ['act'] : 'pass';
    //测试使用100
    $audit_id   = $yue_login_id;
    $audit_time = time();
    $ids = $_INPUT ['ids'];
    if (empty($ids) || !is_array($ids)) 
    {
       echo "<script>alert('您提交过来的数据有误');location.href='text_examine_list.php';</script>";
       exit();
    }
    switch ($act) {
        //审核通过
        case 'pass':
            $text_examine_obj->pass_examine_text($audit_id, $audit_time, $ids);  
            echo "<script>alert('审核文字成功');location.href='text_examine_list.php';</script>";
            break;
        //删除文字
        case 'del':
            $text_examine_obj->del_examine_text($audit_id, $audit_time, $ids);  
            echo "<script>alert('删除文字成功');location.href='text_examine_list.php';</script>";
            break;
        //由已经审核文字到文字审核
        case 'textPassDel':
            $ymonth = $_INPUT ['ymonth'] ? $_INPUT ['ymonth'] : '';
            $text_examine_obj->delPass_examine_text($ymonth,$audit_id, $audit_time,$ids);  
            echo "<script>alert('删除文字成功');location.href='text_pass_examine_list.php';</script>";
            break;
        //由删除到已经审核
        case 'textDelPass':
            $ymonth = $_INPUT ['ymonth'] ? $_INPUT ['ymonth'] : '';
            $text_examine_obj->passDel_examine_text($ymonth,$audit_id, $audit_time, $ids);  
            echo "<script>alert('恢复文字成功');location.href='text_del_examine_list.php';</script>";
            break;
        default:
            echo "<script>alert('您提交过来的数据有误');location.href='text_examine_list.php';</script>";
            exit();
            break;
    }


 ?>