
<?php

/** 
  * ͷ�� bar
  * ��Բ
  * 2015-6-5
  * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

define('MALL_USER_DIR_APP',"../../../../");
include_once MALL_USER_DIR_APP.'common.inc.php';

// ��Ϊȫվ��½ע��ͷ����bar
function _get_wbc_global_top_bar($attribs)
{   

    $file_dir = dirname(__FILE__);
    
    global $my_app_pai;
    global $yue_login_id;


    //��ͨ���в�ѯ
    $ret_open_city = get_api_result('customer/sell_location.php',array(
        'user_id' => $yue_login_id,
        'type_id' => $type_id,
        'query' => $query
    ));

    // print_r($ret_open_city);
    $has_open_city = $ret_open_city['data']['service']['list']  ;

    // ��������
    $other_open_city = $ret_open_city['data']['other']['list']  ;



    if(preg_match('/yueus.com/',$_SERVER['HTTP_HOST']))
    {
        $tpl     = $my_app_pai->getView($file_dir . "/global-top-bar.tpl.htm",true);

        $tpl->assign('index_url', G_MALL_PROJECT_USER_INDEX_DOMAIN);

        $r_url =  'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $tpl->assign('r_url', $r_url);



        // �ж��Ƿ��¼
        if ($yue_login_id) 
        {
            $tpl->assign('is_login', 1);
        }

        $location_id_cookie = $_COOKIE['yue_location_id'] ;

        // �����cookie����ȡcookie �����ȡphp����
        if ($location_id_cookie) 
        {
            $location_id = $location_id_cookie;
            $location_arr = get_poco_location_name_by_location_id ( $location_id,true,true );
            $city =  $location_arr['level_1']['name'];
            $city = str_replace("��","", $city );
            $gps_cur = "��ǰѡ�����";
        }
        else
        {
            // ����            
            $current_locate_info = POCO::execute('common.get_ip_location_info', get_client_ip());
            $gps_cur = "��ǰ��λ����";

            // ����жϵĳ����У������У������ȡĬ�Ϲ���
            if (count($current_locate_info) > 0) 
            {
                $city = $current_locate_info['city'];
                $location_id = $current_locate_info['location_id'];
            }
            else
            {
                $city = '����';
                $location_id = 101029001;
            }


            foreach ( $has_open_city as $k => $val ) 
            {
                $data_location_arr[$k] = $val['location_id'];
            }
            // print_r($data_location_arr);
            //�жϵ����Ƿ��ڿ�ͨ�ĳ��У�����Ĭ����ʾ����
            if (!in_array($location_id, $data_location_arr)) 
            {
                $city = '����';
                $location_id = 101029001;
            }

            $expire = time() + 3600*24*7; // 3600 ��Ч��1Сʱ
            setcookie ("yue_location_id", $location_id, $expire,'/', 'yueus.com' ); // ����һ������Ϊvar_name��cookie�����ƶ�����Ч��
        }




        $user_name = get_user_nickname_by_user_id($yue_login_id);


        
        $tpl->assign('city', $city);
        $tpl->assign('location_id', $location_id);
        $tpl->assign('gps_cur', $gps_cur);
        $tpl->assign('user_name', $user_name);

    }
    else
    {
        $tpl = new SmartTemplate($file_dir . "/global-top-bar.tpl.htm");
    }   

    if(defined("opacity"))
    {
         $tpl->assign('opacity', 1);
    }
    
    $tpl->assign('data', $has_open_city);


    $tpl->assign('other_open_city', $other_open_city);

    $tpl_html = $tpl->result();

    return $tpl_html;
}

