/**
 * 
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-03 10:30:54
 * @version 1
 */

//���ݵ���
function export_data($act)
{
	var $url = "topic_apply_export.php";
	var ids = new Array();
	//��ȡ����ѡ���id
	var i = 0;
	$("#list_form .inputcheckbox").each(function(){
		if ($(this).attr("checked")) 
		{
			//window.alert($(this).val());
			ids[i] = $(this).val();
			i++;
		};
	});
	//��������
	var $id = 0;
	if (ids.length >0) 
	{
		$id = ids.join(',');
	}
	else
	{
		window.alert('��û��ѡ�������޷��ύ');
		return ;
	}
	//window.alert(id);
	$.layer({
            type: 2,
            title: '��ѡ�񵼳�����',
            shade: [0.6,'#000'],
            maxmin: false,
            shadeClose: false, 
            area : ['200' , '150'],
            offset : ['50px', ''],
            iframe: {src: $url+'?ids='+$id+'&act='+$act}
        }); 

}

