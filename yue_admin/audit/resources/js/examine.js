/*
* ���ύ
*/

/*ͼƬ���ֵ�*/
//���
function picPass()
{
	var $url = "pic_examine_edit.php?act=pass";
	$("#list_form").attr("action", $url).submit();
}

//ɾ��
function picDel($act)
{
	var $url = "pic_examine_edit.php?act=del";
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
            title: '����ҳ',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['600' , '400'],
            offset : ['50px', ''],
            iframe: {src: 'template_select.php?ids='+$id+'&url='+$act}
        }); 

	
}

//������ύ��
function passDel($year, $month)
{
    $month = parseInt($month);
    var $ymonth = '';
    if($month<=10)
    {
        $ymonth = $year+'0'+$month;
    }
	else
    {
        $ymonth = $year+''+$month;
    }
	var $url = "pic_examine_edit.php?act=passdel&ymonth="+$ymonth;
	$("#list_form").attr("action", $url).submit();
}

//�ָ�ͼƬ
function returnDel($year,$month)
{
    $month = parseInt($month);
    var $ymonth = '';
    if($month<=10)
    {
        $ymonth = $year+'0'+$month;
    }
    else
    {
        $ymonth = $year+''+$month;
    }
	var $url = "pic_examine_edit.php?act=returnDel&ymonth="+$ymonth;
	$("#list_form").attr("action", $url).submit();
}
/*ͼƬ���ֵĽ���*/

/**���ֲ��ֿ�ʼ*/
function textPass()
{
	var $url = "text_examine_edit.php?act=pass";
	$("#list_form").attr("action", $url).submit();
}

function textDel()
{
	var $url = "text_examine_edit.php?act=del";
	$("#list_form").attr("action", $url).submit();
}

//���Ѿ��������ɾ����
function textPassDel($year, $month)
{
	var $ymonth = $year+''+$month;
	var $url = "text_examine_edit.php?act=textPassDel&ymonth="+$ymonth;
	$("#list_form").attr("action", $url).submit();
}
//��ɾ�����Ѿ����
function textDelPass($year, $month)
{
	var $ymonth = $year+''+$month;
	var $url = "text_examine_edit.php?act=textDelPass&ymonth="+$ymonth;
	$("#list_form").attr("action", $url).submit();
}
/**���ֲ��ֽ���*/