<?php

/** 
  * ͷ�� bar
  * ��Բ
  * 2015-6-5
  * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



// ��Ϊȫվ��½ע��ͷ����bar
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



// ��Ϊ������pc�汾ͷ����bar
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