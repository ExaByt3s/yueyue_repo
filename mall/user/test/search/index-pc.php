<?php

//****************** pc��ͷ��ͨ�� start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'search/index.tpl.htm');

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
$self_time_show = false;

//���ʱ�����Զ���
if($_GET['front_time'] == 'self')
{
    if($_INPUT['huo_add_time_s'] && $_INPUT['huo_add_time_e'])
    {
        $front_time_s_val = $_INPUT['huo_add_time_s'];
        $front_time_e_val = $_INPUT['huo_add_time_e'];
        $_GET['huo_add_time_s'] = $front_time_s_val;
        $_GET['huo_add_time_e'] = $front_time_e_val;
        
    }else
    {
        $front_time_s_val = date("Y-m-d",time()).' '."00:00:00";
        $front_time_e_val = date('Y-m-d',strtotime(date('Y-m-d',time()+86400))).' '."23:59:59";
    }
    $front_time_s = "<input class='self_time' id='front_time_s' type='text' name='huo_add_time_s'  placeholder='2015-10-01 09:00' value='{$front_time_s_val}'/>";
    $front_time_e = "<input class='self_time' id='front_time_e' type='text' name='huo_add_time_e'  placeholder='2015-10-02 09:00' value='{$front_time_e_val}'/>";
    $tpl->assign('front_time_s',$front_time_s);
    $tpl->assign('front_time_e',$front_time_e);

    $tpl->assign('front_time_s_val',$front_time_s_val);
    $tpl->assign('front_time_e_val',$front_time_e_val);
    if( ! empty($_SERVER['REQUEST_URI']) )
    {
        $uri_ary = explode('&',$_SERVER['REQUEST_URI']);
        if( ! empty($uri_ary) )
        {
            foreach($uri_ary as $k => $v)
            {
                if(preg_match('/huo_add_time_s/',$v) || preg_match('/huo_add_time_e/',$v) )
                {
                    unset($uri_ary[$k]);
                }
            }
            
        }
        $_SERVER['REQUEST_URI'] = implode("&",$uri_ary);
    }
    $jump_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $tpl->assign('jump_url',$jump_url);
    
    $self_time_show = true;
    $tpl->assign('self_time_show',$self_time_show);
} 

$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('global_top_bar', $global_top_bar);
$tpl->assign('footer', $footer);
$tpl->assign('down_app_area', $down_app_area);

$tpl->assign('index_url', G_MALL_PROJECT_USER_INDEX_DOMAIN);
// ================== pc��ͷ��ͨ�� end ==================


if($page >=50)
{
    $page = 50;
}

// ��ҳʹ�õ�page_count
$page_count = 9;
$is_no_data = false;
$limit_start = ($page - 1)*$page_count;

$limit = "{$limit_start},{$page_count}";



/**********��ҳ����**********/
$mall_api_obj = POCO::singleton('pai_mall_api_class'); 
if($search_type == 'seller')
{
	$mall_seller_obj = POCO::singleton('pai_mall_seller_class');

	$ret = $mall_seller_obj->user_search_seller_list($filter,$limit);

	
	// ���������б�����
	$ret = $mall_api_obj->seller_data_for_front_packing($ret);

}
else
{	
	if($_GET['debug']=='mall_test')
	{
		setcookie('debug',$_GET['debug'],time()+3600,'/','yueus.com');
		$filter['debug'] = 'mall_test';
	}
	if($_COOKIE['debug']=='mall_test')
	{
		$filter['debug'] = 'mall_test';
	}
	$mall_goods_obj = POCO::singleton('pai_mall_goods_class');

	if(G_MALL_PROJECT_USER_ONLINE_VERSION == 1)
	{
		$ret = $mall_goods_obj -> user_search_goods_list($filter,$limit);
	}
	else
	{
		$ret = $mall_goods_obj -> search_goods_list_by_fulltext($filter,$limit);
	}    
	
    
	// ���������б�����
	$ret = $mall_api_obj->goods_data_for_front_packing($ret);
    if(!empty($keywords))
	{
		foreach ($ret['data'] as $key => $value)
		{
			$ret['data'][$key]['final_titles'] = $ret['data'][$key]['titles'];
			$ret['data'][$key]['titles'] = preg_replace("/$keywords/", "<font class='color-ff5959'>$keywords</font>", $ret['data'][$key]['titles']);
		}
	}
	
	
}



// ������ʾû�����ݵ��İ�
if($ret['total'] == 0)
{
	$is_no_data = true;
}




