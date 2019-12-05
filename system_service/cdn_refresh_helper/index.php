<?

include_once ("cdn_refresh_helper_class.inc.php");

$urls = trim ( $_REQUEST ["urls"] );
$attemper = trim ( $_REQUEST ["attemper"] ) * 1;
if (! empty ( $urls )) {
	$cdn_refresh_helper_obj = new cdn_refresh_helper_class ();
	
	$urls = str_replace ( "\r", "", $urls );
	$urls_arr = explode ( "\n", $urls );
	$cdn_refresh_helper_obj->refresh_urls ( $urls_arr, true, $attemper * 1 );
	
	echo "刷新成功!" . date ( "Y-m-d H:i:s" );
}
?>

<form id="form1" name="form1" method="post" action=""><label>urls，多个url用换行分割<br />
<textarea name="urls" id="urls" cols="99" rows="30"><?=$urls;?> 
</textarea><input type="hidden" name="attemper" id="attemper"
	value=<?=$attemper?>> </label> <br />
<label>提交 <input type="submit" name="button" id="button" value="Submit"
	onclick="this.value='wait...';" />
	<input type="checkbox" name="_debug"/>调试
	</label></form>
