<?php


include_once('../common.inc.php');

/**
 * ҳ����ղ���
 */




// ���ղ���
$page = intval($_INPUT['page']);

$option_time = $_INPUT['option_time'];





if(empty($page))
{
    $page = 1;
}

// ��ҳʹ�õ�page_count
$page_count = 10;

if($page > 1)
{
    $limit_start = ($page - 1)*($page_count - 1);
}
else
{
    $limit_start = ($page - 1)*$page_count;
}

$limit = "{$limit_start},{$page_count}";


// $time = strtotime(date("Y-m-d",time()));
// $where = "begin_time<={$time} and end_time>={$time}";


//Լ��
$order_payment_obj = POCO::singleton('pai_mall_order_payment_class');


/**
 * ��ȡ�̼Ҷ����б�����TAB
 * @param int $user_id �̼��û�ID
 * @param int $tab today week month all
 * @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
 * @param string $limit һ�β�ѯ����
 * @param int $is_seller_comment [�̼��Ƿ�������]
 * @return array
 */
$ret = $order_payment_obj->get_order_list_by_tab_for_seller($yue_login_id, $option_time, false, $limit);




// ���ǰ���й������һ�����ݣ�������ʵ���
$rel_page_count = 9;



$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
    array_pop($ret);
}

$output_arr['page'] = $page;

$output_arr['has_next_page'] = $has_next_page;

$output_arr['list'] = $ret;

// �������
mall_mobile_output($output_arr,false);



?>