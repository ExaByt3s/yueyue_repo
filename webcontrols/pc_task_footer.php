<?php

function _ctlpc_task_footer($attribs)
{

    global $tpl;
    global $my_app_pai;
    global $yue_login_id;

    $footer_tpl	 = $my_app_pai->getView('./webcontrols/pc_task_footer.tpl.htm',true);
    $footer_html = $footer_tpl->result();
    return $footer_html;

}
?>