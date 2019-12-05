<?php 

/* 
 *图片待审核控制器
 *
*/
    include_once 'common.inc.php';
    check_authority(array('pic_examine'));
    $retUrl = $_SERVER['HTTP_REFERER'];
    $pic_examine_obj = POCO::singleton ('pai_pic_examine_class');
    $act   = trim($_INPUT ['act']) ? trim($_INPUT ['act']) : 'pass';
    $audit_id   = intval($yue_login_id);
    $audit_time = time();
    $ids = $_INPUT ['ids'];
    if (empty($ids)) 
    {
       echo "<script>alert('您提交过来的数据有误');location.href='{$retUrl}';</script>";
       exit();
    }
    switch ($act) {
        //未审核审核
        case 'pass':
             $pic_examine_obj->pass_examine_pic($audit_id, $audit_time, $ids);  
             echo "<script>alert('审核图片成功');location.href='{$retUrl}';</script>";
             break;
        //未审核删除
        case 'del':
            $url        = trim($_INPUT['url']) ? trim($_INPUT['url']) : 'works';
            $tpl_id     = intval($_INPUT['tpl_id']);
            $tpl_detail = trim($_INPUT['tpl_detail']);
            $ids  = explode(',', $ids);
            $pic_examine_obj->del_examine_pic($audit_id, $audit_time, $ids, $tpl_detail, $tpl_id);  
            echo "<script>alert('删除图片成功');parent.location.href='pic_examine_list.php?act={$url}';</script>";
            break;
        //审核删除
        case 'passdel':
            $ymonth = trim($_INPUT['ymonth']);
            $pic_examine_obj->delpass_examine_pic($ymonth, $audit_id, $audit_time, $ids );  
            echo "<script>alert('删除图片成功');location.href='{$retUrl}';</script>";
            break;
        //恢复图片审核
        case 'returnDel':
            $ymonth = trim($_INPUT ['ymonth']) ? trim($_INPUT ['ymonth']) : '';
            $pic_examine_obj->returnDel($ymonth, $audit_id, $audit_time,$ids);  
            echo "<script>alert('恢复图片成功');location.href='{$retUrl}';</script>";
            break;
        //重新做图
        case  'makeAgain':
                $result = $pic_examine_obj->make_again_pic($ids);
                //$arr = array();
                if($result)
                {
                    $arr['msg']    = 'success';
                    $arr['result'] = $result;
                }
                else
                {
                    $arr['msg'] = 'fail';
                }
                echo json_encode($arr);
                //exit;
               break;
        default:
            echo "<script>alert('您提交过来的数据有误');location.href='{$retUrl}';</script>";
            break;
    }

 ?>