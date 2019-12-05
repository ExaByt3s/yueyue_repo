<?php

//****************** pc版头部通用 start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'goods/activity_detail.tpl.htm');

// 头部css相关
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// 头部bar
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');
// 底部
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// 下载区域
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/down-app-area.php');

$pc_global_top = _get_wbc_head();
$global_top_bar = _get_wbc_global_top_bar();
$footer = _get_wbc_footer();
$down_app_area = _get_wbc_down_app_area();


$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('global_top_bar', $global_top_bar);
$tpl->assign('footer', $footer);
$tpl->assign('down_app_area', $down_app_area);

$tpl->assign('index_url', G_MALL_PROJECT_USER_INDEX_DOMAIN);
// ================== pc版头部通用 end ==================






$type_id = $ret['data']['profile_type'];

//展示的信息
//处理规格数据结构2015-11-10

//处理第一个
foreach($ret['data']['showing']["exhibit"] as $k => $v)
{
    if($k==0)
    {
        $first_total_num = $v["total_num"];
        $first_attend_num = $v["attend_num"];
        $first_stage_id = $v["stage_id"];
        break;
    }

}


//处理标准规格信息
foreach($ret['data']['standard'] as $key => $value)
{
    foreach($value as $k => $v)
    {
        $ret['data']['standard'][$key][$k]["utf8_name"] = iconv('gbk//IGNORE','utf-8', $v["name"]);
    }

}

//构造场次，规格对象
$new_standard_list = "";
$the_lowest_prices = "";
foreach($ret['data']['standard'] as $key => $value)
{
    foreach($value as $k => $v)
    {
        $new_standard_list[$key]["stage_id"] = $v["stage_id"];
        $new_standard_list[$key]["id"] = $v["id"];
        //计算最低价
        //循环获取该场次最低价
        if(empty($the_lowest_prices))
        {
            $the_lowest_prices = $v["value"];
        }
        else
        {
            if($v["value"]<$the_lowest_prices)
            {
                $the_lowest_prices = $v["value"];
            }
        }
    }
    $new_standard_list[$key]["the_lowest_prices"] = $the_lowest_prices;
    $new_standard_list[$key]["stage_array"] = $value;
    $stage_arr_len = count($value);
    $new_standard_list[$key]["stage_array_len"] = $stage_arr_len;



}






//获取报名名单
$order_obj = POCO::singleton('pai_mall_order_class');

$i=0;
$join_list = "";
//根据报名名单条数显示查看更多按钮
$largest_show_join = 10;
$show_more_join_list = 0;
foreach($ret['data']['roster']["value"] as $key => $value)
{
    $tmp_list = $order_obj->get_order_list_of_paid_by_stage($goods_id, $value["stage_id"],false,"","0,9");
    $tmp_list_count = $order_obj->get_order_list_of_paid_by_stage($goods_id, $value["stage_id"],true,"","0,10");
    if($tmp_list_count>9)
    {
        $show_more_join_list = 1;
    }
    foreach($tmp_list as $k => $v)
    {
        $tmp_list[$k]["user_icon"] = get_user_icon($v["buyer_user_id"],64);//获取64的头像
    }

    $join_list[$i]["section_join_list"] = $tmp_list;
    $join_list[$i]["section_name"] = $value["name"];
    $i++;
}





if($yue_login_id==100004)
{
    //echo $show_more_join_list;
}


$tpl->assign('show_more_join_list',$show_more_join_list);
$tpl->assign('join_list',$join_list);
$tpl->assign('new_standard_list',$new_standard_list);
$tpl->assign('first_stage_id',$first_stage_id);
$tpl->assign('first_total_num',$first_total_num);
$tpl->assign('first_attend_num',$first_attend_num);
//处理规格数据结构2015-11-10



//获取评论处理-2015-11-23
//对评价进行分页处理
$p = 1;
$show_count = 5;//测试，先设置三个
$limit_start = ($p-1)*$show_count;

$limit_str = $limit_start.",".$show_count;
$comment_list_total_count = POCO::singleton('pai_mall_comment_class')->get_seller_comment_list_by_user_id_or_goods_id("",$goods_id,true ,$order_by = 'comment_id DESC', $limit_str, $fields = '*');

