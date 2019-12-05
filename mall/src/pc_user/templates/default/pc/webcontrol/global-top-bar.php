
<?php

/** 
  * 头部 bar
  * 汤圆
  * 2015-6-5
  * 引用资源文件定位，注意！确保引用路径争取
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

define('MALL_USER_DIR_APP',"../../../../");
include_once MALL_USER_DIR_APP.'common.inc.php';

// 此为全站登陆注册头部的bar
function _get_wbc_global_top_bar($attribs)
{   

    $file_dir = dirname(__FILE__);
    
    global $my_app_pai;
    global $yue_login_id;


    //开通城市查询
    $ret_open_city = get_api_result('customer/sell_location.php',array(
        'user_id' => $yue_login_id,
        'type_id' => $type_id,
        'query' => $query
    ));

    // print_r($ret_open_city);
    $has_open_city = $ret_open_city['data']['service']['list']  ;

    // 其他城市
    $other_open_city = $ret_open_city['data']['other']['list']  ;



    if(preg_match('/yueus.com/',$_SERVER['HTTP_HOST']))
    {
        $tpl     = $my_app_pai->getView($file_dir . "/global-top-bar.tpl.htm",true);

        $tpl->assign('index_url', G_MALL_PROJECT_USER_INDEX_DOMAIN);

        $r_url =  'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $tpl->assign('r_url', $r_url);



        // 判断是否登录
        if ($yue_login_id) 
        {
            $tpl->assign('is_login', 1);
        }

        $location_id_cookie = $_COOKIE['yue_location_id'] ;

        // 如果有cookie，读取cookie 否则读取php城市
        if ($location_id_cookie) 
        {
            $location_id = $location_id_cookie;
            $location_arr = get_poco_location_name_by_location_id ( $location_id,true,true );
            $city =  $location_arr['level_1']['name'];
            $city = str_replace("市","", $city );
            $gps_cur = "当前选择城市";
        }
        else
        {
            // 地区            
            $current_locate_info = POCO::execute('common.get_ip_location_info', get_client_ip());
            $gps_cur = "当前定位城市";

            // 如果判断的出城市，读城市，否则读取默认广州
            if (count($current_locate_info) > 0) 
            {
                $city = $current_locate_info['city'];
                $location_id = $current_locate_info['location_id'];
            }
            else
            {
                $city = '广州';
                $location_id = 101029001;
            }


            foreach ( $has_open_city as $k => $val ) 
            {
                $data_location_arr[$k] = $val['location_id'];
            }
            // print_r($data_location_arr);
            //判断地区是否在开通的城市，否则默认显示广州
            if (!in_array($location_id, $data_location_arr)) 
            {
                $city = '广州';
                $location_id = 101029001;
            }

            $expire = time() + 3600*24*7; // 3600 有效期1小时
            setcookie ("yue_location_id", $location_id, $expire,'/', 'yueus.com' ); // 设置一个名字为var_name的cookie，并制定了有效期
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

