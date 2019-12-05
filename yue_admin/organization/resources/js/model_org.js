/*
* xiao xiao
*机构模特js
*/

//添加风格
function model_org_style($url)
{
	$.layer({
            type: 2,
            title: '查看拍摄风格',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['500' , '470'],
            offset : ['100px', ''],
            iframe: {src: $url}
        });
}

//添加标签
function model_org_label($url)
{
	$.layer({
		type  : 2,
		title : '查看标签',
		shade: [0.1,'#fff'],
        maxmin: false,
        shadeClose: true, 
        area : ['320' , '200'],
        offset : ['100px', ''],
        iframe: {src: $url}
	});
}