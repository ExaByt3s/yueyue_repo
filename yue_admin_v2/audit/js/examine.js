/*
* 绑定提交
*/


//审核
function pic_examine_pass()
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
function passDel($month)
{
	var $url = "pic_examine_edit.php?act=pass_to_del&ymonth="+$month;
	$("#list_form").attr("action", $url).submit();
}

//恢复图片
function returnDel($month)
{
	var $url = "pic_examine_edit.php?act=del_to_pass&ymonth="+$month;
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
function textPassDel($month)
{
	var $url = "text_examine_edit.php?act=pass_to_del&ymonth="+$month;
	$("#list_form").attr("action", $url).submit();
}
//由删除到已经审核
function textDelPass($month)
{
	var $url = "text_examine_edit.php?act=del_to_pass&ymonth="+$month;
	$("#list_form").attr("action", $url).submit();
}
/**文字部分结束*/