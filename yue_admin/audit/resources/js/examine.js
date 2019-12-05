/*
* 绑定提交
*/

/*图片部分的*/
//审核
function picPass()
{
	var $url = "pic_examine_edit.php?act=pass";
	$("#list_form").attr("action", $url).submit();
}

//删除
function picDel($act)
{
	var $url = "pic_examine_edit.php?act=del";
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
            title: '详情页',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['600' , '400'],
            offset : ['50px', ''],
            iframe: {src: 'template_select.php?ids='+$id+'&url='+$act}
        }); 

	
}

//由审核提交的
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

//恢复图片
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
/*图片部分的结束*/

/**文字部分开始*/
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

//由已经审核文字删除掉
function textPassDel($year, $month)
{
	var $ymonth = $year+''+$month;
	var $url = "text_examine_edit.php?act=textPassDel&ymonth="+$ymonth;
	$("#list_form").attr("action", $url).submit();
}
//由删除到已经审核
function textDelPass($year, $month)
{
	var $ymonth = $year+''+$month;
	var $url = "text_examine_edit.php?act=textDelPass&ymonth="+$ymonth;
	$("#list_form").attr("action", $url).submit();
}
/**文字部分结束*/