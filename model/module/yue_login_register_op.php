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



if($yue_action_type=="login")
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
        //�жϽ�ɫ
        $role_ret = $user_obj->check_role($ret);
        if($role_ret!="model")
        {
            echo "<script>
            parent.document.getElementById('yue_error_tips').style.display = 'block';
            parent.document.getElementById('yue_error_tips').innerHTML = '�õ�¼�û�����ģ�أ���ѡ��ʹ��ģ���˺ŵ�¼';
            parent.document.getElementById('yue_submit_btn').innerHTML = '��¼'; 
            </script>";
            //���˳�����
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
        parent.document.getElementById('yue_error_tips').innerHTML = '��¼�����ֻ��Ż����������';
        parent.document.getElementById('yue_submit_btn').innerHTML = '��¼'; 
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
        parent.document.getElementById('yue_error_tips').innerHTML = 'ע���ʧ�ܣ����ֻ����Ѿ�ע���ԼԼ';
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
            $user_info_arr ['role'] = "model";
            $member_id = $user_obj->create_account($user_info_arr, $err_msg);//�����û�

            
            
            //echo $ret;
            if($member_id>0)
            {
                //����cookie
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