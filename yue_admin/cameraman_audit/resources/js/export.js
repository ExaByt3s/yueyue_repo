/**
 *��Ӱʦ������ 
 * 
 */
 function export_search($url)
 {
	 $.layer({
         type: 2,
         title: '��ѡ�񵼳�����',
         shade: [0.6,'#000'],
         maxmin: false,
         shadeClose: false, 
         area : ['250' , '250'],
         offset : ['50px', ''],
         iframe: {src: $url}
     }); 
 }