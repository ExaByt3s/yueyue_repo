<?php
// ��ȡ�ҵ����Ķ����б�

include_once('../common.inc.php');

/**
 * ҳ����ղ���
 */
$page = intval($_INPUT['page']) ;
$type = trim($_INPUT['type']);

if(empty($page))
{
	$page = 1;
}


// ��ҳʹ�õ�page_count
$page_count = 6;
if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);	
}
else
{
	$limit_start = ($page - 1)*$page_count;	
}

$limit = "{$limit_start},{$page_count}";

//$yue_login_id = 53354078;

switch ($type) 
{
	case 'unpaid':				
	case 'paid':	
		/*
		 * ��ȡ�û������״̬�б�
		 * @param int $user_id
		 * @param string $status δ���unpaid �Ѹ��paid
		 * @param bool $b_select_count
		 * @param string $limit
		 */
		$ret = get_enroll_list_by_status($yue_login_id,$type,false,$limit);	
		break;
	case 'pub':		
		/**
		 * ��ȡ�ҷ����Ļ		 
		 * @return array|int
		 */		
		$ret = get_my_event_list($yue_login_id, false, $limit);

		break;		
}


// ���ǰ���й������һ�����ݣ�������ʵ���
$rel_page_count = 5;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($ret);
}

if(empty($ret))
{
	$ret = array();
}

$output_arr['code'] = 1;

$output_arr['list'] = $ret;

$output_arr['has_next_page'] = $has_next_page;

mall_mobile_output($output_arr,false);
?>