<?php

/** 
 * pc 
 * 列表通用组件
 * 汤圆
 * 2015-11-10
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


// $params_array 列表数据，array
// $tpl_num 模板
// $page_nums 一页显示多少个 不传为9个

function list_type($params_array,$tpl_num,$page_nums)
{	
	$file_dir = dirname(__FILE__);

    global $my_app_pai;
    global $yue_login_id;

	$tpl = $my_app_pai->getView($file_dir . "/list_type_".$tpl_num.".tpl.htm",true);

	$page = $_INPUT['page'];
	$page = $page ? $page : 1 ;

	// 分页使用的page_count
	$page_count = $page_nums  ; 

	if($page > 1)
	{
	    $limit_start = ($page - 1)*($page_count - 1);
	}
	else
	{
	    $limit_start = ($page - 1)*$page_count;
	}

	$limit = "{$limit_start},{$page_count}";

	$ret = get_api_result( $params_array['url'] ,array(
	    'user_id' => $yue_login_id,
	    'limit' => $limit,
	    'return_query' => $params_array['return_query']
	    ));

	// print_r($ret);

	/**********分页处理**********/
	$page_obj = new show_page ();
	$page_obj->file = $params_array['url'].'?';
	$total_count = $ret['data']['total'];

	$page_obj->setvar (array( 
			'return_query' => $params_array['return_query'] ,
			'title' =>  $params_array['title'],
			'type_id' =>  $params_array['type_id']
		)
	);

	$page_obj->set ( $page_count, $total_count );

	$ret = get_api_result(  $params_array['url'] ,array(

	    'user_id' => $yue_login_id,
	    'limit' => $page_obj->limit (), 
	    'return_query' => $params_array['return_query']

	    )
	);

	if ($page_count > $total_count) 
	{
	    $page_show = '';
	}
	else
	{
	    $page_show = $page_obj->output ( 1 ) ;
	}

	$tpl->assign('data_list', $ret['data']['goods']);
	$tpl->assign ( "page", $page_show );



	$tpl_html = $tpl->result();

	return $tpl_html;

};


?>