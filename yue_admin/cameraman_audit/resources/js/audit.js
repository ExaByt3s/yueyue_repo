function subcheck($url)
{
	layer.prompt({title: '�����뱸ע'}, function(reason){
    $url = $url+"&reason="+reason;
    //window.alert($url);
    location.href=$url;
});
}