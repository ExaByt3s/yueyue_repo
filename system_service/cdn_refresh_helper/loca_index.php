<?
include_once ("cdn_refresh_helper_class.inc.php");
echo "<br /> \n\t <-- EXITCODE:0  LASTERROR:ABC --> \n\t <br />";
$urls = trim ( $_REQUEST ["urls"] );
$b_refresh_cdn = trim ( $_REQUEST ["b_refresh_cdn"] );
if (! empty ( $urls )) {
	$cdn_refresh_helper_obj = new cdn_refresh_helper_class ();
	$urls_arr = array(urldecode ( $urls ));
	$cdn_refresh_helper_obj->__refresh_urls ( $urls_arr, $b_refresh_cdn );
}
?>