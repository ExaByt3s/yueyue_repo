	function sub()
	{
		var $dmid_name    = $("#dmid_name").val();
		//var $rank_name    = $("#rank_name").val();
		var $location_id  = $("#location_id").val();
		var $start_time   = $("#start_time").val();
		var $end_time   = $("#end_time").val();
		if ($location_id == 0) 
		{
			window.alert('����ID����Ϊ��');
			return false;
		}
		if ($dmid_name == '') 
		{
			window.alert('���Ʋ���Ϊ��');
			return false;
		}
		if ($start_time == '') 
	    {
	    	window.alert('��ʼʱ�䲻��Ϊ��');
	    	return false;
	    }
	    if ($end_time == '') 
	    {
	    	window.alert('����ʱ�䲻��Ϊ��');
	    	return false;
	    };
		/*if ($rank_name == '') 
		{
			window.alert('���Ʋ���Ϊ��');
			return false;
		}*/
		
		$("#myform").submit();
	}