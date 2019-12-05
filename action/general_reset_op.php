<?php

/**
 * 约约重设密码页（form或者异步）
 *
 *
 * author    星星
 *
 *
 * 2015-6-9
 */

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/basics.fun.php');
$user_obj = POCO::singleton ( 'pai_user_class' );
//检查验证码
$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );

$action_mode = trim($_INPUT['action_mode']);//提交的方式，form或者异步

$phone = (int)$_INPUT['phone'];
$password = trim($_INPUT['password']);
$r_url = trim($_INPUT['r_url']);//来源地址
$active_word = (int)$_INPUT['active_word'];//激活码

if($action_mode=="form")
{
    if(empty($phone) || $password=="" || empty($active_word))
    {
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '手机号,密码或验证码格式有误';
         parent.document.getElementById('submit_btn').innerHTML = '确认修改';
         </script>";
        exit();
    }

    //检查密码是否超过六位，大于32位
    $pwd_len = strlen($password);
    if($pwd_len<6)
    {
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '密码不能少于6位';
         parent.document.getElementById('submit_btn').innerHTML = '确认修改';
         </script>";
        exit();
    }
    if($pwd_len>=32)
    {
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '密码不能大于等于32位';
         parent.document.getElementById('submit_btn').innerHTML = '确认修改';
         </script>";
        exit();
    }
    //判断密码不能全为数字
    $is_all_num_res = is_numeric($password);
    if($is_all_num_res)
    {
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '密码不能全为数字';
         parent.document.getElementById('submit_btn').innerHTML = '确认修改';
         </script>";
        exit();
    }
    //判断密码不能有中文
    $is_ch_res = preg_match("/[\x7f-\xff]/", $password);
    if($is_ch_res)
    {
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '密码不能有中文';
         parent.document.getElementById('submit_btn').innerHTML = '确认修改';
         </script>";
        exit();
    }



    //手机是否有注册过约约
    $ret = $user_obj->check_cellphone_exist($phone);
    if($ret)
    {



        //检查验证码
        if($active_word>0)
        {
            //校验验证码是否正确
            $group_key = 'G_PAI_USER_PASSWORD_VERIFY';
            $ret = $pai_sms_obj->check_verify_code ( $phone, $group_key,$active_word);
            if(!$ret)
            {
                //错误
                echo "<script>
                    parent.document.getElementById('error_tips').style.display = 'block';
                    parent.document.getElementById('err_tips_txt').innerHTML = '修改失败，验证码出错';
                    parent.document.getElementById('submit_btn').innerHTML = '立即修改';
                    </script>";
                exit();

            }

            $ret = $user_obj->update_pwd_by_phone ( $phone, $password );
            if(!$ret)
            {
                echo "<script>
                    parent.document.getElementById('error_tips').style.display = 'block';
                    parent.document.getElementById('err_tips_txt').innerHTML = '修改失败';
                    parent.document.getElementById('submit_btn').innerHTML = '立即修改';
                  </script>";
                exit();
            }
            else
            {
                if(!empty($r_url))
                {
                    /*echo "<script>
                    parent.location.href='".$r_url."';
                    </script>";
                    exit();*/
                    $r_url = urlencode($r_url);
                    echo "<script>
                    parent.location.href='http://www.yueus.com/pc/login.php?".$r_url."';
                    </script>";
                    exit();


                }
                else
                {
                    /*echo "<script>
                      parent.location.href='http://www.yueus.com/';
                      </script>";
                    exit();*/
                    echo "<script>
                      parent.location.href='http://www.yueus.com/pc/login.php';
                      </script>";
                    exit();


                }
            }
        }
        else
        {
            echo "<script>
              parent.document.getElementById('error_tips').style.display = 'block';
              parent.document.getElementById('err_tips_txt').innerHTML = '注册失败，验证码为空';
              parent.document.getElementById('submit_btn').innerHTML = '立即注册';
              </script>";
            exit();
        }
    }
    else
    {
        //错误
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '该手机号没有注册约约';
         parent.document.getElementById('submit_btn').innerHTML = '确认修改';
         </script>";
        exit();

    }
}
else if($action_mode=="ajax")
{
    $ajax_status = 1;
    if(empty($phone) || $password=="" || empty($active_word))
    {
        $ajax_status = 0;
        $res = "phone or password or active_word error";
    }

    //检查密码是否超过六位
    $pwd_len = strlen($password);
    if($pwd_len<6)
    {
        $ajax_status = 0;
        $res = "password too short error";
    }

    if($ajax_status==1)
    {

        if(!empty($phone) && !empty($password) && !empty($active_word))
        {

            //手机是否有注册过约约
            $ret = $user_obj->check_cellphone_exist($phone);
            if($ret)
            {
                //检查验证码
                if($active_word>0)
                {
                    //校验验证码是否正确
                    $group_key = 'G_PAI_USER_PASSWORD_VERIFY';
                    $ret = $pai_sms_obj->check_verify_code ( $phone, $group_key, $active_word);
                    if(!$ret)
                    {
                        //错误
                        $ajax_status = 0;
                        $res = "active_word error";
                    }
                    else
                    {
                        $ret = $user_obj->update_pwd_by_phone ( $phone, $password );
                        if(!$ret)
                        {
                            $ajax_status = 0;
                            $res = "reset error";
                        }
                        else
                        {
                            $res = "success";
                        }
                    }


                }
                else
                {
                    $ajax_status = 0;
                    $res = "active_word empty error";
                }
            }
            else
            {
                $ajax_status = 0;
                $res = "phone not exist error";

            }
        }
    }

    $res_arr = array(
        "ajax_status"=>$ajax_status,
        "res"=>$res,
        "r_url"=>$r_url

    );

    echo json_encode($res_arr);
}




?>