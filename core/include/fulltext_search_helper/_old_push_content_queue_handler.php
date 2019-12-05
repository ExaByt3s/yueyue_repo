<?
/**
 * 全文搜索推数据队列处理程序
 * 
 * 在 59.39.59.14 cron每小时请求一次
 * 
 * @author Tony
 */

set_time_limit(1800);
ini_set("memory_limit","256M");
ignore_user_abort(true);


include_once("fulltext_search_helper_class.inc.php");

$fulltext_search_helper_obj = new fulltext_search_helper_class();
$fulltext_search_helper_obj->handle_queue();

?>