/*��layer�ؼ�ʹ�õ�*/
function openCamera($obj)
{
	$.layer({
            type: 2,
            title: '����ҳ',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['90%' , '80%'],
            offset : ['50px', ''],
            iframe: {src: 'cameraman_detail.php'}
        });
}

function openModel($obj)
{
   $.layer({
            type: 2,
            title: '����ҳ',
            shade: [0.1,'#fff'],
            maxmin: false,
            shadeClose: true, 
            area : ['90%' , '80%'],
            offset : ['50px', ''],
            iframe: {src: 'model_detail.php'}
        }); 
}