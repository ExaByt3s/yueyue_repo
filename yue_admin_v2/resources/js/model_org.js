/*
* xiao xiao
*����ģ��js
*/

//��ӷ��
function model_org_style($url)
{
	$.layer({
            type: 2,
            title: '�鿴������',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['500' , '470'],
            offset : ['100px', ''],
            iframe: {src: $url}
        });
}

//��ӱ�ǩ
function model_org_label($url)
{
	$.layer({
		type  : 2,
		title : '�鿴��ǩ',
		shade: [0.1,'#fff'],
        maxmin: false,
        shadeClose: true, 
        area : ['320' , '200'],
        offset : ['100px', ''],
        iframe: {src: $url}
	});
}