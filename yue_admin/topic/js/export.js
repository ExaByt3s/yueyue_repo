/**
 * 
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-03 10:30:54
 * @version 1
 */

//数据导出
function export_data($act)
{
	var $url = "topic_apply_export.php";
	var ids = new Array();
	//获取所有选择的id
	var i = 0;
	$("#list_form .inputcheckbox").each(function(){
		if ($(this).attr("checked")) 
		{
			//window.alert($(this).val());
			ids[i] = $(this).val();
			i++;
		};
	});
	//发送数据
	var $id = 0;
	if (ids.length >0) 
	{
		$id = ids.join(',');
	}
	else
	{
		window.alert('您没有选择数据无法提交');
		return ;
	}
	//window.alert(id);
	$.layer({
            type: 2,
            title: '请选择导出类型',
            shade: [0.6,'#000'],
            maxmin: false,
            shadeClose: false, 
            area : ['200' , '150'],
            offset : ['50px', ''],
            iframe: {src: $url+'?ids='+$id+'&act='+$act}
        }); 

}

