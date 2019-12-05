<?php
$access_control_array['audit']['pic_examine']       = '[100008],[100009],[100010],[100293],[100041],[100000],[100002],[100016],[101930],[100043],[126205]';
$access_control_array['audit']['text_examine']      = '[100008],[100293],[100041],[100000],[100002],[101930],[100043],[126205]';
$access_control_array['audit']['model']             = '[100009],[100293],[100008],[100034],[100039],[100000],[100042],[100002],[100458],[100036],[103571],[105788],[100046],[109078]';
$access_control_array['audit']['cameraman']         = '[100008],[100293],[100042],[100000],[100002],[100221],[100382],[100458],[100002],[100036],[105788],[100042]';
$access_control_array['audit']['model_audit']       = '[100293],[100034],[100008],[100000],[100039],[100002],[100036],[106654],[100046],[100091]';
$access_control_array['audit']['organization']      = '[100008],[100293],[100227],[100034],[100000],[100002],[100036]';
$access_control_array['audit']['push']              = '[100008],[100293],[100000],[100002],[100036],[100034]';

//OA 三种角色权限
$access_control_array['oa']['operate']      = '[100000],[100042],[100002],[100458],[105788],[100036],[117315],[128216],[126205]'; //运营
$access_control_array['oa']['expand']       = '[107131],[100039],[106654],[102073]';//拓展
$access_control_array['oa']['financial']    = '[100912],[161735],[355630]';//财务
//拓展细分权限（如果无加入细分权限，则不作分类权限判断，即可以查看所有分类）
$access_control_array['expand_category'][1] = "[100039]";//影棚租赁
$access_control_array['expand_category'][2] = "[100001],[107131]";//摄影培训
$access_control_array['expand_category'][3] = "[100039]";//化妆服务
$access_control_array['expand_category'][7] = "[106654],[102073]";//模特约拍
$access_control_array['expand_category'][0] = "[106654],[102073]";//模特约拍旧版

//第四版OA拓展细分权限
$access_control_array['mall_expand_category'][12] = "[100039]";//影棚租赁
$access_control_array['mall_expand_category'][5] = "[100001],[107131]";//摄影培训
$access_control_array['mall_expand_category'][3] = "[100039]";//化妆服务
$access_control_array['mall_expand_category'][31] = "[106654],[102073]";//模特约拍
$access_control_array['mall_expand_category'][40] = "";//摄影服务

$access_control_array['version']['pic_examine'] = '[100009],[100000],[101424]';

$access_control_array['topic']['admin'] = '[100000],[100001]';

$access_control_array['cms']['admin'] = '[101567],[100004],[100008],[100002],[100000],[100036],[100046],[100013],[101424],[100005],[100034],[100293],[103571],[100047],[101828],[117452],[126205],[100049],[100067],[129291],[131184],[176207],[183113],[194478],[350323]';
$access_control_array['wxpub']['admin'] = '[100008],[100002],[100003],[100042],[111917],[110418]';
//100423陆思明 104169曾莹【已经离职】

function yueyue_admin_authorization_list()

{
    global $yue_login_id,$access_control_array;
    $check_id = '[' . $yue_login_id . ']';
    //audit
    if (in_array($check_id,yueyue_admin_array($access_control_array['audit']))) 
    {
        $access_control_url['audit_url'] = 'http://www.yueus.com/yue_admin/audit/index.php';
    }
    //oa
    if (in_array($check_id,yueyue_admin_array($access_control_array['oa']))) 
    {
        $access_control_url['oa_url'] = 'http://www.yueus.com/yue_admin/oa/index.php';
    }
    //version
    if (in_array($check_id,yueyue_admin_array($access_control_array['version']))) 
    {
        $access_control_url['version_url'] = 'http://www.yueus.com/yue_admin/version/index.php';
    }
    //cms
    if (in_array($check_id,yueyue_admin_array($access_control_array['cms']))) 
    {
        $access_control_url['cms_url'] = 'http://www.yueus.com/yue_admin/cms/index.php';
    }
    return $access_control_url;
}

function yueyue_admin_array($access_array)
{
    $all_control_key = array();
    if (is_array($access_array)) 
    {
        foreach ($access_array as $key => $vo) 
        {
          $control_key = explode(',', $vo);
          $all_control_key = array_merge($all_control_key, $control_key);
        }
    }
    return $all_control_key;
}
function yueyue_admin_check($module_name, $category_array, $execution = 0)
{
    global $yue_login_id, $access_control_array;
    
    $access_str = '>';
    
    if(!is_array($category_array))
    {
        $category_array = (array)$category_array;
    }

    
    $check_id = '[' . $yue_login_id . ']';
    
    if($access_control_array[$module_name])
    {
        //$access_str .= $access_control_array[$module_name]['admin'];
        $return_category = '';
        foreach($category_array AS $val)
        {
            if($access_control_array[$module_name][$val])
            {
                $access_str .= $access_control_array[$module_name][$val] . ',';
                if($execution)
                {
                    $key_array = explode(',', $access_control_array[$module_name][$val]);
                    
                    foreach($key_array AS $k=>$v)
                    {
                        $return_category[$v][] = $val;
                    }
                }
            }
        }
    }


    if(strpos($access_str,$check_id) != false)
    {
        if($execution)
        {
           $pass = $return_category[$check_id]; 
        }else{
           $pass = 1;
        }
        
    }else{
        $pass = 0;
    }
    if($execution)
    {
        return $pass;
    }else{
        if(!$pass) 
        {
            $referer_url = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
            header('location:http://www.yueus.com/yue_admin/login_e.php?referer_url=' . $referer_url);
        }
    }
}
?>