<?php
/** 
 * �󶨸�ע����ҳ
 * 
 * author ����
 * 
 * 2015-1-14
 */


include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
$user_obj = POCO::singleton ( 'pai_user_class' );
//�����֤��
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

//У�鷵�����ӽṹ������ȫ����
if(!empty($r_url) || $r_url!="")
{
    $parse_url_arr = parse_url($r_url);

    if($parse_url_arr['scheme']!="http")
    {
        echo "<script>
               parent.alert('�������Ӳ��������ӽṹ');
               parent.location.href='http://www.yueus.com/reg/login.php';
            </script>";
        exit();
    }
    if(!preg_match("/\.yueus\.com/",$parse_url_arr['host']))
    {
        echo "<script>
               parent.alert('�������Ӳ���yueus����������');
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
            parent.document.getElementById('yue_error_tips').innerHTML = '�ֻ��Ż������벻��Ϊ��Ŷ';
            parent.document.getElementById('yue_submit_btn').innerHTML = '��¼'; 
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
            parent.document.getElementById('yue_error_tips').innerHTML = '��¼�����ֻ��Ż����������';
            parent.document.getElementById('yue_submit_btn').innerHTML = '��¼'; 
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
            parent.document.getElementById('supplier_yue_error_tips').innerHTML = '�̼�ID�������벻��Ϊ��';
            parent.document.getElementById('yue_submit_btn').innerHTML = '��¼'; 
            </script>";
            exit();
        }
        define('YUE_LOGIN_ORGANIZATION',1);
        $get_all_profile_obj = POCO::singleton('pai_task_profile_class');
        $is_supplier = $get_all_profile_obj->check_seller_by_user_id($yue_id);
        if (!$is_supplier) 
        {
            //������
            echo "<script>
            parent.document.getElementById('supplier_yue_error_tips').style.display = 'block';
            parent.document.getElementById('supplier_yue_error_tips').innerHTML = '��¼������ID�����̼Һŵ�ID';
            parent.document.getElementById('supplier_yue_submit_btn').innerHTML = '��¼'; 
            </script>";
            exit();
            
        }
        else
        {
            
            //��yueid�ֻ��ŵ�¼
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
                parent.document.getElementById('supplier_yue_error_tips').innerHTML = '��¼�����̼�ID�Ż����������';
                parent.document.getElementById('supplier_yue_submit_btn').innerHTML = '��¼'; 
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
        parent.document.getElementById('yue_error_tips').innerHTML = '��ѡ���ɫ';
        parent.document.getElementById('yue_submit_btn').innerHTML = '����ע��'; 
        </script>";
        exit();
    }
    
    
    if(empty($yue_phone) || $yue_password=="" || empty($yue_active_word))
    {
        echo "<script>
        parent.document.getElementById('yue_error_tips').style.display = 'block';
        parent.document.getElementById('yue_error_tips').innerHTML = '�ֻ���,�������֤���ʽ����';
        parent.document.getElementById('yue_submit_btn').innerHTML = '����ע��'; 
        </script>";
        exit();
    }
    
    //�ֻ��Ƿ���ע���ԼԼ
    $ret = $user_obj->check_cellphone_exist($yue_phone);
    if($ret)
    {
        //����
        echo "<script>
        parent.document.getElementById('yue_error_tips').style.display = 'block';
        parent.document.getElementById('yue_error_tips').innerHTML = 'ע��ʧ�ܣ����ֻ����Ѿ�ע���ԼԼ';
        parent.document.getElementById('yue_submit_btn').innerHTML = '����ע��'; 
        </script>";
        exit();
    }
    else
    {

        //�����֤��
        if($yue_active_word>0)
        {
            //У����֤���Ƿ���ȷ
            $ret = $pai_sms_obj->check_phone_reg_verify_code ( $yue_phone, $yue_active_word, 0, true );
            if(!$ret)
            {
                //����
                echo "<script>
                parent.document.getElementById('yue_error_tips').style.display = 'block';
                parent.document.getElementById('yue_error_tips').innerHTML = 'ע��ʧ�ܣ���֤�����';
                parent.document.getElementById('yue_submit_btn').innerHTML = '����ע��';
                </script>";
                exit();
                
            }
        
        
            //ûע���ԼԼ

            $user_info_arr ['pwd'] = $yue_password;
            $user_info_arr ['cellphone'] = $yue_phone;
            $user_info_arr ['nickname'] ="�ֻ��û�".substr($yue_phone,-4);;
            $user_info_arr ['role'] = $yue_role;
            $member_id = $user_obj->create_account($user_info_arr, $err_msg);//�����û�

            
            
            //echo $ret;
            if($member_id>0)
            {
                //����cookie
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
                parent.document.getElementById('yue_error_tips').innerHTML = 'ע���쳣�����Ժ�����';
                parent.document.getElementById('yue_submit_btn').innerHTML = '����ע��';
                </script>";
                exit();
            }
        }
        else
        {
            echo "<script>
            parent.document.getElementById('yue_error_tips').style.display = 'block';
            parent.document.getElementById('yue_error_tips').innerHTML = 'ע��ʧ�ܣ���֤��Ϊ��';
            parent.document.getElementById('yue_submit_btn').innerHTML = '����ע��';
            </script>";
            exit();
        }
    }
    
    
}
?>