<?php

/** 
 * �������ַ��������ҳ
 * 
 * author ����
 * 
 * 2014-6-17
 */



function _ctlpai_local_choose($attribs)
{
    global $my_app_pai;
    global $login_id;


    $pai_local_choose_tpl = $my_app_pai->getView('./webcontrols/pai_local_choose.tpl.htm',true);


    return $pai_local_choose_tpl->result();
}
?>