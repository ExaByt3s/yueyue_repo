<?php

/** 
  * 头部 bar
  * 汤圆
  * 2015-6-5
  * 引用资源文件定位，注意！确保引用路径争取
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



// 此为全站登陆注册头部的bar
function _get_wbc_global_top_bar($attribs)
{   

    $file_dir = dirname(__FILE__);
    
    global $my_app_pai;
    global $yue_login_id;

    if(preg_match('/yueus.com/',$_SERVER['HTTP_HOST']))
    {
        $tpl     = $my_app_pai->getView($file_dir . "/global-top-bar.tpl.htm",true);

        if(defined("opacity"))
        {
            $tpl->assign('opacity', 1);
        }
        
    }
    else
    {
        $tpl = new SmartTemplate($file_dir . "/global-top-bar.tpl.htm");

        if(defined("opacity"))
        {
             $tpl->assign('opacity', 1);
        }
        
    }   

    $tpl_html = $tpl->result();

    return $tpl_html;
}



// 此为消费者pc版本头部的bar
function _get_consumers_global_top_bar($attribs)
{   

    $file_dir = dirname(__FILE__);
    
    global $my_app_pai;
    global $yue_login_id;

    if(preg_match('/yueus.com/',$_SERVER['HTTP_HOST']))
    {
        $tpl     = $my_app_pai->getView($file_dir . "/global-top-bar-consumers.tpl.htm",true);

        if(defined("opacity"))
        {
            $tpl->assign('opacity', 1);
        }
        
    }
    else
    {
        $tpl = new SmartTemplate($file_dir . "/global-top-bar-consumers.tpl.htm");

        if(defined("opacity"))
        {
             $tpl->assign('opacity', 1);
        }
        
    }   

    $tpl_html = $tpl->result();

    return $tpl_html;
}

?>