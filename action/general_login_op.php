<?php

/**
 * ԼԼ��¼����ҳ��form�����첽��
 *
 * author       ����
 *
 * 2015-6-9
 */


include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/basics.fun.php');
$user_obj = POCO::singleton ( 'pai_user_class' );

$action_mode = trim($_INPUT['action_mode']);//�ύ�ķ�ʽ��form�����첽

$phone = (int)$_INPUT['phone'];
$password = trim($_INPUT['password']);
$r_url = trim($_INPUT['r_url']);//��Դ��ַ

//��־
pai_log_class::add_log(array(), 'general_login_op', 'general_login');

if($action_mode=="form")//form�ύ
{
    if(empty($phone) || $password=="")
    {
        echo "<script>
            parent.document.getElementById('error_tips').style.display = 'block';
            parent.document.getElementById('err_tips_txt').innerHTML = '��¼�Ż������벻��Ϊ��Ŷ';
            parent.document.getElementById('submit_btn_txt').innerHTML = '��¼';
            </script>";
        exit();
    }
    $ret = $user_obj->user_login($phone, $password);
    if($ret>0)
    {
    	//΢�ŵ�¼��������ڰ󶨵�����
    	$is_weixin_tmp = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
    	if( $is_weixin_tmp )
    	{
    		$yue_open_id = trim($_COOKIE['yueus_openid']);
    		if( strlen($yue_open_id)<1 )
    		{
    			$user_obj->logout(); //�ǳ�
    			echo "<script>
		            parent.document.getElementById('error_tips').style.display = 'block';
		            parent.document.getElementById('err_tips_txt').innerHTML = '���ȵ�΢����Ȩ';
		            parent.document.getElementById('submit_btn_txt').innerHTML = '��¼';
		            </script>";
    			exit();
    		}
    		
    		$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
    		$check_open_id_bind = $bind_weixin_obj->get_bind_info_by_open_id($yue_open_id);
    		
    		//���΢���Ƿ��а󶨹�����û�ID
    		if( $check_open_id_bind && $check_open_id_bind['user_id']!=$ret ) 
    		{
    			$user_obj->logout(); //�ǳ�
    			echo "<script>
		            parent.document.getElementById('error_tips').style.display = 'block';
		            parent.document.getElementById('err_tips_txt').innerHTML = '��΢�ź��Ѱ󶨹�ԼԼ��';
		            parent.document.getElementById('submit_btn_txt').innerHTML = '��¼';
		            </script>";
			    exit();
    		}
    		
    		//����û��Ƿ��а󶨹����΢��
    		$check_user_bind = $bind_weixin_obj->get_bind_info_by_user_id($ret);
    		if( $check_user_bind && $check_user_bind['open_id']!=$yue_open_id )
    		{
    			$user_obj->logout(); //�ǳ�
    			echo "<script>
		            parent.document.getElementById('error_tips').style.display = 'block';
		            parent.document.getElementById('err_tips_txt').innerHTML = '��ԼԼ���Ѱ󶨹�΢����';
		            parent.document.getElementById('submit_btn_txt').innerHTML = '��¼';
		            </script>";
    			exit();
    		}
    		
    		//�Ҳ����û�ID�����Ҳ���OPENID��ȥ��
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
            //�Ի�����ƥ�䴦��
            //form�ύУ�鷵�����ӽṹ������ȫ����

                $parse_url_arr = parse_url($r_url);
                if($parse_url_arr['scheme']!="http")
                {
                    echo "<script>
                       parent.alert('�������Ӳ��������ӽṹ');
                       parent.location.href='http://www.yueus.com/';
                       </script>";
                    exit();
                }

                if(!preg_match("/\.yueus\.com/",$parse_url_arr['host']))
                {
                     echo "<script>
                       parent.alert('�������Ӳ���yueus����������');
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
            //û���������to do
            //�жϵ�ǰ��¼���Ƿ��̼�
            $mall_obj = POCO::singleton('pai_mall_seller_class');
            $seller_info=$mall_obj->get_seller_info($ret,2);

            //ע�͵�ҳ��ȫ��ʱ���to_do
            if(!empty($seller_info))
            {
                //�̼���ȥ�̼�ҳ
                echo "<script>
                  parent.location.href='http://s.yueus.com/';
                  </script>";
                exit();
            }
            else
            {
                //Ĭ����ȥ��ҳ
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
            parent.document.getElementById('err_tips_txt').innerHTML = '��¼������¼�Ż����������';
            parent.document.getElementById('submit_btn_txt').innerHTML = '��¼';
            </script>";
        exit();
    }
}
else if($action_mode=="ajax")//�첽�ύ
{
    $ajax_status = 1;
    if(empty($phone) || $password=="")
    {
        $ajax_status = 0;
        $res = "phone or password error";

    }

    //���е�¼
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

    //ajax�ύУ�鷵�����ӽṹ������ȫ����
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