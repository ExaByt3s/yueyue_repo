	function sub()
	{
		var $tpl_name = $("#tpl_name").val();
		if ($tpl_name == '') 
		{
			window.alert("���Ʋ���Ϊ��!");
			return false;
		};
		
		$("#myform").submit();
	}