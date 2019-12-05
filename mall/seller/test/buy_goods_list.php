<?php
	include_once 'common.inc.php';
	$task_goods_obj = POCO::singleton('pai_mall_goods_class');
	$type_obj = POCO::singleton('pai_mall_goods_type_class');
	$type_list = $type_obj->get_type_cate(2);
	$type_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
	$type_name_list = $type_obj -> get_type_attribute_cate(0);
	foreach($type_name_list as $val)
	{
		$type_name[$val['id']] = $val;
	}
	
	$type_list_name = array();
	foreach($type_list as $key => $val)
	{
		$type_list_name[$val['id']] = $val;
	}
	$show_status = pai_mall_load_config("goods_show");
	$status_name = pai_mall_load_config('goods_status');
	$where = "status = 1 AND is_show = 1 and (stock_type!=1 or stock_type=1 and stock_num>0) and user_id != {$yue_login_id} and (seller_id<=52 or seller_id in (55,62,68,89,90,99))";		
	$where = $_POST['user_id']?$where." and user_id = '".(int)$_POST['user_id']."'":$where;
	//echo $where;
	$list = $task_goods_obj->get_goods_list(false, $where, "goods_id DESC", "0,200");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
	<title>商品列表</title>
	<style>
	td{ font-size:12px;}
	</style>
</head>
<body>
<form id="form1" name="form1" method="post" action="">
  <?=$yue_login_id;?>
  &nbsp;&nbsp;&nbsp;&nbsp;--->> <a href="buy_bill_list.php" target="_self">进入订单列表</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;商家ID:
<input name="user_id" type="text" id="user_id" size="10" maxlength="10" />
  <input type="submit" name="button" id="button" value="提交" />
