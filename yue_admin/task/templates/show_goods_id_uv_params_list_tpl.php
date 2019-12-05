<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GBK">
<script type="text/javascript" src="./js/jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="js/DatePicker/WdatePicker.js"></script>
<link rel="stylesheet" type="text/css" href="css/style.css">
<link type="text/css" href="css/jquery-ui-1.8.17.custom.css" rel="stylesheet" />
<link type="text/css" href="css/jquery-ui-timepicker-addon.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-ui-1.8.17.custom.min.js"></script>

<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-zh-CN.js"></script>

<script src="js/shadowbox/shadowbox.js" type="text/javascript"></script>
<link href="js/shadowbox/shadowbox.css" rel="stylesheet" type="text/css" />

<script type="text/javascript"> 
Shadowbox.init({ 
    handleOversize: "drag", 
	overlayColor: '#000',
    modal: true,
    displayNav: true,
//	onClose: function(){location.reload();},
	displayCounter: false
}); 
Shadowbox.setup();
</script>
<script>
    
    $(function(){
         $(".ui_timepicker").datetimepicker({
            //showOn: "button",
            //buttonImage: "./css/images/icon_calendar.gif",
            //buttonImageOnly: true,
            showSecond: true,
            timeFormat: '',
            stepHour: 1,
            stepMinute: 1,
            stepSecond: 1
        });
    })
    
</script>
<style>
    .system,.browse,.date_div {float: left; margin-right: 60px;}
    
    .container{padding: 30px;}
    .date_hour{display: none;}
    
</style>

<title>商品uv参数详情</title>
</head>
<body>

<div class="container">

    <div class="system">
        <span>系统汇总:</span></br>
        <p style="padding-left: 20px;">
            <?php if($list['system']):?>
                 <?php foreach($list['system'] as $k => $v):?>
                        系统：<?php echo $k;?>,总数:<?php echo $v;?></br>
                 <?php endforeach;?>
            <?php endif;?>
        </p>
    </div>
    
    <div class="date_div">
        <span>日期汇总:</span></br>
        <p style="padding-left: 20px;">
            <?php if($list['date']):?>
                 <?php foreach($list['date'] as $k => $v):?>
                    <div class="every_day">
                        <a rel="shadowbox[every_day_hour_<?php echo $goods_id;?>]" href="goods_statistical.php?action=show_goods_id_uv_params_list&date=<?php echo $k;?>&year_date=<?php echo $year_date;?>&goods_id=<?php echo $goods_id;?>"><?php echo $k;?></a>,总数:<div class="statistical_div" style="height:15px;width:<?php echo ($v/10)."px";?>;"></div><?php echo $v;?>
                    </div>
                 <?php endforeach;?>
            <?php endif;?>
        </p>
    </div> 

    <div class="browse">
        <span>浏览器汇总:</span></br>
        <p style="padding-left: 20px;">
            <?php if($list['browse']):?>
                 <?php foreach($list['browse'] as $k => $v):?>
                        系统：<?php echo $k;?>,总数:<?php echo $v;?></br>
                 <?php endforeach;?>
            <?php endif;?>
        </p>
    </div>
    
    <div style="clear:both;float: none;"></div>
    
</div>

</body>
</html>