if(empty($ret['data']) && !$is_use_filter)
{
    $no_data_text = '��Ǹ��û���ҵ� "'.$keywords.'" �����������Ϊ���Ƽ����½��';

    /**********�Ƽ�����**********/
	$mall_search_obj = POCO::singleton('pai_search_class');
	$loca = $_COOKIE['yue_location_id']?101029001:$_COOKIE['yue_location_id'];

	if($search_type == 'seller')
	{
		$recommend = $mall_search_obj->get_search_recommend_content('seller', $type_id, $loca);

		// �����̼��Ƽ�����
		foreach($recommend as $key => $val)
		{
			$recommend[$key]['cover'] = $val['images'];
			$recommend[$key]['name'] = $val['price'];
			$recommend[$key]['seller_bill_finish_num'] = $val['titles'];
			$recommend[$key]['seller_goods_num'] = $val['buy_num'];
			$recommend[$key]['avatar'] = get_seller_user_icon($val['seller_user_id'],165);
		}
	}
	else
	{
		$recommend = $mall_search_obj->get_search_recommend_content('goods', $type_id, $loca);

		
	}

	
	
	$ret['data'] = $recommend;
	$ret['total'] = count($recommend);
	
	/**********�Ƽ����� end **********/
}
else
{
    $no_data_text = '��Ǹ��û���ҵ��κν��';
}

//$_GET = urldecode($_GET);
unset($_GET['p']);

$get_params = $_GET;
$get_url_data = $_GET;

//��ҳ�����ݴ���
$detail = $third = '';
if( ! empty($get_url_data['detail']) )
{
    foreach($get_url_data['detail'] as $k => $v)
    {
        $detail .= "$k,$v". '_';
    }

    $detail = substr($detail, 0, -1);
    $get_url_data['detail'] = $detail;

    unset($detail);
    $get_url_data['for_page'] = 1;

}

if( ! empty($get_url_data['third']))
{
    foreach($get_url_data['third'] as $k => $v)
    {
        $third .= "$k,$v". '_';
    }

    $third = substr($third, 0, -1);
    $get_url_data['third'] = $third;
    unset($third);
    $get_url_data['for_page'] = 1;
}

if($ret['total']>=450)
{
    $ret['total'] = 450;
}

$page_obj = new show_page ();
$page_obj->file = "index.php?";
$total_count = $ret['total'];
$show_count = 9 ;
$page_obj->setvar ($get_url_data);
$page_obj->sethash ('dw');
$page_obj->set ( $show_count, $total_count );

if ($show_count > $total_count) 
{
    $page_show = '';
}
else
{
    $page_show = $page_obj->output ( 1,1 ) ;
}
$tpl->assign ( "page", $page_show );
/**********��ҳ���� end **********/


$list_data['page'] = $page;

$list_data['has_next_page'] = $has_next_page;

$list_data['list'] = $ret;

$mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$mall_goods_obj->hotgoods_list(array('type_id'=>$type_id),'0,9');

$key_current = $MALL_COLUMN_CONFIG[$type_id] ;

// û������ƥ���ʱ��
if(!$key_current)
{
	// �ؼ�������
	$title_key = 'ԼԼ--���Ч��ʱ�����ƽ̨';
	$keywords_key ='ԼԼ��ģ�أ���Ӱʦ��Լ�ģ�Լ�㣬����ʱ����̣����ܷ���';
	$description_key ='ԼԼ�����Ч��ʱ�����O2Oƽ̨��ͨ��ʱ�����ƽ̨��ÿ���û����������Լ�������ʱ�䣬�ṩ��Ӱ�����ܵ���صķ����������ֵ��';

	$key_current['title_key'] = $title_key;
	$key_current['keywords_key'] = $keywords_key;
	$key_current['description_key'] = $description_key;
}

$tpl->assign('key_current', $key_current);
$tpl->assign('list_data',$list_data);

if ($_INPUT['print']) 
{
    print_r($ret['data']);
}

// ��ȡ�������� ģ�ء���ѵ����Ӱ.....
$type_obj = POCO::singleton('pai_mall_goods_type_class');
$type_data = $type_obj->get_type_cate(2);
//��������ʾ�����ʽ��ʱ����ʾ
//if( ! empty($type_data) )
//{
//    foreach($type_data as $k => $v)
//    {
//        if($v['id'] == 42)
//        {
//            unset($type_data[$k]);
//            break;
//        }
//    }
//}

array_unshift($type_data, array('name'=>'����','type_id'=>$type_id));

// ���������İ�����
$mall_search_obj = POCO::singleton('pai_search_class');
$location_id = $_COOKIE['yue_location_id']?101029001:$_COOKIE['yue_location_id'];
if($search_type == 'seller')
{
	$get_hot_search_tag = $mall_search_obj->get_search_tag('seller', $type_id, $location_id);
}
else
{
	$get_hot_search_tag = $mall_search_obj->get_search_tag('goods', $type_id, $location_id);
}


// ���ű�ǩ����
if(!is_array($get_hot_search_tag))
{
	$get_hot_search_tag = '';
}
else
{	
	// ���������Ƽ�ʱ����
	foreach($get_hot_search_tag as $key => $val)
	{			
		// ��������url����
		$url = mall_yueyue_app_to_http($val['link']);

		$get_hot_search_tag[$key]['link'] = $url;

        $get_hot_search_tag[$key]['title'] = $val['str'];

        $get_hot_search_tag[$key]['str'] = mall_search_str_cut($val['str'],8);


	}

}