</form> 
<table width="90%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#999999">
  <tr>
		<td width="8%" height="30" align="center" bgcolor="#FFFFFF">ID</td>
		<td width="8%" height="30" align="center" bgcolor="#FFFFFF">USERID</td>
		<td width="20%" height="30" align="center" bgcolor="#FFFFFF">名称</td>
		<td width="10%" height="30" align="center" bgcolor="#FFFFFF">类型</td>
		<td width="10%" height="30" align="center" bgcolor="#FFFFFF">参考价格</td>
		<td height="30" align="center" bgcolor="#FFFFFF">购买</td>
	</tr>
	<?
	foreach($list as $val)
	{
		$goods_info = $task_goods_obj->get_goods_info($val['goods_id']);
		$goods_data = $goods_info['goods_data'];
		$val['type_name'] = $type_list_name[$val['type_id']]['name'];
	?>
  	<tr>
	    <td width="8%" height="30" align="center" bgcolor="#FFFFFF"><?=$val['goods_id']?></td>
	    <td width="8%" height="30" align="center" bgcolor="#FFFFFF"><?=$val['user_id']?></td>
	    <td width="20%" height="30" align="center" bgcolor="#FFFFFF">
	    	<img src="<?=$val['images']?>" width="150"/><br><?=$val['titles']?>
	    </td>
	    <td width="10%" height="30" align="center" bgcolor="#FFFFFF"><?=$val['type_name']?></td>
	    <td width="10%" height="30" align="center" bgcolor="#FFFFFF"><?=$val['prices']?></td>
	    <td height="30" align="left" bgcolor="#FFFFFF">
	    <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	    <?
		$prices_list_de = unserialize($val['prices_list']);
		$val['prices_list_de']=array();
		if($prices_list_de)
		{
			foreach($prices_list_de as $key_de => $val_de)
			{
				echo "<tr><td height='20px'>&nbsp;</td></tr>";
				echo "<tr><td>".$val_de."/".$type_name[$key_de]['name']."</td></tr>";
				echo '<form id="form_'.$val['goods_id'].'" name="form_'.$val['goods_id'].'" method="post" action="buy_bill_list.php">';
				echo '<tr><td>数量:';
					if($val['buy_num'] != 0)
					{
						echo '<input name="num" type="text" id="num" value="1" size="5" maxlength="5" readonly="readonly"/>';
					}
					else
					{
						echo '<input name="num" type="text" id="num" value="1" size="5" maxlength="5"/>';
					}

					if ($goods_data['type_id'] == 31)//模特
					{
						echo '<br><input type="text" name="service_people" value="" placeholder="到场拍摄人数">';
						echo '<br><input type="text" name="service_time" value="" placeholder="服务时间">';
						echo '<br><input type="text" name="service_address" value="" placeholder="地点">';
					}
					elseif ($goods_data['type_id'] == 5)//培训
					{
						# code...
					}
					elseif ($goods_data['type_id'] == 12)//影棚
					{
						echo '<br><input type="text" name="service_time" value="" placeholder="服务时间">';
					}
					elseif ($goods_data['type_id'] == 3 or $goods_data['type_id'] == 40)//化妆/摄影
					{
						echo '<br><input type="text" name="service_time" value="" placeholder="服务时间">';
						echo '<br><input type="text" name="service_address" value="" placeholder="地点">';		
					}
					echo '<input name="prices_spec" type="hidden" value="'.$val_de."/".$type_name[$key_de]['name'].'"/>
					  <input name="step" type="hidden" value="submit_order" />
					  <input name="user_id" type="hidden" value="'.$yue_login_id.'" />
					  <input name="goods_id" type="hidden" value="'.$val['goods_id'].'" />
					  <input name="type_id" type="hidden" value="'.$val['type_id'].'" />
					  <input type="submit" name="button" onclick="check_service_time()" id="button" value="购买" />';
				echo '</td></tr>';
				echo '</form>';
			}
		}
		else
		{
			echo '<form id="form_'.$val['goods_id'].'" name="form_'.$val['goods_id'].'" method="post" action="buy_bill_list.php">';
			echo '<tr><td>';
				if ($goods_data['type_id'] == 31)//模特
				{
					echo '<br><input type="text" name="service_people" value="" placeholder="到场拍摄人数">';
					echo '<br><input type="text" name="service_time" value="" placeholder="服务时间">';
					echo '<br><input type="text" name="service_address" value="" placeholder="地点">';
				}
				elseif ($goods_data['type_id'] == 5)//培训
				{
					# code...
				}
				elseif ($goods_data['type_id'] == 12)//影棚
				{
					echo '<br><input type="text" name="service_time" value="" placeholder="服务时间">';
				}
					elseif ($goods_data['type_id'] == 3 or $goods_data['type_id'] == 40)//化妆/摄影
				{
					echo '<br><input type="text" name="service_time" value="" placeholder="服务时间">';
					echo '<br><input type="text" name="service_address" value="" placeholder="地点">';		
				}
			echo '</td></tr>';

			echo '<tr><td>数量:';
				if($val['buy_num'] != 0)
				{
					echo '<input name="num" type="text" id="num" value="1" size="5" maxlength="5" readonly="readonly"/>';
				}
				else
				{
					echo '<input name="num" type="text" id="num" value="1" size="5" maxlength="5"/>';
				}
			
				echo '<input name="user_id" type="hidden" value="'.$yue_login_id.'" />
				  <input name="step" type="hidden" value="submit_order" />
				  <input name="goods_id" type="hidden" value="'.$val['goods_id'].'" />
				  <input name="type_id" type="hidden" value="'.$val['type_id'].'" />
				  <input type="submit" name="button" id="button" onclick="check_service_time("'.$val['goods_id'].'")" value="购买" />';
			echo '</td></tr>';
			echo '</form>';
		}
		?>
	    </table>
	    </td>
	  </tr>
	  <?
	  }
	  ?>
</table>
<script type="text/javascript">
	var button = document.getElementById('button');
	function check_service_time(){
		var service_value = document.getElementByName('service_time').value;
		if( service_value == '' ){
			alert('服务时间必填');
		}
		else
		{
			document.getElementById("actorCard").sumbit();
		}
	}
</script>
</body>
</html>
