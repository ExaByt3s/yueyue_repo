<?php
/**
 * 活动头部控件
 * 
 * @param $attribs 控件的参数
 * 
 * @return string
 */

function _ctlPartyHeader($attribs)
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

        $event_comm_js = array(
        
            '/disk/data/htdocs233/poco_main/js_common/mootools/mootools.v1.24-core.js',
            '/disk/data/htdocs233/poco_main/js_common/common/poco_common_v2.js',
        
        );
        
        $tpl->assign('event_comm_css',$event_comm_css);
        $tpl->assign('event_comm_js',$event_comm_js);
        
        
    $header_tpl	 = $my_app_pai->getView('/disk/data/htdocs232/poco/pai/webcontrols/PartyHeader.tpl.htm',true);
    $header_html = $header_tpl->result();
    return $header_html;
}

?>