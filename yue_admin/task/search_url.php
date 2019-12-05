<?php
include_once 'common.inc.php';

//原来的url
dump("test_url");
$test_url = "p:3|gg_1:40|gd_:90,335_289,0_309,0|gt_:335,336|ss_7:4~5年|55||ff\|66||5566|8899|5566:''fsdsdfs|gg_3:0|gg_5:0";
dump($test_url);

//反码
$test_ary = search_url_decode($test_url);
dump($test_ary);

//编码
$test_str = search_url_encode($test_ary);

dump($test_str);


//search_url 编码
function search_url_encode($url_ary)
{
    if( empty($url_ary) )
    {
        return false;
    }
    if( empty($url_ary['field_info']) )
    {
        return false;
    }
    $field_info = $url_ary['field_info'];
    unset($url_ary['field_info']);
    $url_search_config = pai_mall_load_config('url_search');
    $unit = array();

    $key_blong_ary = array(
        'seller'=>'ss_',
        'order'=>'or_',
        'common'=>'gg_',
    );

    foreach($url_ary as $k => $v)
    {
         $key_blong = '';
         $field_info_flip = array();
         $short_tag = '';
         $key_blong = $field_info[$k];
         if($key_blong == 'goods')  //属于商品属性
         {
             $field_info_flip = array_flip($url_search_config['goods']);
             $short_tag = $field_info_flip[$k];
             if($short_tag)
             {
                if($k == 'detail' || $k == 'third')
                {
                    $v_str = '';
                    if(is_array($v))
                    {
                        foreach($v as $vk => $vv)
                        {
                            $v_str .= $vk.",".$vv."_";
                        }
                        $v_str = substr($v_str,0,-1);
                    }
                    $unit[$short_tag."_"] = $v_str;
                }else
                {
                    $unit['go_'.$short_tag] = $v;
                }

             }
         }else if($key_blong == 'seller' || $key_blong == 'order' || $key_blong == 'common') //属于商家属性
         {
             $field_info_flip = array_flip($url_search_config[$key_blong]);
             $short_tag = $field_info_flip[$k];
             if($short_tag)
             {
                 $prev = '';
                 $prev = $key_blong_ary[$key_blong];
                 $unit[$prev.$short_tag] = $v;
             }

         }else if($key_blong == '')  //没有被短标记的
         {
             $unit[$k] = $v;
         }   
    }

    $unit_str = '';
    if( ! empty($unit) )
    {
        foreach($unit as $k => $v)
        {
            $unit_str .= $k.":".$v."|";
        }
        $unit_str = substr($unit_str, 0,-1);
    }

    return $unit_str;
}

//search_url 反码
function search_url_decode($url)
{
    $url_search_config = pai_mall_load_config('url_search');

    if( empty($url) )
    {
        return false;
    }
    $unit = array();
    $url_ary = explode("|",$url);
    
    //排除一些"|" 这个参数的一些影响
    $res = array();
    $first_k = '';
    foreach($url_ary as $k => $v)
    {
        if(strpos($v,':') === false)
        {
            $res[$k] = $v;
        }
    }
    
    $i=1;
    $first_k = '';
    $j = array();
    foreach($res as $k => $v)
    {
        if( isset($res[$k+1]) )
        {
            if($i == 1)
            {
                $first_k = $k;
                $j[$first_k][$k] = $v;
            }else
            {
                $j[$first_k][$k] = $v;
            }
            
            $i++;
        }else
        {
            if( isset($res[$k-1]) )
            {
                //连续的最后一个
                $j[$first_k][$k] = $v;
                $i = 1;

            }else
            {
                //单单一个
                if(empty($first_k))
                {
                    $first_k = $k;
                }
                $j[$first_k][$k] = $v;
                $i = 1;
            }
        }
    }
    
    foreach($j as $k => $v)
    {
        foreach($v as $vk => $vv)
        {
            $f[$k] .= "|".$vv;
            unset($url_ary[$vk]);
        }

    }
    foreach($f as $k => $v)
    {
        if($url_ary[$k-1])
        {
            $url_ary[$k-1] = $url_ary[$k-1].$v;
        }else
        {
            $url_ary[$k] = $url_ary[$k].$v;
        }
    }
    
    if( ! empty($url_ary) )
    {
        foreach($url_ary as $uk => $uv)
        {
            $uv_ary = explode(":",$uv);
            if( ! empty($uv_ary) )
            {
                if(preg_match("/gd_/",$uv_ary['0']))  //全文detail用的
                {
                    $detail_data_ary = $detail_data = array();
                    $detail_data = $uv_ary['1'];
                    $detail_data_ary = explode("_",$detail_data);
                    foreach($detail_data_ary as $dk => $dv)
                    {
                        $dv_ary = array();
                        $dv_ary = explode(",",$dv);
                        if( ! empty($dv_ary) )
                        {
                            $unit['detail'][$dv_ary['0']] = $dv_ary['1'];
                            $unit['field_info']['detail'] = 'goods';
                        }

                    }
                }else if(preg_match("/gt_/",$uv_ary['0'])) //全文三级third 用的
                {
                    $third_data = '';
                    $third_data_ary = array();

                    $third_data = $uv_ary['1'];
                    $third_data_ary = explode("_",$third_data);

                    if( ! empty($third_data_ary) )
                    {
                        foreach($third_data_ary as $tk => $tv)
                        {
                            $tv_ary = array();
                            $tv_ary = explode(",",$tv);
                            if( ! empty($tv_ary) )
                            {
                                $unit['third'][$tv_ary['0']] = $tv_ary['1'];
                                $unit['field_info']['third'] = 'goods';
                            }
                        }
                    }
                }else if(preg_match("/go_/",$uv_ary['0'])) //商品属性
                {
                    $short_tag = $real_tag ='';
                    $short_tag = str_replace("go_","",$uv_ary['0']);
                    $real_tag = $url_search_config['goods'][$short_tag];
                    if( ! empty($real_tag) )
                    {
                        $unit[$real_tag] = $uv_ary['1'];
                        $unit['field_info']['third'] = 'goods';
                    }
                }
                else if(preg_match("/ss_/",$uv_ary['0'])) // 商家属性
                {
                    $short_tag = $real_tag ='';
                    $short_tag = str_replace("ss_","",$uv_ary['0']);
                    $real_tag = $url_search_config['seller'][$short_tag];
                    if( ! empty($real_tag) )
                    {
                        $unit[$real_tag] = $uv_ary['1'];
                        $unit['field_info'][$real_tag] = 'seller';
                    }

                }else if(preg_match("/gg_/",$uv_ary['0'])) //公共属性
                {
                    $short_tag = $real_tag ='';
                    $short_tag = str_replace("gg_","",$uv_ary['0']);
                    $real_tag = $url_search_config['common'][$short_tag];
                    if( ! empty($real_tag) )
                    {
                        $unit[$real_tag] = $uv_ary['1'];
                        $unit['field_info'][$real_tag] = 'common';
                    }
                }else
                {
                    //如果没找到就是没有短标记的直接key=>value就可以了
                    if( ! empty($uv_ary['0']) )
                    {
                        $unit[$uv_ary['0']] = $uv_ary['1'];
                        $unit['field_info'][$uv_ary['0']] = '';
                    }
                    
                }
            }
        }
    }

    return $unit;

}

exit;


?>

