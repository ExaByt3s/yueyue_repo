<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>修改商品</title>
<meta http-equiv="Content-Type" content="text/html; charset=GBK">
<script type="text/javascript" src="./js/jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="js/DatePicker/WdatePicker.js"></script>
<link rel="stylesheet" type="text/css" href="./css/style.css">
<script src="js/shadowbox/shadowbox.js" type="text/javascript"></script>
<link href="js/shadowbox/shadowbox.css" rel="stylesheet" type="text/css" />
<style>
    img {width:200px !important;}
</style>
<script type="text/javascript"> 
Shadowbox.setup();

function check(goods_id,edit_status)
{
    if( ! confirm('确认操作') )
    {
        return false;
    }
    
	var url = '?action=do_edit_status&goods_id='+goods_id+'&edit_status='+edit_status;
	
	$.ajax( {    
			url:url,
			type:'post',  
			data:{},  
			cache:false,    
			dataType:'json',    
			success:function(data) {    
			    alert('成功操作');
				window.parent.location.reload();
			 },    
			 error : function() {    
				  alert("异常！");
				  window.parent.Shadowbox.close();
			 }			 
	});  
}

</script>
</head>
<body>
<div class="mainbox">
  <div id="tabs" style="margin-top:10px;">
    <div class="tabbox">
      <div class="table-list">
        <div class="table" style="width:100%;">          
          <fieldset>
            <legend>
                活动修改没审核详情
            </legend>
			<form id="form1" name="form1" method="post" action="?action=do_edit_status" target="_self">
			  <table width="90%" align="center">
                <tr>
                     <td width="26%" height="25">主题</td>
                     <td width="74%" height="25"><?php echo $topic_array[$rs['system_data']['39059724f73a9969845dfe4146c5660e']];?></td>
                </tr>
                <tr>
                     <td width="26%" height="25">主题详情</td>
                     <td width="74%" height="25"><?php echo $topic_info_array[$rs['system_data'][$rs['system_data']['39059724f73a9969845dfe4146c5660e']]];?></td>
                </tr>  
                  
                <tr>
                     <td width="26%" height="25">标题</td>
                     <td width="74%" height="25"><?php echo $rs['default_data']['titles'];?></td>
                </tr>
                <tr>
                     <td width="26%" height="25">封面图</td>
                     <td width="74%" height="25"><a target="_blank" href="<?php echo $rs['img']['0']['img_url'];?>"><img src="<?php echo $rs['img']['0']['img_url'];?>" /></a></td>
                </tr>  
                <tr>
                     <td width="26%" height="25">活动地址</td>
                     <td width="74%" height="25"><?php echo $rs['system_data']['7a614fd06c325499f1680b9896beedeb'];?></td>
                </tr>
                <tr>
                     <td width="26%" height="25">注意事项</td>
                     <td width="74%" height="25"><?php echo $rs['system_data']['4734ba6f3de83d861c3176a6273cac6d'];?></td>
                </tr>
                
                <tr>
                     <td width="26%" height="25">内容</td>
                     <td width="74%" height="25"><?php echo $rs['default_data']['content'];?></td>
                </tr>
                <tr>
                     <td width="26%" height="25">地区</td>
                     <td width="74%" height="25"><?php echo $rs['default_data']['location_text'];?></td>
                </tr>
                <tr>
                     <td width="26%" height="25">活动领队</td>
                     <td width="74%" height="25">
                         <p>
                             <?php foreach($rs['contact_data'] as $k => $v):?>
                             <span>名子:</span><?php echo $v['name'];?>,<span>手机:<?php echo $v['phone'];?></span></br>
                             <?php endforeach;?>
                         </p>
                     </td>
                </tr>
<!--                <tr>
                     <td width="26%" height="25">活动配置</td>
                     <td width="74%" height="25">
                         <?php foreach($rs['prices_diy'] as $k => $v):?>
                         <p style="padding:10px;">
                            <span>名称:</span><?php echo $v['name'];?></br>
                            <span>开始时间:</span><?php echo $v['time_s'];?></br>
                            <span>结束时间:</span><?php echo $v['time_e'];?></br>
                            <span>剩余名额:</span><?php echo $v['stock_num'];?></br>
                            <span>明细:</span></br>
                                <?php foreach($v['detail']['name'] as $dk => $dv):?>
                                    <span style="margin-left:20px;">价格名称:</span><?php echo $dv;?><span>价钱:</span><?php echo $v['detail']['prices'][$dk];?></br>
                                <?php endforeach;?>
                         </p>
                         <?php endforeach;?>
                     </td>
                </tr>  -->
				<tr>
				  <td colspan="2" align="center">
                    <input type="button" name="button" value="通过" onclick="check(<?php echo $goods_id;?>,2)"/>&nbsp;&nbsp;<input type="button" name="button" value="不通过" onclick="check(<?php echo $goods_id;?>,3)"/>
                  </td>
				</tr>
			  </table>
			</form>
          </fieldset>
        </div>
      </div>
    </div>   
    </div>
     </div>
</body>
</html>