/**
 *摄影师导出类 
 * 
 */
 function export_search($url)
 {
	 $.layer({
         type: 2,
         title: '请选择导出类型',
         shade: [0.6,'#000'],
         maxmin: false,
         shadeClose: false, 
         area : ['250' , '250'],
         offset : ['50px', ''],
         iframe: {src: $url}
     }); 
 }