<?php
    function unicode2utf8($str){
        if(!$str) return $str;
        $decode = json_decode($str);
        if($decode) return $decode;
        $str = '["' . $str . '"]';
        $decode = json_decode($str);
        if(count($decode) == 1){
            return $decode[0];
        }
        return $str;
    }
    
//    header("Content-Type:text/html;charset=utf8");
    //echo 'alskdjfkalsdfj';
    $array[0]['cls'] = '品类1';
    $array[0]['url'] = 'http://image.baidu.com/i?tn=baiduimage&ipn=r&ct=201326592&cl=2&lm=-1&st=-1&fm=result&fr=&sf=1&fmq=1433313955909_R&pv=&ic=0&nc=1&z=&se=1&showtab=0&fb=0&width=&height=&face=0&istype=2&ie=utf-8&word=%E6%B5%81%E5%8F%A3%E6%B0%B4&f=3&oq=liukou&rsp=0&showtitle=1';
    $array[0]['src'] = 'http://cdn.duitang.com/uploads/item/201112/02/20111202142259_Fmdxh.jpg';
    $array[1]['cls'] = '品类2';
    $array[1]['url'] = 'http://image.baidu.com/i?tn=baiduimage&ipn=r&ct=201326592&cl=2&lm=-1&st=-1&fm=result&fr=&sf=1&fmq=1433313955909_R&pv=&ic=0&nc=1&z=&se=1&showtab=0&fb=0&width=&height=&face=0&istype=2&ie=utf-8&word=%E6%B5%81%E5%8F%A3%E6%B0%B4&f=3&oq=liukou&rsp=0&showtitle=1';
    $array[1]['src'] = 'http://img10.360buyimg.com/n0/g14/M00/0C/13/rBEhVlLST_4IAAAAAAOFeXB1moMAAH84gO-bMoAA4WR711.jpg';
    $array[2]['cls'] = '品类3';
    $array[2]['url'] = 'http://image.baidu.com/i?tn=baiduimage&ipn=r&ct=201326592&cl=2&lm=-1&st=-1&fm=result&fr=&sf=1&fmq=1433313955909_R&pv=&ic=0&nc=1&z=&se=1&showtab=0&fb=0&width=&height=&face=0&istype=2&ie=utf-8&word=%E6%B5%81%E5%8F%A3%E6%B0%B4&f=3&oq=liukou&rsp=0&showtitle=1';
    $array[2]['src'] = 'http://files.jb51.net/image/163cdn_255.gif';
    $array[3]['cls'] = '品类4';
    $array[3]['url'] = 'http://image.baidu.com/i?tn=baiduimage&ipn=r&ct=201326592&cl=2&lm=-1&st=-1&fm=result&fr=&sf=1&fmq=1433313955909_R&pv=&ic=0&nc=1&z=&se=1&showtab=0&fb=0&width=&height=&face=0&istype=2&ie=utf-8&word=%E6%B5%81%E5%8F%A3%E6%B0%B4&f=3&oq=liukou&rsp=0&showtitle=1';
    $array[3]['src'] = 'http://files.jb51.net/image/zs.gif';
    $array[4]['cls'] = '品类5';
    $array[4]['url'] = 'http://image.baidu.com/i?tn=baiduimage&ipn=r&ct=201326592&cl=2&lm=-1&st=-1&fm=result&fr=&sf=1&fmq=1433313955909_R&pv=&ic=0&nc=1&z=&se=1&showtab=0&fb=0&width=&height=&face=0&istype=2&ie=utf-8&word=%E6%B5%81%E5%8F%A3%E6%B0%B4&f=3&oq=liukou&rsp=0&showtitle=1';
    $array[4]['src'] = 'http://files.jb51.net/image/enkj_468.gif';
    $array[5]['cls'] = '品类6';
    $array[5]['url'] = 'http://image.baidu.com/i?tn=baiduimage&ipn=r&ct=201326592&cl=2&lm=-1&st=-1&fm=result&fr=&sf=1&fmq=1433313955909_R&pv=&ic=0&nc=1&z=&se=1&showtab=0&fb=0&width=&height=&face=0&istype=2&ie=utf-8&word=%E6%B5%81%E5%8F%A3%E6%B0%B4&f=3&oq=liukou&rsp=0&showtitle=1';
    $array[5]['src'] = 'http://img4.duitang.com/uploads/item/201308/27/20130827170826_yiWRG.thumb.600_0.gif';
    //echo 'asdfjkhaskasdfasdfasdf:' + unicode2utf8('品类1') + ':';
    echo json_encode($array);
?>