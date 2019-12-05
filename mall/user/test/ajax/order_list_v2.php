<?php
/**
 * ���������ǩ
 */
include_once 'config.php';


$mall_order_obj = POCO::singleton('pai_mall_order_class');
$type_id = intval($_INPUT['type_id']);
$status = intval($_INPUT['status']);
$page = intval($_INPUT['page']);
// ��ҳʹ�õ�page_count
$page_count = 11;
if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);
}
else
{
	$limit_start = ($page - 1)*$page_count;
}
$limit = "{$limit_start},{$page_count}";

/**** ��ҳ���� END   ****/

/**
* @param int $user_id ����û�ID
* @param int $type_id ��ƷƷ��ID
* @param int $status ����״̬��-1ȫ����0��֧����1��ȷ�ϣ�2��ǩ����7�ѹرգ�8�����
* @param boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
* @param string $order_by ����
* @param string $limit һ�β�ѯ����
* @param string $fields ��ѯ�ֶ�
* @return array
 */


$ret = $mall_order_obj->get_order_list_for_buyer($yue_login_id, $type_id, $status, false, 'order_id DESC', $limit,'*');

$list =array();
$activity_code_obj = POCO::singleton('pai_activity_code_class');
foreach ($ret as $key => $value) {
    $btn_action = btn_action($value['status'], $value['is_buyer_comment']);
    $ret[$key]['btn_action'] = $btn_action;	
	
	$code_list = $ret[$key]['code_list'];
	
	foreach($code_list as $c_key => $c_value)
	{
		
			// ��ά��ͼƬת�� start 
			$ret[$key]['code_list'][$c_key]['qr_code_url_img'] = $activity_code_obj->get_qrcode_img($c_value['qr_code_url']);
			// ��ά��ͼƬת�� end	
			if($_INPUT['print'] == 1)
			{
				print_r($ret[$key]['code_list'][$c_key]['qr_code_url_img']);
			}
	}
	
  }

// ���ð�ť״̬
function btn_action($status, $is_buyer_comment) {
    if ($is_buyer_comment == 1) {  // �̼�������
        return array();
    }
    // ��ť�İ�
    $action_arr = array(
        0 => '�ر�.close|֧��.pay',
        1 => 'ȡ������.cancel',
        2 => '�����˿�.refund|��ʾ��ά��.sign',
        7 => 'ɾ������.delete',
        8 => '���۶���.appraise'
    );
    $btn = explode('|', $action_arr[$status]);
    $arr = array();
    foreach ($btn as $value) {
        if (empty($value)) {
            continue;
        }
        list($name, $request) = explode('.', $value);
        if (empty($name) || empty($request)) {
            continue;
        }
        $arr[] = array(
            'title' => $name,
            'request' => $request, // $user_id, $order_sn
        );
    }
    return $arr;
}

// ���ǰ���й������һ�����ݣ�������ʵ���
$rel_page_count = $page_count -1;

$has_next_page = (count($ret)>$rel_page_count);

if($has_next_page)
{
	// ɾ�����һ������
	array_pop($ret);
}

if($ret)
{
	$output_arr['code'] = 1;
	$output_arr['msg']  = 'Success';
}
else
{
	$output_arr['code'] = 0;
	$output_arr['msg']  = 'Error';
}

$output_arr['data'] = $ret;
$output_arr['has_next_page'] = $has_next_page;
$output_arr['pay_url'] = '../pay/?order_sn=';


mobile_output($output_arr,false);

?>