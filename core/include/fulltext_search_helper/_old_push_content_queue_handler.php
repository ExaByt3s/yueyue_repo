<?
/**
 * ȫ�����������ݶ��д������
 * 
 * �� 59.39.59.14 cronÿСʱ����һ��
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