<?php

//****************** pc��ͷ��ͨ�� start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'goods/activity_detail.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// ͷ��bar
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');
// �ײ�
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// ��������
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
// ================== pc��ͷ��ͨ�� end ==================






$type_id = $ret['data']['profile_type'];

//չʾ����Ϣ
//���������ݽṹ2015-11-10

//�����һ��
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


//�����׼�����Ϣ
foreach($ret['data']['standard'] as $key => $value)
{
    foreach($value as $k => $v)
    {
        $ret['data']['standard'][$key][$k]["utf8_name"] = iconv('gbk//IGNORE','utf-8', $v["name"]);
    }

}

//���쳡�Σ�������
$new_standard_list = "";
$the_lowest_prices = "";
foreach($ret['data']['standard'] as $key => $value)
{
    foreach($value as $k => $v)
    {
        $new_standard_list[$key]["stage_id"] = $v["stage_id"];
        $new_standard_list[$key]["id"] = $v["id"];
        //������ͼ�
        //ѭ����ȡ�ó�����ͼ�
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






//��ȡ��������
$order_obj = POCO::singleton('pai_mall_order_class');

$i=0;
$join_list = "";
//���ݱ�������������ʾ�鿴���ఴť
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
        $tmp_list[$k]["user_icon"] = get_user_icon($v["buyer_user_id"],64);//��ȡ64��ͷ��
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
//���������ݽṹ2015-11-10



//��ȡ���۴���-2015-11-23
//�����۽��з�ҳ����
$p = 1;
$show_count = 5;//���ԣ�����������
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


$tpl->assign("page_select",$page_select);	//��ҳ
$tpl->assign("comment_list",$comment_list);

//��ҳ����


//��ȡ���۴���-2015-11-23

//��⵱ǰ��¼�û��Ƿ��з���Ʒ��Ȩ��2015-11-26
$can_publish_article = 0;//Ĭ�ϲ��ܷ���Ʒ,��ʱ����Ϊ�ܿ�����ʾ
if(!empty($yue_login_id))
{
    $can_publish_article = 1;
    //�ж϶��ڵ�ǰ��Ƿ�������˵ĳ���
    //����������status����8��ʾ�Ѿ����
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
//��⵱ǰ��¼�û��Ƿ��з���Ʒ��Ȩ��2015-11-26

//���ݷֲ��ַ���⣬���ݵ�һ���ǵ�ַ
$map_location = $ret['data']['promise'][0]["value"];

$tpl->assign('map_location', $map_location);





// �ؼ�������
$keywords_key ='';
$description_key ='';



$tpl->assign('keywords_key', $keywords_key);
$tpl->assign('description_key', $description_key);

$tpl->assign('type_id', $type_id);
$tpl->assign('type_id_title', $MALL_COLUMN_CONFIG[$type_id]['key_nav']);


//�����ҳ�Ľṹ����-��װ
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
    //�첽�����ҳ

    preg_match_all($page_reg,$page_select,$data_arr);
    $page_arr =	$data_arr[3];
    foreach( $page_arr as $key=>$val ){
        $page_select = str_replace($data_arr[0][$key],"href='javascript:;' data-class=\"J_list_page_ajax\"  data-page=\"{$val}\"  ",$page_select);
    }
    $page_select = str_replace('color:red','color:#737373',$page_select);
    $page_select = str_replace('>��һҳ<', '>&lt;<', $page_select);
    $page_select = str_replace('>��һҳ<', '>&gt;<', $page_select);

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
