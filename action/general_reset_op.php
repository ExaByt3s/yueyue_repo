<?php

/**
 * ԼԼ��������ҳ��form�����첽��
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

if($action_mode=="form")
{
    if(empty($phone) || $password=="" || empty($active_word))
    {
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '�ֻ���,�������֤���ʽ����';
         parent.document.getElementById('submit_btn').innerHTML = 'ȷ���޸�';
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
         parent.document.getElementById('submit_btn').innerHTML = 'ȷ���޸�';
         </script>";
        exit();
    }
    if($pwd_len>=32)
    {
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '���벻�ܴ��ڵ���32λ';
         parent.document.getElementById('submit_btn').innerHTML = 'ȷ���޸�';
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
         parent.document.getElementById('submit_btn').innerHTML = 'ȷ���޸�';
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
         parent.document.getElementById('submit_btn').innerHTML = 'ȷ���޸�';
         </script>";
        exit();
    }



    //�ֻ��Ƿ���ע���ԼԼ
    $ret = $user_obj->check_cellphone_exist($phone);
    if($ret)
    {



        //�����֤��
        if($active_word>0)
        {
            //У����֤���Ƿ���ȷ
            $group_key = 'G_PAI_USER_PASSWORD_VERIFY';
            $ret = $pai_sms_obj->check_verify_code ( $phone, $group_key,$active_word);
            if(!$ret)
            {
                //����
                echo "<script>
                    parent.document.getElementById('error_tips').style.display = 'block';
                    parent.document.getElementById('err_tips_txt').innerHTML = '�޸�ʧ�ܣ���֤�����';
                    parent.document.getElementById('submit_btn').innerHTML = '�����޸�';
                    </script>";
                exit();

            }

            $ret = $user_obj->update_pwd_by_phone ( $phone, $password );
            if(!$ret)
            {
                echo "<script>
                    parent.document.getElementById('error_tips').style.display = 'block';
                    parent.document.getElementById('err_tips_txt').innerHTML = '�޸�ʧ��';
                    parent.document.getElementById('submit_btn').innerHTML = '�����޸�';
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
              parent.document.getElementById('err_tips_txt').innerHTML = 'ע��ʧ�ܣ���֤��Ϊ��';
              parent.document.getElementById('submit_btn').innerHTML = '����ע��';
              </script>";
            exit();
        }
    }
    else
    {
        //����
        echo "<script>
         parent.document.getElementById('error_tips').style.display = 'block';
         parent.document.getElementById('err_tips_txt').innerHTML = '���ֻ���û��ע��ԼԼ';
         parent.document.getElementById('submit_btn').innerHTML = 'ȷ���޸�';
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

    //��������Ƿ񳬹���λ
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

            //�ֻ��Ƿ���ע���ԼԼ
            $ret = $user_obj->check_cellphone_exist($phone);
            if($ret)
            {
                //�����֤��
                if($active_word>0)
                {
                    //У����֤���Ƿ���ȷ
                    $group_key = 'G_PAI_USER_PASSWORD_VERIFY';
                    $ret = $pai_sms_obj->check_verify_code ( $phone, $group_key, $active_word);
                    if(!$ret)
                    {
                        //����
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