function subcheck($url, $reason)
{
	layer.prompt({title: 'ÇëÊäÈë±¸×¢', val: $reason}, function(reason){
    $url = $url+"&reason="+reason;
    //window.alert($url);
    location.href=$url;
});
}