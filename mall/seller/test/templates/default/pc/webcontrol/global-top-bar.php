<?php

/** 
  * ͷ�� bar
  * ��Բ
  * 2015-6-5
  * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

function _get_wbc_global_top_bar($attribs = array())
{   

    $file_dir = dirname(__FILE__);

    global $my_app_pai;
    global $yue_login_id;
    global $__yue_user_info;

    $user_id = $yue_login_id;

    //  �û���Ϣ
    $mall_obj = POCO::singleton('pai_mall_seller_class');
    $seller_info=$mall_obj->get_seller_info($user_id,2);
    $seller_name=$seller_info['seller_data']['name'];

    //�жϵ�ǰ�����Ƿ�������У�2015-9-15-by ����
    $mall_basic_check_obj = POCO::singleton('pai_mall_certificate_basic_class');
    $user_basic_status_list = $mall_basic_check_obj->get_person_status_by_user_id($user_id);
    if($user_basic_status_list['status']==0 || $user_basic_status_list['status']==2)
    {
        $basic_certificate = "show";//�����
    }
    //�жϵ�ǰ�����Ƿ�������У�2015-9-15-by ����

    if(preg_match('/yueus.com/',$_SERVER['HTTP_HOST']))
    {
        // ��Ϊ������
        if (empty($seller_name)) 
        {
            if($basic_certificate=="show")
            {
                $tpl     = $my_app_pai->getView($file_dir . "/global-top-bar-seller.tpl.htm",true);
            }
            else
            {
                $tpl     = $my_app_pai->getView($file_dir . "/global-top-bar-consumers.tpl.htm",true);
            }

        }
        else
        {
            $tpl     = $my_app_pai->getView($file_dir . "/global-top-bar-seller.tpl.htm",true);
        }

    }
    else
    {
        // ��Ϊ������
        if (empty($seller_name)) 
        {
            $tpl     = $my_app_pai->getView($file_dir . "/global-top-bar-consumers.tpl.htm",true);
        }
        else
        {
            $tpl     = $my_app_pai->getView($file_dir . "/global-top-bar-seller.tpl.htm",true);
        }

    }   

    // ���δ��¼����ʾ��¼ע��
    if (empty($yue_login_id)) 
    {
        $tpl->assign('no_login', 1);

        // ����
        $r_url_prev = $_SERVER['SERVER_NAME'];
        $r_url_next = $_SERVER['REQUEST_URI'];
        $r_url = "http://".$r_url_prev.$r_url_next;
        $tpl->assign('r_url', urlencode($r_url));
        // print_r($r_url);

    }

    // �˺����
    $pai_payment_obj = POCO::singleton('pai_payment_class');
    $user_available_balance = $pai_payment_obj->get_user_available_balance($user_id);
    $tpl->assign('user_available_balance', $user_available_balance);

    // ��Ϊ������
     if (empty($seller_name))
     {
         $user_name = $__yue_user_info['nickname'];
     }
     else
     {
         $user_name = $seller_name ;
     }


    //  ��ʾ�û���
    $tpl->assign('user_name', $user_name);

    //  ��Ŀ·��
    $tpl->assign('project_root', TASK_PROJECT_ROOT);    

    // ��ҳ��ַ����
    $tpl->assign('index_url_link', TASK_PROJECT_ROOT);  

    if(defined("index_value"))
    {
         $tpl->assign('index_value', 1);
    }      
    

    $tpl_html = $tpl->result();





    return $tpl_html;
}


?>