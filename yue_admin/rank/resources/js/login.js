	function sub()
	{
		var $rank_id      = $("#rank_id").val();
		//var $rank_name    = $("#rank_name").val();
		var $location_id  = $("#location_id").val();
		//����ID
		var $dmid  = $("#dmid").val();
		if ($location_id == 0) 
		{
			window.alert('����ID����Ϊ��');
			return false;
		};
		if ($rank_id == '') 
		{
			window.alert('��ID����Ϊ��');
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
							//layer.msg('����ID����', 1,1);
							$("#myform").submit();
						}
						else
						{
							window.alert('����ID������');
							//layer.msg('����ID������');
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
			window.alert('���Ʋ���Ϊ��');
			return false;
		}*/
		$("#myform").submit();
	}