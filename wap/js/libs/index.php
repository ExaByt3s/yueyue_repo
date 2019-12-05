<?php
/**
 * 自动合并JS 和 CSS 文件
 * @author kidney
 * @example http://www.poco.cn/css_common/v3/??header/header.min.css,footer/footer.min.css&t=201109201538.css
 * 
 * NOTES:
 * Develop Log：
 *   - 2011-09-23 可以通过链接传入，自定义设置 禁用GZIP压缩，传送参数为no_gzip=1
 * 		如：http://www.poco.cn/css_common/v3/??header/header.min.css,footer/footer.min.css&no_gzip=1
 * 
 *   - 2011-09-20 可以通过链接传入，自定义设置 最后修改时间，传送参数为t=日期.文件类型
 * 		如：http://www.poco.cn/css_common/v3/??header/header.min.css,footer/footer.min.css&t=201109201538.css
 */
/*** 跳去新版 ***/
$tmp_prefix = $_SERVER['SCRIPT_NAME']; // php脚本名(绝对路径) return:/js_common/index.php
$tmp_prefix = str_replace('index.php', '', $tmp_prefix); // return: /js_common/
$tmp_split_a = explode("??", $_SERVER['REQUEST_URI']); // php传递参数 EX:/js_common/index.php??mootools/mt_more/cmt.min.js,mootools/mt_more/itemFav.min.js&t=12232132.css
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