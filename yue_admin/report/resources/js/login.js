	function sub()
	{
		var $tpl_name = $("#tpl_name").val();
		if ($tpl_name == '') 
		{
			window.alert("名称不能为空!");
			return false;
		};
		
		$("#myform").submit();
	}