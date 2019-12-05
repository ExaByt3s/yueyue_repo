/*
* xiao xiao
*机构管理js
*/
//查看
function close_bill_list($url)
{
    $.layer({
            type: 2,
            title: '历史结算详情',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['500' , '300'],
            offset : ['100px', ''],
            iframe: {src: $url}
        });
}