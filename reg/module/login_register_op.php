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
$yue_role = trim($_INPUT['yue_role']);
$r_url = trim($_INPUT['r_url']);
$login_modle = trim($_INPUT['login_modle']);
if($yue_action_type=="login" && $login_modle=="supplier")
{
    $yue_id = (int)$_INPUT['supplier_yue_id'];
    $yue_password = trim($_INPUT['supplier_yue_password']);
}

//校验返回链接结构，作安全处理
if(!empty($r_url) || $r_url!="")
{
    $parse_url_arr = parse_url($r_url);

    if($parse_url_arr['scheme']!="http")
    {
        echo "<script>
               parent.alert('返回链接不符合链接结构');
               parent.location.href='http://www.yueus.com/reg/login.php';
            </script>";
        exit();
    }
    if(!preg_match("/\.yueus\.com/",$parse_url_arr['host']))
    {
        echo "<script>
               parent.alert('返回链接不是yueus域名的链接');
               parent.location.href='http://www.yueus.com/reg/login.php';
            </script>";
        exit();
    }
}


if($yue_action_type=="login")
{
    if($login_modle=="cameraman_model")
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
            if(!empty($r_url))
            {
                echo "<script>
                parent.location.href='".$r_url."';
                </script>";
                exit();
            }
            else
            {
                echo "<script>
                parent.location.href='http://www.yueus.com/reg/login.php';
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
    else if($login_modle=="supplier")
    {
        if(empty($yue_id) || $yue_password=="")
        {
            echo "<script>
            parent.document.getElementById('supplier_yue_error_tips').style.display = 'block';
            parent.document.getElementById('supplier_yue_error_tips').innerHTML = '商家ID或者密码不能为空';
            parent.document.getElementById('yue_submit_btn').innerHTML = '登录'; 
            </script>";
            exit();
        }
        define('YUE_LOGIN_ORGANIZATION',1);
        $get_all_profile_obj = POCO::singleton('pai_task_profile_class');
        $is_supplier = $get_all_profile_obj->check_seller_by_user_id($yue_id);
        if (!$is_supplier) 
        {
            //做错处理
            echo "<script>
            parent.document.getElementById('supplier_yue_error_tips').style.display = 'block';
            parent.document.getElementById('supplier_yue_error_tips').innerHTML = '登录出错，该ID不是商家号的ID';
            parent.document.getElementById('supplier_yue_submit_btn').innerHTML = '登录'; 
            </script>";
            exit();
            
        }
        else
        {
            
            //查yueid手机号登录
            $yue_supplier_phone = $user_obj->get_phone_by_user_id($yue_id);
            $ret = $user_obj->user_login($yue_supplier_phone, $yue_password);
            if($ret>0)
            {
                echo "<script>
                parent.location.href='http://task.yueus.com/';
                </script>";
                exit();
            }
            else
            {
                echo "<script>
                parent.document.getElementById('supplier_yue_error_tips').style.display = 'block';
                parent.document.getElementById('supplier_yue_error_tips').innerHTML = '登录出错，商家ID号或者密码错误';
                parent.document.getElementById('supplier_yue_submit_btn').innerHTML = '登录'; 
                </script>";
                exit();
            }
 
        }
    }
    

}
else if($yue_action_type=="register")
{
    if(empty($yue_role))
    {
        echo "<script>
        parent.document.getElementById('yue_error_tips').style.display = 'block';
        parent.document.getElementById('yue_error_tips').innerHTML = '请选择角色';
        parent.document.getElementById('yue_submit_btn').innerHTML = '立即注册'; 
        </script>";
        exit();
    }
    
    
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
        parent.document.getElementById('yue_error_tips').innerHTML = '注册失败，该手机号已经注册过约约';
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
            $user_info_arr ['role'] = $yue_role;
            $member_id = $user_obj->create_account($user_info_arr, $err_msg);//创建用户

            
            
            //echo $ret;
            if($member_id>0)
            {
                //清理cookie
                $user_obj->load_member($member_id, $b_hide_online = null);
                if(!empty($r_url))
                {
                    echo "<script>
                    parent.location.href='".$r_url."';
                    </script>";
                    exit();
                }
                else
                {
                    echo "<script>
                    parent.location.href='http://www.yueus.com/reg/login.php';
                    </script>";
                    exit();
                }
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