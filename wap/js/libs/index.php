<?php
/**
 * �Զ��ϲ�JS �� CSS �ļ�
 * @author kidney
 * @example http://www.poco.cn/css_common/v3/??header/header.min.css,footer/footer.min.css&t=201109201538.css
 * 
 * NOTES:
 * Develop Log��
 *   - 2011-09-23 ����ͨ�����Ӵ��룬�Զ������� ����GZIPѹ�������Ͳ���Ϊno_gzip=1
 * 		�磺http://www.poco.cn/css_common/v3/??header/header.min.css,footer/footer.min.css&no_gzip=1
 * 
 *   - 2011-09-20 ����ͨ�����Ӵ��룬�Զ������� ����޸�ʱ�䣬���Ͳ���Ϊt=����.�ļ�����
 * 		�磺http://www.poco.cn/css_common/v3/??header/header.min.css,footer/footer.min.css&t=201109201538.css
 */
/*** ��ȥ�°� ***/
$tmp_prefix = $_SERVER['SCRIPT_NAME']; // php�ű���(����·��) return:/js_common/index.php
$tmp_prefix = str_replace('index.php', '', $tmp_prefix); // return: /js_common/
$tmp_split_a = explode("??", $_SERVER['REQUEST_URI']); // php���ݲ��� EX:/js_common/index.php??mootools/mt_more/cmt.min.js,mootools/mt_more/itemFav.min.js&t=12232132.css
$tmp_split_a = $tmp_split_a[1]; 



if (preg_match('/[,\/]/', $tmp_split_a))
{
	
	include_once('/disk/data/htdocs232/poco/pai/mobile/combo/old_combo.php');
}
else 
{

	include_once('/disk/data/htdocs232/poco/pai/mobile/combo/combo.php');
}
?>