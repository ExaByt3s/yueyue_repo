<?php

/**
 * ԼԼע�ᴦ��ҳ��form�����첽��
 *
 *
 * author    ����
 *
 *
 * 2015-6-9
 */

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/basics.fun.php');
$user_obj = POCO::singleton ( 'pai_user_class' );
//�����֤��
$pai_sms_obj = POCO::singleton ( 'pai_sms_class' );

$action_mode = trim($_INPUT['action_mode']);//�ύ�ķ�ʽ��form�����첽

$phone = (int)$_INPUT['phone'];
$password = trim($_INPUT['password']);
$r_url = trim($_INPUT['r_url']);//��Դ��ַ
$active_word = (int)$_INPUT['active_word'];//������
$role = trim($_INPUT['role']);
$role = "cameraman";
$yue_open_id = trim($_COOKIE['yueus_openid']);


if($action_mode=="form")
{
    if(empty($phone) || $password=="" || empty($active_word))
    {
       echo "<script>
        parent.document.getElementById('error_tips').style.display = 'block';
        parent.document.getElementById('err_tips_txt').innerHTML = '�ֻ���,�������֤���ʽ����';
        parent.document.getElementById('submit_btn').innerHTML = '����ע��';
        </script>";
       exit();
    }

    //��������Ƿ񳬹���λ������32λ
    $pwd_len = strlen($password);
    if($pwd_len<6)
    {
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '���벻������6λ';
         parent.document.getElementById('submit_btn').innerHTML = '����ע��';
         </script>";
        exit();
    }
    if($pwd_len>=32)
    {
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '���벻�ܴ��ڵ���32λ';
         parent.document.getElementById('submit_btn').innerHTML = '����ע��';
         </script>";
        exit();
    }
    //�ж����벻��ȫΪ����
    $is_all_num_res = is_numeric($password);
    if($is_all_num_res)
    {
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '���벻��ȫΪ����';
         parent.document.getElementById('submit_btn').innerHTML = '����ע��';
         </script>";
        exit();
    }
    //�ж����벻��������
    $is_ch_res = preg_match("/[\x7f-\xff]/", $password);
    if($is_ch_res)
    {
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '���벻��������';
         parent.document.getElementById('submit_btn').innerHTML = '����ע��';
         </script>";
        exit();
    }


    $user_info = $user_obj->get_user_by_phone ( $phone );
	//����û��Ƿ�Ԥ�ȵ����
	if ($user_info ['pwd_hash'] != 'poco_model_db')
	{
		//�ֻ��Ƿ���ע���ԼԼ
       	 $ret = $user_obj->check_cellphone_exist($phone);
	}

    if($ret)
    {
        //����
       echo "<script>
        parent.document.getElementById('error_tips').style.display = 'block';
        parent.document.getElementById('err_tips_txt').innerHTML = 'ע���ʧ�ܣ����ֻ����Ѿ�ע���ԼԼ';
        parent.document.getElementById('submit_btn').innerHTML = '����ע��';
        </script>";
       exit();
    }
    else
    {
    	//΢�ŵ�¼��������ڰ󶨵�����
    	$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
    	if( $__is_weixin )
    	{
    		if( strlen($yue_open_id)<1 )
    		{
    			echo "<script>
			        parent.document.getElementById('error_tips').style.display = 'block';
			        parent.document.getElementById('err_tips_txt').innerHTML = 'ע���ʧ�ܣ����ȵ�΢����Ȩ';
			        parent.document.getElementById('submit_btn').innerHTML = '����ע��';
			        </script>";
    			exit();
    		}
    		
    		//���΢�ź��Ƿ��Ѱ�ԼԼ
    		$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
    		$check_open_id_bind = $bind_weixin_obj->get_bind_info_by_open_id($yue_open_id);
    		if( $check_open_id_bind )
    		{
    			echo "<script>
			        parent.document.getElementById('error_tips').style.display = 'block';
			        parent.document.getElementById('err_tips_txt').innerHTML = 'ע���ʧ�ܣ���΢�ź��Ѱ󶨹�ԼԼ��';
			        parent.document.getElementById('submit_btn').innerHTML = '����ע��';
			        </script>";
    			exit();
    		}
    	}
    	
        //�����֤��
        if($active_word>0)
        {
            //У����֤���Ƿ���ȷ
            $ret = $pai_sms_obj->check_phone_reg_verify_code ( $phone, $active_word, 0, true );
            if(!$ret)
            {
              //����
              echo "<script>
                parent.document.getElementById('error_tips').style.display = 'block';
                parent.document.getElementById('err_tips_txt').innerHTML = 'ע��ʧ�ܣ���֤�����';
                parent.document.getElementById('submit_btn').innerHTML = '����ע��';
                </script>";
              exit();

            }

			$reg_from_tmp = 'pc';
            $nickname_tmp = "�ֻ��û�".substr($phone,-4);
            //΢�ŵ�¼��������ڰ󶨵�����
            if( $__is_weixin )
            {
            	$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
            	$weixin_user_info = $weixin_pub_obj->get_weixin_user($yue_open_id);
            	$nickname_tmp = trim($weixin_user_info['nickname']);
            	if( empty($weixin_user_info['nickname']) )
            	{
            		$nickname_tmp = "΢���û�";
            	}
				$reg_from_tmp = 'weixin';
            }
            
            //ûע���ԼԼ
            $user_info_arr ['pwd'] = $password;
            $user_info_arr ['cellphone'] = $phone;
            $user_info_arr ['nickname'] = $nickname_tmp;
            $user_info_arr ['role'] = $role;
            $user_info_arr ['reg_from'] = $reg_from_tmp;
            $member_id = $user_obj->create_mall_account($user_info_arr, $err_msg);//�����û�


            //echo $ret;
            if($member_id>0)
            {
            	//����cookie
            	$user_obj->load_member($member_id, $b_hide_online = null);
            	
            	//΢�ŵ�¼��������ڰ󶨵�����
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
            			//ͬ��΢��ͷ��
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
                    $seller_info=$mall_obj->get_seller_info($member_id,2);

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
                 parent.document.getElementById('err_tips_txt').innerHTML = 'ע���쳣�����Ժ�����';
                 parent.document.getElementById('submit_btn').innerHTML = '����ע��';
                 </script>";
               exit();
            }
        }
        else
        {
             echo "<script>
              parent.document.getElementById('error_tips').style.display = 'block';
              parent.document.getElementById('err_tips_txt').innerHTML = 'ע��ʧ�ܣ���֤������';
              parent.document.getElementById('submit_btn').innerHTML = '����ע��';
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
		//����û��Ƿ�Ԥ�ȵ����
		if ($user_info ['pwd_hash'] != 'poco_model_db')
		{
			//�ֻ��Ƿ���ע���ԼԼ
       		 $ret = $user_obj->check_cellphone_exist($phone);
		}
		
        if($ret)
        {
            //����
            $ajax_status = 0;
            $res = "phone_exist error";
        }
        else
        {

            //�����֤��
            if($active_word>0)
            {
                //У����֤���Ƿ���ȷ
                $ret = $pai_sms_obj->check_phone_reg_verify_code ( $phone, $active_word, 0, true );
                if(!$ret)
                {
                    //����
                    $ajax_status = 0;
                    $res = "active_word error";

                }
                else
                {

                    //ûע���ԼԼ

                    $user_info_arr ['pwd'] = $password;
                    $user_info_arr ['cellphone'] = $phone;
                    $user_info_arr ['nickname'] ="�ֻ��û�".substr($phone,-4);;
                    $user_info_arr ['role'] = $role;
                    $user_info_arr ['reg_from'] = "pc";
                    
                    $member_id = $user_obj->create_mall_account($user_info_arr, $err_msg);//�����û�


                    //echo $ret;
                    if($member_id>0)
                    {
                        //����cookie
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