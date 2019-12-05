<?php
if($_SERVER['HTTP_REFERER'])
{
    if(stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') || stripos($_SERVER['HTTP_USER_AGENT'], 'iPad') || stripos($_SERVER['HTTP_USER_AGENT'], 'iPod'))
    {
        $file_url = "itms-services:///?action=download-manifest&url=https%3a%2f%2fypays.yueus.com%2fyuesell%2fyuesell_dist_ent_v3.plist";
    }else{
        $file_url = "http://c.poco.cn/yue_pai_ent_sell_120_beta8.ipa";
    }
}else{
    $file_url = "http://s.yueus.com";
}

header("Location:$file_url");
?>