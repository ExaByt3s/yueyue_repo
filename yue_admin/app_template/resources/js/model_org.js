/*
* xiao xiao
*��������js
*/
//�鿴
function close_bill_list($url)
{
    $.layer({
            type: 2,
            title: '��ʷ��������',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['500' , '300'],
            offset : ['100px', ''],
            iframe: {src: $url}
        });
}