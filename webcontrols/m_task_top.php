<?php

function _ctlm_task_top($attribs)
{
    global $tpl;
    global $my_app_pai;
    global $yue_login_id;

    $header_tpl	 = $my_app_pai->getView('./webcontrols/m_task_top.htm',true);

	$header_tpl ->assign('yue_id',$yue_login_id);
    $header_tpl ->assign('_dev',time());
    $header_tpl ->assign("rand",date("YmdHis"));

    //дљЫЭЩњвтПЈ
    if( $yue_login_id>0 )
    {
    	$ref_id = strtotime(date('Y-m-d 00:00:00'));
    	$task_coin_obj = POCO::singleton('pai_task_coin_class');
    	$task_coin_obj->submit_give('SELLER_LOGIN_TODAY', $yue_login_id, $ref_id);
    }
    
    $header_html = $header_tpl->result();
    return $header_html;

}
?>