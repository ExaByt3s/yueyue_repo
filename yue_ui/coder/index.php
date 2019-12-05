<?php

$seajs_combo = $_REQUEST['seajs_combo'];
$seajs_no_cache = $_REQUEST['seajs_no_cache'];
$seajs_uglify = $_REQUEST['seajs_uglify'];

/*if($seajs_combo==null)
{
	$seajs_combo = 1;
}*/

if($seajs_no_cache==null)
{
	$seajs_no_cache = 1;
}

?>
<!DOCTYPE >
<!-- html  manifest="manifest.php" -->
<html >
<head>
	<title>世界·POCO</title>

	<meta http-equiv="Content-Type" content="text/html;" />

	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"  />
    <meta name="viewport" content="initial-scale=1.0,user-scalable=no,maximum-scale=1" media="(device-height: 568px)" />
    
    
    
    <meta name="imagemode" content="force"/>

	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="images/app_icon-57x57.png" /> 
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/app_icon-72x72.png" /> 
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/app_icon-114x114.png" /> 
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="images/app_icon-144x144.png" /> 

    
	
	<!-- iPhone (Retina) -->
    <link href="images/start_up_640x920.png" media="(device-width: 320px) and (device-height: 480px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">

    <!-- iPhone 5 -->
    <link href="images/start_up_640x1096.png"  media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">

    <!-- iPad Portrait -->
    <link href="images/ipad_start_up_768x1004.png" media="(device-width: 768px) and (device-height: 1024px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 1)" rel="apple-touch-startup-image">

    <!-- iPad Landscape -->
    <link href="images/ipad_start_up_1024x748.png" media="(device-width: 768px) and (device-height: 1024px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 1)" rel="apple-touch-startup-image">

    <!-- iPad Portrait (Retina) -->
    <link href="images/ipad_start_up_1536x2008.png" media="(device-width: 768px) and (device-height: 1024px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">

    <!-- iPad Landscape (Retina) -->
    <link href="images/ipad_start_up_2048x1496.png" media="(device-width: 768px) and (device-height: 1024px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
	

	<link type="text/css" rel="stylesheet" href="css/wo.css?yuyuyu" />
	
	<style type="text/css">
		
		body,html{ margin:0;padding:0;width:100%;height:100%;-webkit-touch-callout: none; -webkit-user-select: none; -webkit-tap-highlight-color: none;}
		.apps_page,body{ background: #e1e1e1 }

    </style>
</head>
<body >
	
   
    
    <div class="page_container" style="height:100%;width:100%; position:relative;">
		
	</div>
</body>

<?php
if(empty($seajs_combo))
{

?>
	<script src="js/sea.js"></script>
<?php
}
else
{
	if(empty($seajs_no_cache))
	{
?>
	<script src="http://cb.poco.cn/seajs/2.0.0/??sea.js,plugin-combo.js"></script>
<?php
	}
	else
	{
?>
	<script src="http://cb.poco.cn/seajs/2.0.0/??sea.js,plugin-combo.js,plugin-nocache.js"></script>
<?php	
	}
}
?>


<script type="text/javascript">
<!--


<?php
if(empty($seajs_combo) && !empty($seajs_no_cache))
{

?>
var noCachePrefix = 'seajs-ts='
var noCacheTimeStamp = noCachePrefix + new Date().getTime()
var seajs_no_cache = true
<?php
}
else
{

?>
var seajs_no_cache = false
<?php
}

if(empty($seajs_uglify))
{
?>
var seajs_uglify = false
<?php
}
else
{

?>
var seajs_uglify = true
<?php
}
?>

//-->
</script>
<script src="./js/seajs_config.js"></script>
<script type="text/javascript">
//初始化sea
seajs.use('http://my.poco.cn/hotbed/bsz_frame/js/wo/web_wo_init.js')
//-->
</script>

</html>