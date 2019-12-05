<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/yue_access_control.inc.php');
$list = array('admin');
$auth = yueyue_admin_check ( 'wxpub', $list, 1 );
//print_r($auth);exit;
if (!$auth)
{
	header("Location:http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin%2Fwxpub%2Fmain.php");
}
if( empty( $_COOKIE['cur_bind_id'] ) && basename($_SERVER['PHP_SELF']) !='main.php' && basename($_SERVER['PHP_SELF']) !='bind.php' ){

	echo "<script>top.location='main.php'</script>";
	exit();

}

/**
 * 弹出提示
 * @param array|string $contents
 * @param string $to_url
 * @param string $win
 * @param string $title
 * @return array
 */
 function pop_msg($contents, $to_url='', $win='', $title='提示')
{
	$to_url = trim($to_url);
	$win = trim($win);
	$title = trim($title);
	if( strlen($win)<1 ) $win = 'parent';
	
	if( !is_array($contents) )
	{
		$contents = trim($contents);
		$contents = array( $contents );
	}
	$contents_html = implode('<br />', $contents);
	$contents_html = "<div style='padding:5px;'>" . $contents_html . '</div>';
	$contents_txt = implode("\r\n", $contents);
	
	$to_url_js = '';
	if( strlen($to_url)>0 ) $to_url_js = "window.{$win}.location.href=\"{$to_url}\";";
	
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gbk\">
	<script type=\"text/javascript\">
		try{
			window.{$win}.$.jBox.close('ecpay_admin_pop_msg');
			window.{$win}.$.jBox.closeTip();
			window.{$win}.$.jBox(\"{$contents_html}\", { 'id': 'ecpay_admin_pop_msg', 'title':'{$title}', 'closed': function (){
				{$to_url_js}
			}});
		}catch(e){
			alert(\"{$contents_txt}\");
			{$to_url_js}
		}
	</script>
	";
	exit();
}
?>