function subcheck($url)
{
	layer.prompt({title: 'ÇëÊäÈë±¸×¢'}, function(reason){
    $url = $url+"&reason="+reason;
    //window.alert($url);
    location.href=$url;
});
}