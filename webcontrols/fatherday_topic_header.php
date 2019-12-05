<?php
/**
 * 父亲节   专题公共头
 * ram $attribs 控件的参数
 *
 * @return string
 */

function _ctlfatherday_topic_header($attribs)
{
    global $tpl,$my_app_pai;
    $event_comm_css = array(

        '/disk/data/htdocs233/poco_main/css_common/v3/reset.css',
        '/disk/data/htdocs233/poco_main/css_common/v3/global.css',
        '/disk/data/htdocs233/poco_main/css_common/v3/header/header.css',
        '/disk/data/htdocs233/poco_main/css_common/v3/footer/footer.css',
        '/disk/data/htdocs233/poco_main/css_common/v3/layout.css',
        '/disk/data/htdocs233/poco_main/css_common/v3/common.css',
        '/disk/data/htdocs232/poco/event/css/action.css'

    );

    $header_tpl	 = $my_app_pai->getView('/disk/data/htdocs232/poco/pai/webcontrols/fatherday_topic_header.tpl.htm',true);
    $header_html = $header_tpl->result();
    return $header_html;

}


?>