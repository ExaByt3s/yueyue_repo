	function sub()
	{
		var $rank_id      = $("#rank_id").val();
		//var $rank_name    = $("#rank_name").val();
		var $location_id  = $("#location_id").val();
		//促销ID
		var $dmid  = $("#dmid").val();
		if ($location_id == 0) 
		{
			window.alert('城市ID不能为空');
			return false;
		};
		if ($rank_id == '') 
		{
			window.alert('榜单ID不能为空');
			return false;
		}
		if ($dmid != 0 && $dmid != '') 
		{
			$.ajax({
				type     : 'POST',
				dataType : 'html',
				url      : 'dmid_for.php?act=check&id='+$dmid,
				success:function(data)
				{
					var $msg = '';
					if (data) 
					{
						if (data == 'success') 
						{
							//layer.msg('促销ID存在', 1,1);
							$("#myform").submit();
						}
						else
						{
							window.alert('促销ID不存在');
							//layer.msg('促销ID不存在');
						}
						/*$msg = data == 'success' ? '' : ;
						layer.msg($msg,1,1);*/
					};
				},
				error:function(data)
				{
					window.alert(data);
				}
			})
		  return false;
		};
		/*if ($rank_name == '') 
		{
			window.alert('名称不能为空');
			return false;
		}*/
		$("#myform").submit();
	}