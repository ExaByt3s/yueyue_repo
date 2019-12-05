<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GBK">
<script type="text/javascript" src="./js/jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="js/DatePicker/WdatePicker.js"></script>
<link rel="stylesheet" type="text/css" href="./css/style.css">
<title>ip汇总详情</title>
<script src="js/shadowbox/shadowbox.js" type="text/javascript"></script>
<link href="js/shadowbox/shadowbox.css" rel="stylesheet" type="text/css" />
</head>
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
    
<body>
<div class="mainbox">
    <div id="tabs" style="margin-top:10px;">
        <div class="tabbox">
            <div class="table-list">
              <div class="table" style="width:100%;">          
                    <fieldset>
                      <legend>ip汇总详情</legend>
                            <table width="90%" align="center">
                                <tr>
                                    <td height="25">商品id</td>
                                    <td height="25">数量</td>
                                </tr>
                                <?php if( ! empty($new_final_list) ):?>
                                    <?php foreach($new_final_list as $k => $v):?>
                                        <tr>
                                            <td height="25"><?php echo $v['goods_id'];?>&nbsp;&nbsp; <a rel="shadowbox[goods_info<?php echo $v['goods_id'];?>]" href="http://www.yueus.com/mall/user/goods/service_detail.php?goods_id=<?php echo $v['goods_id'];?>" >查看</a> </td>
                                            <td height="25"><div class="statistical_div" style="width:<?php echo $v['value']."%";?>"></div><?php echo $v['value'];?></td>
                                        </tr>
                                    <?php endforeach;?>
                                <?php endif;?>
                           </table>
                     </fieldset>
              </div>
            </div>
        </div>   
    </div>
</div>
</body>
</html>