$return_arr = consturct_page_select($show_count,$comment_list_total_count);
$limit_str = $return_arr[0];
$page_select = $return_arr[1];

$comment_list = POCO::singleton('pai_mall_comment_class')->get_seller_comment_list_by_user_id_or_goods_id("",$goods_id,false, $order_by = 'comment_id DESC', $limit_str, $fields = '*');


$star_len = 5;
foreach($comment_list as $k => $v)
{
    $star_tmp_len = $v["overall_score"];
    for($i=0;$i<$star_len;$i++)
    {
        if($i<$star_tmp_len)
        {
            $v["star_list"][$i]["html_class"] = "yellow";
        }
        else
        {
            $v["star_list"][$i]["html_class"] = "";
        }

    }
    $comment_list[$k] = $v;
}


$tpl->assign("page_select",$page_select);	//分页
$tpl->assign("comment_list",$comment_list);

//分页处理


//获取评论处理-2015-11-23

//检测当前登录用户是否有发作品的权限2015-11-26
$can_publish_article = 0;//默认不能发作品,暂时设置为总可以显示
if(!empty($yue_login_id))
{
    $can_publish_article = 1;
    //判断对于当前活动是否有完成了的场次
    //第三个参数status传入8表示已经完成
    /*$complete_num_arr = $order_obj->get_order_list_by_activity_id_for_buyer($yue_login_id, $goods_id, 8, true, $order_by='', $limit='0,200', $is_fill_order=0);
    if($complete_num_arr["order_num"]>0)
    {
        $can_publish_article = 1;
    }*/
}
else
{
    $can_publish_article = 0;
}




$tpl->assign('can_publish_article', $can_publish_article);
//检测当前登录用户是否有发作品的权限2015-11-26

//数据分拆地址标题，数据第一个是地址
$map_location = $ret['data']['promise'][0]["value"];

$tpl->assign('map_location', $map_location);





// 关键词配置
$keywords_key ='';
$description_key ='';



$tpl->assign('keywords_key', $keywords_key);
$tpl->assign('description_key', $description_key);

$tpl->assign('type_id', $type_id);
$tpl->assign('type_id_title', $MALL_COLUMN_CONFIG[$type_id]['key_nav']);


//构造分页的结构函数-封装
function consturct_page_select($show_count,$total_count)
{
    //static $time;
    //$time++;
    // if( $time==4 )
    //exit();
    //var_dump(get_defined_vars());
    //$page_obj =new show_page;
    $page_obj =POCO::singleton('show_page');
    $page_obj->output_pre10  = '';
    $page_obj->output_pre    = '';
    $page_obj->output_page   = '';
    $page_obj->output_back   = '';
    $page_obj->output_back10 = '';

    $page_obj->file = '';
    $page_obj->set($show_count, $total_count);
    $page_reg = '/href=(?:\"|)(.*)(\?|&)p=(\d+)(?:\"| )/isU';
    //var_dump($page_obj->tpage);
    $limit_str = $page_obj->limit();
    $page_select = str_replace('&nbsp;', '', $page_obj->output_pre10.$page_obj->output_pre.$page_obj->output_page.$page_obj->output_back.$page_obj->output_back10);
    //异步处理分页

    preg_match_all($page_reg,$page_select,$data_arr);
    $page_arr =	$data_arr[3];
    foreach( $page_arr as $key=>$val ){
        $page_select = str_replace($data_arr[0][$key],"href='javascript:;' data-class=\"J_list_page_ajax\"  data-page=\"{$val}\"  ",$page_select);
    }
    $page_select = str_replace('color:red','color:#737373',$page_select);
    $page_select = str_replace('>上一页<', '>&lt;<', $page_select);
    $page_select = str_replace('>下一页<', '>&gt;<', $page_select);

    $return_arr = array($limit_str,$page_select);
    // dump($return_arr);
    return $return_arr;

}



if ($_INPUT['print'])
{
    print_r($ret);
    print_r($comment_list);
    print_r($join_list);
    print_r($new_standard_list);
}

?>
