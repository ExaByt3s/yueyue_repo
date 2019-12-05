<?php
include_once("../../poco_app_common.inc.php");

$act        = $_POST['act'];

if($act == 'add')
{
    $user_type  = $_POST['user_type'];
    $user_name  = $_POST['user_name'];
    $user_tel   = $_POST['user_tel'];
    
    if(!$user_name)
    {
        echo "<script>alert('名字不能为空！');</script>";
        exit();
    }

    if(!$user_tel)
    {
        echo "<script>alert('电话不能为空！');</script>";
        exit();
    }
    
    $topic_obj = POCO::singleton('pai_topic_class'); 
    if($topic_obj->add_topic_sign_up($user_name, $user_tel, $user_type))
    {
    	include_once(G_YUEYUE_ROOT_PATH . "/system_service/sms_service/poco_app_common.inc.php");
        $user_tel = (int)$user_tel;
        if(strlen($user_tel) == 11)
        {
            $phone = $user_tel;
            if($user_type == 'cameraman')
            {
                $content = '感谢您参与《私拍体验活动第二季》，您已经成功获得参与资格，活动开始前我们将会通知你来抢约，敬请期待。';
            }else{
                $content = '感谢您参与《私拍体验活动第二季》，您已经成为“Yue”女神候选人，约约工作人员将会尽快通知您的评选结果';
            }
            $product_type = 0;//默认0，0数联短信延时通道，1数联短信报备通道，10摩幻时短信延时通道，11摩幻时短信报备通道
            $sms_obj = POCO::singleton('class_sms_v2');
            $ret = $sms_obj->save_and_send_sms($phone, $content, $product_type);    
        }
        
        
        echo "<script>alert('提交预约成功，请等待工作人员联系');location.href='index.html';</script>";
    }else{
        echo "<script>alert('该手机号码已报名！');</script>";
    }   
}
?>