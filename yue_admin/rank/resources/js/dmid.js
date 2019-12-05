	function sub()
	{
		var $dmid_name    = $("#dmid_name").val();
		//var $rank_name    = $("#rank_name").val();
		var $location_id  = $("#location_id").val();
		var $start_time   = $("#start_time").val();
		var $end_time   = $("#end_time").val();
		if ($location_id == 0) 
		{
			window.alert('城市ID不能为空');
			return false;
		}
		if ($dmid_name == '') 
		{
			window.alert('名称不能为空');
			return false;
		}
		if ($start_time == '') 
	    {
	    	window.alert('开始时间不能为空');
	    	return false;
	    }
	    if ($end_time == '') 
	    {
	    	window.alert('结束时间不能为空');
	    	return false;
	    };
		/*if ($rank_name == '') 
		{
			window.alert('名称不能为空');
			return false;
		}*/
		
		$("#myform").submit();
	}