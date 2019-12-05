<?
/** 帐号服务中心后台	公共文件
* @author ERLDY
*/

@define("G_DB_GET_REALTIME_DATA",1);

include_once(realpath(dirname(__FILE__))."/location_common.inc.php");
include_once(G_LOCATION_PATH."location_tpl_cfg.inc.php");


$webcheck_patch = "/disk/data/htdocs232/poco/webcheck/";
require_once $webcheck_patch."admin_function.php";


admin_check("common", "check");

?>