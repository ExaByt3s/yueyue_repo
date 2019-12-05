<?php

/**
 * 约约登录处理页（form或者异步）
 *
 * author       星星
 *
 * 2015-6-9
 */


include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/basics.fun.php');
$user_obj = POCO::singleton ( 'pai_user_class' );

$action_mode = trim($_INPUT['action_mode']);//提交的方式，form或者异步

$phone = (int)$_INPUT['phone'];
$password = trim($_INPUT['password']);
$r_url = trim($_INPUT['r_url']);//来源地址

//日志
pai_log_class::add_log(array(), 'general_login_op', 'general_login');

if($action_mode=="form")//form提交
{
    if(empty($phone) || $password=="")
    {
        echo "<script>
            parent.document.getElementById('error_tips').style.display = 'block';
            parent.document.getElementById('err_tips_txt').innerHTML = '登录号或者密码不能为空哦';
            parent.document.getElementById('submit_btn_txt').innerHTML = '登录';
            </script>";
        exit();
    }
    $ret = $user_obj->user_login($phone, $password);
    if($ret>0)
    {
    	//微信登录，处理关于绑定的事情
    	$is_weixin_tmp = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
    	if( $is_weixin_tmp )
    	{
    		$yue_open_id = trim($_COOKIE['yueus_openid']);
    		if( strlen($yue_open_id)<1 )
    		{
    			$user_obj->logout(); //登出
    			echo "<script>
		            parent.document.getElementById('error_tips').style.display = 'block';
		            parent.document.getElementById('err_tips_txt').innerHTML = '请先到微信授权';
		            parent.document.getElementById('submit_btn_txt').innerHTML = '登录';
		            </script>";
    			exit();
    		}
    		
    		$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
    		$check_open_id_bind = $bind_weixin_obj->get_bind_info_by_open_id($yue_open_id);
    		
    		//检查微信是否有绑定过别的用户ID
    		if( $check_open_id_bind && $check_open_id_bind['user_id']!=$ret ) 
    		{
    			$user_obj->logout(); //登出
    			echo "<script>
		            parent.document.getElementById('error_tips').style.display = 'block';
		            parent.document.getElementById('err_tips_txt').innerHTML = '该微信号已绑定过约约了';
		            parent.document.getElementById('submit_btn_txt').innerHTML = '登录';
		            </script>";
			    exit();
    		}
    		
    		//检查用户是否有绑定过别的微信
    		$check_user_bind = $bind_weixin_obj->get_bind_info_by_user_id($ret);
    		if( $check_user_bind && $check_user_bind['open_id']!=$yue_open_id )
    		{
    			$user_obj->logout(); //登出
    			echo "<script>
		            parent.document.getElementById('error_tips').style.display = 'block';
		            parent.document.getElementById('err_tips_txt').innerHTML = '该约约号已绑定过微信了';
		            parent.document.getElementById('submit_btn_txt').innerHTML = '登录';
		            </script>";
    			exit();
    		}
    		
    		//找不到用户ID并且找不到OPENID就去绑定
    		if( !$check_user_bind && !$check_open_id_bind )
    		{
    			$bind_data = array();
    			$bind_data['user_id'] = $ret;
    			$bind_data['open_id'] = $yue_open_id;
    			$affected_rows = $bind_weixin_obj->add_bind($bind_data);
    			if( $affected_rows )
    			{
    				$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
    				$weixin_user_info = $weixin_pub_obj->get_weixin_user($yue_open_id);
    				$cellphone = $user_obj->get_phone_by_user_id($ret);
    				
    				$template_code = 'G_PAI_WEIXIN_USER_BIND';
    				$data = array('nickname'=>$weixin_user_info['nickname'], 'cellphone'=>$cellphone);
    				$weixin_pub_obj->message_template_send_by_user_id($ret, $template_code, $data);
    			}
    		}
    	}
    	
        if(!empty($r_url))
        {
            //对回链做匹配处理
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
            $seller_info=$mall_obj->get_seller_info($ret,2);

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
            parent.document.getElementById('err_tips_txt').innerHTML = '登录出错，登录号或者密码错误';
            parent.document.getElementById('submit_btn_txt').innerHTML = '登录';
            </script>";
        exit();
    }
}
else if($action_mode=="ajax")//异步提交
{
    $ajax_status = 1;
    if(empty($phone) || $password=="")
    {
        $ajax_status = 0;
        $res = "phone or password error";

    }

    //进行登录
    if(!empty($phone) && !empty($password))
    {
        $ret = $user_obj->user_login($phone, $password);
        if($ret>0)
        {
            $res = "success";
        }
        else
        {
            $ajax_status = 0;
            $res = "phone or password error";
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