<?php
/**
 * Ӧ�ÿ�� Ĭ��β���ؼ�
 * 
 * @param $attribs �ؼ��Ĳ���
 * 
 * @return string
 * 
 */
function _ctlFooter($attribs = array())
{
	ob_start();
	//include_once("/disk/data/htdocs233/poco_main/index_include_v2/fooder_v6.php");
	$_MYPOCO_APP_DEFAULT_BOTTOM_HTML = ob_get_contents();
	ob_clean();
	
	return $_MYPOCO_APP_DEFAULT_BOTTOM_HTML;
}

?>