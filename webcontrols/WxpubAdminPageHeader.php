<?php

function _ctlWxpubAdminPageHeader($attribs)
{
    global $tpl;
    global $my_app_pai;
    global $yue_login_id;
    $JS_FILES = array(

    	array('js_link'=>'http://yp.yueus.com/yue_admin/js/jquery.min.js'),
    	array('js_link'=>'http://yp.yueus.com/yue_admin/js/admin.js'),
    	array('js_link'=>'http://yp.yueus.com/yue_admin/wxpub/js/admin.js')

    );
	$STYLE_FILES = array(

		array('css_link'=>'http://yp.yueus.com/yue_admin/css/style.css')

    );
    $user_obj        = POCO::singleton('pai_user_class');  
    $cur_user_name   = $user_obj->get_user_nickname_by_user_id($yue_login_id);
    $tpl->assign('JS_FILES', $JS_FILES);
    $tpl->assign('STYLE_FILES', $STYLE_FILES);
    $tpl->assign('_dev',time());
    $header_tpl	 = $my_app_pai->getView('./webcontrols/WxpubAdminPageHeader.tpl.htm',true);
    $header_tpl->assign('cur_user_name',$cur_user_name);
    $header_html = $header_tpl->result();
    return $header_html;

}
?>