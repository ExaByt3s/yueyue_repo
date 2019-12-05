<?php
/** 
 * 绑定跟注册检测页
 * 
 * author 星星
 * 
 * 2015-1-14
 */


include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
$user_obj = POCO::singleton ( 'pai_user_class' );
//检查验证码
$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );


$yue_phone = (int)$_INPUT['yue_phone'];
$yue_password = trim($_INPUT['yue_password']);
$yue_active_word = (int)$_INPUT['yue_active_word'];
$yue_action_type = trim($_INPUT['yue_action_type']);



if($yue_action_type=="login")
{
    if(empty($yue_phone) || $yue_password=="")
    {
        echo "<script>
        parent.document.getElementById('yue_error_tips').style.display = 'block';
        parent.document.getElementById('yue_error_tips').innerHTML = '手机号或者密码不能为空哦';
        parent.document.getElementById('yue_submit_btn').innerHTML = '登录'; 
        </script>";
        exit();
    }
    $ret = $user_obj->user_login($yue_phone, $yue_password);
    if($ret>0)
    {
        //判断角色
        $role_ret = $user_obj->check_role($ret);
        if($role_ret!="model")
        {
            echo "<script>
            parent.document.getElementById('yue_error_tips').style.display = 'block';
            parent.document.getElementById('yue_error_tips').innerHTML = '该登录用户不是模特，请选择使用模特账号登录';
            parent.document.getElementById('yue_submit_btn').innerHTML = '登录'; 
            </script>";
            //作退出处理
            $user_obj->logout();
            exit();
        }
        else
        {
            echo "<script>
            parent.location.href='../edit_model_card.php';
            </script>";
            exit();

        }
        
    }
    else
    {
        echo "<script>
        parent.document.getElementById('yue_error_tips').style.display = 'block';
        parent.document.getElementById('yue_error_tips').innerHTML = '登录出错，手机号或者密码错误';
        parent.document.getElementById('yue_submit_btn').innerHTML = '登录'; 
        </script>";
        exit();
    }

}
else if($yue_action_type=="register")
{
    if(empty($yue_phone) || $yue_password=="" || empty($yue_active_word))
    {
        echo "<script>
        parent.document.getElementById('yue_error_tips').style.display = 'block';
        parent.document.getElementById('yue_error_tips').innerHTML = '手机号,密码或验证码格式有误';
        parent.document.getElementById('yue_submit_btn').innerHTML = '立即注册'; 
        </script>";
        exit();
    }
    
    //手机是否有注册过约约
    $ret = $user_obj->check_cellphone_exist($yue_phone);
    if($ret)
    {
        //错误
        echo "<script>
        parent.document.getElementById('yue_error_tips').style.display = 'block';
        parent.document.getElementById('yue_error_tips').innerHTML = '注册绑定失败，该手机号已经注册过约约';
        parent.document.getElementById('yue_submit_btn').innerHTML = '立即注册'; 
        </script>";
        exit();
    }
    else
    {

        //检查验证码
        if($yue_active_word>0)
        {
            //校验验证码是否正确
            $ret = $pai_sms_obj->check_phone_reg_verify_code ( $yue_phone, $yue_active_word, 0, true );
            if(!$ret)
            {
                //错误
                echo "<script>
                parent.document.getElementById('yue_error_tips').style.display = 'block';
                parent.document.getElementById('yue_error_tips').innerHTML = '注册失败，验证码出错';
                parent.document.getElementById('yue_submit_btn').innerHTML = '立即注册';
                </script>";
                exit();
                
            }
        
        
            //没注册过约约

            $user_info_arr ['pwd'] = $yue_password;
            $user_info_arr ['cellphone'] = $yue_phone;
            $user_info_arr ['nickname'] ="手机用户".substr($yue_phone,-4);;
            $user_info_arr ['role'] = "model";
            $member_id = $user_obj->create_account($user_info_arr, $err_msg);//创建用户

            
            
            //echo $ret;
            if($member_id>0)
            {
                //清理cookie
                $user_obj->load_member($member_id, $b_hide_online = null);
                echo "<script>
                parent.location.href='../edit_model_card.php';
                </script>";
                exit();
            }
            else
            {
                echo "<script>
                parent.document.getElementById('yue_error_tips').style.display = 'block';
                parent.document.getElementById('yue_error_tips').innerHTML = '注册异常，请稍后再试';
                parent.document.getElementById('yue_submit_btn').innerHTML = '立即注册';
                </script>";
                exit();
            }
        }
        else
        {
            echo "<script>
            parent.document.getElementById('yue_error_tips').style.display = 'block';
            parent.document.getElementById('yue_error_tips').innerHTML = '注册失败，验证码为空';
            parent.document.getElementById('yue_submit_btn').innerHTML = '立即注册';
            </script>";
            exit();
        }
    }
    
    
}
?>