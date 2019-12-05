<?php 

/* 
 *������˿�����
 *
*/
	include_once 'common.inc.php';
    check_authority(array('text_examine'));
    $text_examine_obj = POCO::singleton ('pai_text_examine_class');
    $act   = $_INPUT ['act'] ? $_INPUT ['act'] : 'pass';
    //����ʹ��100
    $audit_id   = $yue_login_id;
    $audit_time = time();
    $ids = $_INPUT ['ids'];
    if (empty($ids) || !is_array($ids)) 
    {
       echo "<script>alert('���ύ��������������');location.href='text_examine_list.php';</script>";
       exit();
    }
    switch ($act) {
        //���ͨ��
        case 'pass':
            $text_examine_obj->pass_examine_text($audit_id, $audit_time, $ids);  
            echo "<script>alert('������ֳɹ�');location.href='text_examine_list.php';</script>";
            break;
        //ɾ������
        case 'del':
            $text_examine_obj->del_examine_text($audit_id, $audit_time, $ids);  
            echo "<script>alert('ɾ�����ֳɹ�');location.href='text_examine_list.php';</script>";
            break;
        //���Ѿ�������ֵ��������
        case 'textPassDel':
            $ymonth = $_INPUT ['ymonth'] ? $_INPUT ['ymonth'] : '';
            $text_examine_obj->delPass_examine_text($ymonth,$audit_id, $audit_time,$ids);  
            echo "<script>alert('ɾ�����ֳɹ�');location.href='text_pass_examine_list.php';</script>";
            break;
        //��ɾ�����Ѿ����
        case 'textDelPass':
            $ymonth = $_INPUT ['ymonth'] ? $_INPUT ['ymonth'] : '';
            $text_examine_obj->passDel_examine_text($ymonth,$audit_id, $audit_time, $ids);  
            echo "<script>alert('�ָ����ֳɹ�');location.href='text_del_examine_list.php';</script>";
            break;
        default:
            echo "<script>alert('���ύ��������������');location.href='text_examine_list.php';</script>";
            exit();
            break;
    }


 ?>