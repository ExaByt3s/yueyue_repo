<meta charset="gb2312">
<title>POCOԼ??</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no, email=no">

<!-- QQ -->
<meta name="x5-fullscreen" content="true">
<!-- UC -->
<meta name="full-screen" content="yes">

<?php
if ($_obj['page']['mode'] == "dev"){
?>
<link charset="utf-8" rel="stylesheet" href="/mobile/css/pai/paiappstyle.css?<?php
echo $_obj['page']['time'];
?>
">
<style>
    #seajs-debug-console {
        display: none!important;
    }
	
	
</style>
<!-- ELSEIF page.mode="test"  -->
<link charset="utf-8" rel="stylesheet" href="/mobile/css/pai/paiappstyle.css}">
<?php
} else {
?>
<link charset="utf-8" rel="stylesheet" href="/mobile/css/pai/paiappstyle.css?<?php
echo $_obj['page']['time'];
?>
">
<?php
}
?>