// ����������������
$querys = '';
$temp_query = array(
  'keywords' => $keywords,
  'search_type' => $search_type 
);
$querys = http_build_query($temp_query);
$url = './index.php?'.$querys;

foreach($type_data as $key => $val)
{
  $type_data[$key]['link'] = $url.'&type_id='.$val['id'];

  if($val['id'] == $type_id)
  {
    $type_data[$key]['selected'] = true;     
  }
}

// ���������������������ڶ�����������ʾ
if(!empty($child_filter_b_temp))
{
	$temp_arr_a = array();

	array_splice($filter_data,1,0,array($child_filter_b_temp));
}

// ��ʼ������ť
if($search_type == 'seller')
{
	$sort_btn = array(
		0 => array(
			'text' => '�ۺ�����',
			'sort' => '-1,-1',
			'orderby' => '',
						 
		),
		1 => array(
			'text' => '��������',
			'sort' => '1,1',
			'orderby' => ''			 
		),
		2 => array(
			'text' => '������',
			'sort' => '3,3',
			'orderby' => ''
		)
	);
}
else
{
	$sort_btn = array(
		0 => array(
			'text' => '�ۺ�����',
			'sort' => '-1,-1',
			'orderby' => '',
					 
		),
		1 => array(
			'text' => '��������',
			'sort' => '1,1',
			'orderby' => ''	
		),
		2 => array(
			'text' => '���۸�',
			'sort' => '4,3',
			'id'   => 'price',	
			'orderby' => ''
		),
		3 => array(
			'text' => '������',
			'sort' => '5,5',
			'orderby' => ''
		),
		4 => array(
			'text' => '������',
			'sort' => '7,7',
			'orderby' => ''
		)
	);
}

// ������������
if(!empty($orderby))
{
	foreach($sort_btn as $key => $val)
	{
		$temp_sort_arr = explode(",", $val['sort']);
		// ֻ��id����ֶβ������������棬����Ķ���Ĭ�Ͻ���
		if(isset($val['id']))
		{
			
			foreach($temp_sort_arr as $m_key => $m_val)
			{
				if($m_val == $orderby)
				{
					

					$sort_btn[$key]['orderby'] = $orderby;

					$arrow = '';

					// �۸�Ĭ��������					
					if(isset($val['id']) && isset($val['id']) == 'price')
					{
						if($m_key % 2 == 0)
						{
							$arrow = 'asc';
						}
						else
						{
							$arrow = 'desc';	
						}				
					}		

					$sort_btn[$key]['selected'] = $orderby == $m_val['orderby'] ? true : false;
					$sort_btn[$key]['arrow'] = $arrow;

					break;
				}
				
			}
		}
		else
		{
			$sort_btn[$key]['selected'] = $orderby == $temp_sort_arr[1] ? true : false;
			$sort_btn[$key]['orderby'] = $temp_sort_arr[0];
			$sort_btn[$key]['arrow'] = 'fn-hide';
		}

		
	}
}



// ���ҳ������
$output_arr['type_data'] = $type_data;
$output_arr['filter_data'] = $filter_data;


$output_arr['input_data']['data'] = array
(

    0 => array(
        'text' => $keywords,
        'place_holder' => '�ؼ���',
        'default_text' => '',
        'default_url' => '',
        'type_id' => $type_id,
        'search_type' => 'goods',
        'show' => true
    ),
    1 => array(
        'text' => $keywords,
        'place_holder' => '��ƷID/�̼�����',
        'default_text' => '',
        'default_url' => '',
        'type_id' => $type_id,
        'search_type' => 'seller',
    )

);

if($search_type == 'seller')
{
    $goods_input_data = $output_arr['input_data']['data'][0];
    $seller_input_data = $output_arr['input_data']['data'][1];

    $goods_input_data['show'] = false;
    $seller_input_data['show'] = true;

    $output_arr['input_data']['data'][0] = $seller_input_data;
    $output_arr['input_data']['data'][1] = $goods_input_data;
}


$output_arr['input_data']['search_type'] = $search_type;
$output_arr['input_data']['hot_data'] = $get_hot_search_tag;
$output_arr['input_data']['is_no_data'] = is_no_data;

$output_arr['sort_data'] = array
(
	'keywords' =>empty($keywords) ? '' : '"'.$keywords.'"',
	'total' => $total_count,
	'sort_btn' => $sort_btn,
	'orderby' => $orderby,
	'is_no_data' => $is_no_data,
	'is_seller' => $is_seller,
    'no_data_text'=>$no_data_text
);
$tpl->assign('keywords',$keywords);
$tpl->assign('is_seller',$is_seller);
$tpl->assign('page_data',mall_output_format_data($output_arr));



?>