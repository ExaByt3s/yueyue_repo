<?php

/**
 * 约约注册处理页（form或者异步）
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
$role = trim($_INPUT['role']);
$role = "cameraman";
$yue_open_id = trim($_COOKIE['yueus_openid']);


if($action_mode=="form")
{
    if(empty($phone) || $password=="" || empty($active_word))
    {
       echo "<script>
        parent.document.getElementById('error_tips').style.display = 'block';
        parent.document.getElementById('err_tips_txt').innerHTML = '手机号,密码或验证码格式有误';
        parent.document.getElementById('submit_btn').innerHTML = '立即注册';
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
         parent.document.getElementById('submit_btn').innerHTML = '立即注册';
         </script>";
        exit();
    }
    if($pwd_len>=32)
    {
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '密码不能大于等于32位';
         parent.document.getElementById('submit_btn').innerHTML = '立即注册';
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
         parent.document.getElementById('submit_btn').innerHTML = '立即注册';
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
         parent.document.getElementById('submit_btn').innerHTML = '立即注册';
         </script>";
        exit();
    }


    $user_info = $user_obj->get_user_by_phone ( $phone );
	//检查用户是否预先导入的
	if ($user_info ['pwd_hash'] != 'poco_model_db')
	{
		//手机是否有注册过约约
       	 $ret = $user_obj->check_cellphone_exist($phone);
	}

    if($ret)
    {
        //错误
       echo "<script>
        parent.document.getElementById('error_tips').style.display = 'block';
        parent.document.getElementById('err_tips_txt').innerHTML = '注册绑定失败，该手机号已经注册过约约';
        parent.document.getElementById('submit_btn').innerHTML = '立即注册';
        </script>";
       exit();
    }
    else
    {
    	//微信登录，处理关于绑定的事情
    	$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
    	if( $__is_weixin )
    	{
    		if( strlen($yue_open_id)<1 )
    		{
    			echo "<script>
			        parent.document.getElementById('error_tips').style.display = 'block';
			        parent.document.getElementById('err_tips_txt').innerHTML = '注册绑定失败，请先到微信授权';
			        parent.document.getElementById('submit_btn').innerHTML = '立即注册';
			        </script>";
    			exit();
    		}
    		
    		//检查微信号是否已绑定约约
    		$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
    		$check_open_id_bind = $bind_weixin_obj->get_bind_info_by_open_id($yue_open_id);
    		if( $check_open_id_bind )
    		{
    			echo "<script>
			        parent.document.getElementById('error_tips').style.display = 'block';
			        parent.document.getElementById('err_tips_txt').innerHTML = '注册绑定失败，该微信号已绑定过约约了';
			        parent.document.getElementById('submit_btn').innerHTML = '立即注册';
			        </script>";
    			exit();
    		}
    	}
    	
        //检查验证码
        if($active_word>0)
        {
            //校验验证码是否正确
            $ret = $pai_sms_obj->check_phone_reg_verify_code ( $phone, $active_word, 0, true );
            if(!$ret)
            {
              //错误
              echo "<script>
                parent.document.getElementById('error_tips').style.display = 'block';
                parent.document.getElementById('err_tips_txt').innerHTML = '注册失败，验证码出错';
                parent.document.getElementById('submit_btn').innerHTML = '立即注册';
                </script>";
              exit();

            }

			$reg_from_tmp = 'pc';
            $nickname_tmp = "手机用户".substr($phone,-4);
            //微信登录，处理关于绑定的事情
            if( $__is_weixin )
            {
            	$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
            	$weixin_user_info = $weixin_pub_obj->get_weixin_user($yue_open_id);
            	$nickname_tmp = trim($weixin_user_info['nickname']);
            	if( empty($weixin_user_info['nickname']) )
            	{
            		$nickname_tmp = "微信用户";
            	}
				$reg_from_tmp = 'weixin';
            }
            
            //没注册过约约
            $user_info_arr ['pwd'] = $password;
            $user_info_arr ['cellphone'] = $phone;
            $user_info_arr ['nickname'] = $nickname_tmp;
            $user_info_arr ['role'] = $role;
            $user_info_arr ['reg_from'] = $reg_from_tmp;
            $member_id = $user_obj->create_mall_account($user_info_arr, $err_msg);//创建用户


            //echo $ret;
            if($member_id>0)
            {
            	//清理cookie
            	$user_obj->load_member($member_id, $b_hide_online = null);
            	
            	//微信登录，处理关于绑定的事情
            	$qrscene_tj_str = '';
            	if( $__is_weixin )
            	{
            		$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
            		$bind_data = array();
            		$bind_data['user_id'] = $member_id;
            		$bind_data['open_id'] = $yue_open_id;
            		$affected_rows = $bind_weixin_obj->add_bind($bind_data);
            		if( $affected_rows )
            		{
            			//同步微信头像
            			if( $weixin_user_info['headimgurl'] )
            			{
            				$bind_weixin_obj->upload_icon($member_id, $weixin_user_info['headimgurl']);
            			}
            			
            			$template_code = 'G_PAI_WEIXIN_USER_BIND';
            			$data = array('nickname'=>$weixin_user_info['nickname'], 'cellphone'=>$phone);
            			$weixin_pub_obj->message_template_send_by_user_id($member_id, $template_code, $data);
            		}
            	}
            	
                if(!empty($r_url))
                {
                    //form提交校验返回链接结构，作安全处理
                        $parse_url_arr = parse_url($r_url);
                        if($parse_url_arr['scheme']!="http")
                        {
                            echo "<script>
                           parent.alert('返回链接不符合链接结构');
                           parent.location.href='http://www.yueus.com/';
                           </script>";
                            exit();
                        }

                        if(!preg_match("/\.yueus\.com/",$parse_url_arr['host']))
                        {
                            echo "<script>
                           parent.alert('返回链接不是yueus域名的链接');
                           parent.location.href='http://www.yueus.com/';
                           </script>";
                            exit();
                        }

                    echo "<script>
                    parent.location.href='".$r_url."';
                    </script>";
                    exit();
                }
                else
                {
                    //没回链情况下to do
                    //判断当前登录人是否商家
                    $mall_obj = POCO::singleton('pai_mall_seller_class');
                    $seller_info=$mall_obj->get_seller_info($member_id,2);

                    //注释等页面全有时候加to_do
                    if(!empty($seller_info))
                    {
                        //商家跳去商家页
                        echo "<script>
                            parent.location.href='http://s.yueus.com/';
                            </script>";
                        exit();
                    }
                    else
                    {
                        //默认跳去首页
                        echo "<script>
                            parent.location.href='http://www.yueus.com/';
                             </script>";
                        exit();
                    }


                }
            }
            else
            {
               echo "<script>
                 parent.document.getElementById('error_tips').style.display = 'block';
                 parent.document.getElementById('err_tips_txt').innerHTML = '注册异常，请稍后再试';
                 parent.document.getElementById('submit_btn').innerHTML = '立即注册';
                 </script>";
               exit();
            }
        }
        else
        {
             echo "<script>
              parent.document.getElementById('error_tips').style.display = 'block';
              parent.document.getElementById('err_tips_txt').innerHTML = '注册失败，验证码有误';
              parent.document.getElementById('submit_btn').innerHTML = '立即注册';
              </script>";
            exit();
        }
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

    if(!empty($phone) && !empty($password) && !empty($active_word))
    {
    	$user_info = $user_obj->get_user_by_phone ( $phone );
		//检查用户是否预先导入的
		if ($user_info ['pwd_hash'] != 'poco_model_db')
		{
			//手机是否有注册过约约
       		 $ret = $user_obj->check_cellphone_exist($phone);
		}
		
        if($ret)
        {
            //错误
            $ajax_status = 0;
            $res = "phone_exist error";
        }
        else
        {

            //检查验证码
            if($active_word>0)
            {
                //校验验证码是否正确
                $ret = $pai_sms_obj->check_phone_reg_verify_code ( $phone, $active_word, 0, true );
                if(!$ret)
                {
                    //错误
                    $ajax_status = 0;
                    $res = "active_word error";

                }
                else
                {

                    //没注册过约约

                    $user_info_arr ['pwd'] = $password;
                    $user_info_arr ['cellphone'] = $phone;
                    $user_info_arr ['nickname'] ="手机用户".substr($phone,-4);;
                    $user_info_arr ['role'] = $role;
                    $user_info_arr ['reg_from'] = "pc";
                    
                    $member_id = $user_obj->create_mall_account($user_info_arr, $err_msg);//创建用户


                    //echo $ret;
                    if($member_id>0)
                    {
                        //清理cookie
                        $user_obj->load_member($member_id, $b_hide_online = null);
                        $res = "success";
                    }
                    else
                    {
                        $ajax_status = 0;
                        $res = "register error";
                    }
                }
            }
            else
            {
                $ajax_status = 0;
                $res = "active_word empty error";
            }
        }
    }

    //ajax提交校验返回链接结构，作安全处理
        $parse_url_arr = parse_url($r_url);
        if($parse_url_arr['scheme']!="http")
        {

            $r_url = 'http://www.yueus.com/';
        }

        if(!preg_match("/\.yueus\.com/",$parse_url_arr['host']))
        {
            $r_url = 'http://www.yueus.com/';
        }

    $res_arr = array(
        "ajax_status"=>$ajax_status,
        "res"=>$res,
        "r_url"=>$r_url

    );

    echo json_encode($res_arr);
}














?>