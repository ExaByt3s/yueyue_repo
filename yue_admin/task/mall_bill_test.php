<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
include_once 'common.inc.php';

$task_order_obj = POCO::singleton('pai_mall_order_class');//������
$task_order_status_config = pai_mall_load_config('order_status');//����״̬����
$task_org_obj = POCO::singleton('pai_organization_class');//������
$task_coupon_obj = POCO::singleton('pai_coupon_class');//�Ż�ȯ��
$task_user_obj = POCO::singleton('pai_user_class');//�û���
$task_seller_service_obj = POCO::singleton('pai_mall_seller_class');//�̼���

$login_user_id = $yue_login_id;//��¼��ID��ȫ�֣�

set_time_limit(0);
/* �����б� */
if( $action == 'list'|| $action == '' )
{
    $order_list = task_mall_bill_get_order_list($_INPUT);

    $tpl = new SmartTemplate( TASK_TEMPLATES_ROOT."order_list.tpl.htm" );
    /* ģ�帳ֵ */
    $tpl->assign( 'filter',$order_list['filter'] );
    $tpl->assign( 'page', $order_list['page'] );
    $tpl->assign( 'list', $order_list['list'] );
    $tpl->assign( 'type_list', task_mall_bill_get_type() );
    $tpl->assign( 'type_id', $order_list['filter']['type_id'] );
}
/* �������� */
elseif( $action == 'detail' )
{
    $order_id = intval($_INPUT['order_id']);
    if( $order_id<1 )
    {
        die('������������ϵ������Ա');
    }
    $order_detail = $task_order_obj->get_order_full_info_by_id($order_id);

    $order_id = intval($order_detail['order_id']);
    $org_user_id = intval($order_detail['org_user_id']);
    $is_use_coupon = intval($order_detail['is_use_coupon']);
    $add_time = $order_detail['add_time'];
    $close_time = $order_detail['close_time'];
    $accept_time = $order_detail['accept_time'];
    $sign_time = $order_detail['sign_time'];
    $buyer_user_id = intval($order_detail['buyer_user_id']);
    $seller_user_id = intval($order_detail['seller_user_id']);
    $type_id = intval($order_detail['type_id']);
    $seller_id = intval($order_detail['seller_id']);

    if( $org_user_id > 1 ) $order_detail['org_user_name'] = $org_obj->get_org_name_by_user_id($org_user_id);
    if( $is_use_coupon == 1 )
    {
        $coupon_info = POCO::singleton('pai_coupon_class')->get_coupon_detail_by_sn($order_detail['coupon_sn'], $user_id=0);
        $order_detail['coupon_name'] = $coupon_info['batch_name'];
    }
    if( $add_time > 0 ) $order_detail['add_time_str'] = date('Y-m-d H:i:s',$add_time);
    if( $close_time > 0 ) $order_detail['close_time_str'] = date('Y-m-d H:i:s',$close_time);
    if( $accept_time > 0 ) $order_detail['accept_time_str'] = date('Y-m-d H:i:s',$accept_time);
    if( $sign_time > 0 ) $order_detail['sign_time_str'] = date('Y-m-d H:i:s',$sign_time);
    $order_detail['buyer_user_phone'] = $user_obj->get_phone_by_user_id($buyer_user_id);
    $order_detail['seller_user_phone'] = $user_obj->get_phone_by_user_id($seller_user_id);
    // ����Ա
    $mall_seller_ret = $task_seller_service_obj->get_seller_service_belong($seller_id);
    $belong_user_id = $mall_seller_ret[$type_id];
    $order_detail['belong_user_name'] = get_user_nickname_by_user_id($belong_user_id);

    $tpl = new SmartTemplate( TASK_TEMPLATES_ROOT."order_detail.tpl.htm" );
    /* ģ�帳ֵ */
    $tpl->assign( 'detail', $order_detail );
}
/* ���򡢷�ҳ����ѯ */
elseif( $action == 'query' )
{

}
elseif( $action == 'export_order' )
{
    $format_ret = task_mall_bill_format_order($_INPUT);
    $where = $format_ret['where'];
    $filter = $format_ret['filter'];

    ini_set("memory_limit","512M");

    $head_arr = array(
        '���','����ID','������','����״̬','������ע','��������','������Դ','�Ƿ񸶿�','ʵ���۸�','�����ܼ�','�µ�ʱ��','����ʱ��',
        'ȡ��ʱ��','����ʱ��','ǩ��ʱ��','ǩ����','�Ƿ��Ż�','�Żݽ��','�Ż�����','�Żݴ���','��������','�����ַ','����ʱ��','��ƷID','��Ʒ����',
        '��������','��չ����','��Ʒ�۸�','�̼�ID','�̼��ǳ�','��������','����ԱID','�̼��ֻ�����','���ID','����ǳ�','����ֻ�����'
    );

    $fileName = "����";
    $title = "�����б�";
    if( $filter['goods_id']>0 ) //����goods_id����
    {
        $list = $task_order_obj->get_order_list_by_goods_ids($filter['type_id'], $filter['status'], array($filter['goods_id']), false, $where, 'order_id', '0,4000');
    }
    else
    {
        $list = $task_order_obj->get_order_full_list($filter['type_id'], $filter['status'], false, $where, 'order_id desc', '0, 4000');
    }

    $output_data = task_mall_bill_order_list_export_data($list);
    task_mall_bill_export_data($fileName,$head_arr,$output_data);
    exit;
}
elseif( $action == 'close_order' )
{
    $ret = 'false';
    $order_sn = isset($_INPUT['order_sn']) ? trim($_INPUT['order_sn']) : '';
    $reason = isset($_INPUT['reason']) ? trim($_INPUT['reason']) : '';
    $str = urldecode(iconv("UTF-8", "GBK", $reason));
    if( $order_sn && $reason )
    {
        $ret = $task_order_obj->close_order_for_system($order_sn,$str);
    }
    echo json_encode($ret);
    exit;
}
/* ��ʾģ�� */
$tpl->output ();

/**
 * �����б�
 * @return array
 */
function task_mall_bill_get_order_list($input)
{
    global $task_order_obj;
    global $org_obj;
    global $order_status_config;
    $format_ret = task_mall_bill_format_order($input);
    $where = $format_ret['where'];
    $filter = $format_ret['filter'];

    if( $filter['sign_time_begin'] ) $filter['sign_time_begin'] = date("Y-m-d",$filter['sign_time_begin']);
    if( $filter['sign_time_end'] ) $filter['sign_time_end'] = date('Y-m-d',$filter['sign_time_end']);
    if( $filter['add_time_begin'] ) $filter['add_time_begin'] = date("Y-m-d",$filter['add_time_begin']);
    if( $filter['add_time_end'] ) $filter['add_time_end'] = date("Y-m-d",$filter['add_time_end']);
    if( $filter['pay_time_begin'] ) $filter['pay_time_begin'] = date('Y-m-d',$filter['pay_time_begin']);
    if( $filter['pay_time_end'] ) $filter['pay_time_end'] = date('Y-m-d',$filter['pay_time_end']);

    $filter['is_close'] = 0;

    /* ��ҳ���� */
    $page_arr['page'] = empty($input['page']) || (intval($input['page']) <= 0) ? 1 : intval($input['page']);
    if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
    {
        $page_arr['page_size'] = intval($input['page_size']);
    }
    else
    {
        $page_arr['page_size'] = 20;
    }
    if( $filter['goods_id']>0 )
    {
        $goods_rst = $task_order_obj->get_order_list_by_goods_ids($filter['type_id'], $filter['status'], array($filter['goods_id']), true, $where);
        $page_arr['page_count'] = $goods_rst['total'];
    }else
    {
        $page_arr['page_count'] = $task_order_obj->get_order_list($filter['type_id'],$filter['status'],true, $where);
    }
    // ��ҳ����
    $page_obj = new show_page();
    // ���÷�ҳ����
    $page_obj->setvar($filter);
    $page_obj->set($page_arr['page_size'], $page_arr['page_count']);

    $limit = $page_obj->limit();

    $page = $page_obj->output(1);

    /* ��ȡ�����б� */
    if( $filter['goods_id']>0 )
    {
        $list = $task_order_obj->get_order_list_by_goods_ids($filter['type_id'], $filter['status'], array($filter['goods_id']), false, $where, 'order_id', $limit);
    }
    else
    {
        $list = $task_order_obj->get_order_full_list($filter['type_id'], $filter['status'], false, $where, 'order_id desc', $limit);
    }
    /* ��ʽ������ */
    foreach($list as $key => &$val)
    {
        $val['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
        $val['type_id_str'] = task_mall_bill_get_type($val['type_id']);
        $val['seller_user_name'] = get_user_nickname_by_user_id($val['seller_user_id'])."</br>[{$val['seller_user_id']}]";
        $val['buyer_user_name'] = get_user_nickname_by_user_id($val['buyer_user_id'])."</br>[{$val['buyer_user_id']}]";
        $val['org_user_name'] = $org_obj->get_org_name_by_user_id($val['org_user_id']);
        $val['is_pay'] = $val['is_pay'] == 0 ? '��' : '��';
        $val['is_use_coupon'] = $val['is_use_coupon'] == 0 ? '��' : '��';
        $list[$key]['service_time_str'] = $val['detail_list'][0]['service_time_str'];
        $list[$key]['goods_id'] = $val['detail_list'][0]['goods_id'];
        $time_left = $val['detail_list'][0]['service_time']-time();
        if( $val['status']==2&&$time_left<12*3600 )
        {
            $val['is_close'] = 1;
        }
    }

    $filter['output'] = 1;
    /* ��filter����ת��ΪurlΪģ�巽��ʹ�� */
    $filter['url'] = http_build_query($filter);

    return array('list'=>$list, 'filter'=>$filter, 'page'=>$page );
}

/**
 * ��ȡƷ����Ϣ
 * @param string $type_id Ʒ��id
 * @return string
 */
function task_mall_bill_get_type($type_id = '')
{
    $type_obj = POCO::singleton('pai_mall_goods_type_class');
    $type_list = $type_obj->get_type_cate(2);
    $type = array();
    if( $type_id )
    {
        foreach( $type_list as $value )
        {
            if( $type_id==$value['id'] )
            {
                $type = $value['name'];
            }
        }
    }
    else
    {
        foreach( $type_list as $value )
        {
            $type[$value['id']] = $value['name'];
        }
    }

    return $type;
}

/**
 * ��ȡ����Ʒ���µĶ���������
 * @param $type_id
 * @param $goods_att_info
 * @return string
 */
function task_mall_bill_get_type_expand($type_id, $goods_att_info)
{
    $type_name = '';
    switch($type_id)
    {
        case 3://��ױ����
            $type_name = $goods_att_info[68];
            break;
        case 5://��Ӱ��ѵ
            $type_name = $goods_att_info[133];
            break;
        case 12://Ӱ������
            $type_name = $goods_att_info[17];
            break;
        case 31://ģ�ط���
            $type_name = $goods_att_info[46];
            break;
        case 40://��Ӱ����
            $type_name = $goods_att_info[90];
            break;
        case 41://Լ��ʳ
            $type_name = $goods_att_info[219];
            break;
        case 43://��������
            $type_name = $goods_att_info[278];
            break;
        default:
            break;
    }
    return $type_name;
}

/**
 * ������������
 * @param $order_list
 * @return array
 */
function task_mall_bill_order_list_export_data($order_list)
{
    global $task_order_obj;
    global $org_obj;
    global $order_status_config;
    $output_data = array();
    $no = 1;
    foreach($order_list as $key => $val)
    {
        $detail_list = POCO::singleton('pai_mall_order_class')->get_detail_list_all($val['order_id']);

        $goods_obj = POCO::singleton('pai_mall_goods_class');
        $goods_info_tmp = $goods_obj->get_goods_info(intval($detail_list[0]['goods_id']));
        $type_expand = task_mall_bill_get_type_expand($val['type_id'], $goods_info_tmp['goods_att']);

        $type_id = $val['type_id'];
        $mall_seller_ret = POCO::singleton('pai_mall_seller_class')->get_seller_service_belong($val['seller_id']);
        $belong_user_id = $mall_seller_ret[$type_id];

        $output_data[$key]['no'] = $no;
        $output_data[$key]['order_id'] = $val['order_id'];
        $output_data[$key]['order_sn'] = $val['order_sn'];
        $output_data[$key]['status'] = $order_status_config[$val['status']];
        $output_data[$key]['description'] = $val['description'];
        $output_data[$key]['quantity'] = $detail_list[0]['quantity'];
        $output_data[$key]['referer'] = $val['referer'];
        if( $val['is_pay']==1 ) {$output_data[$key]['is_pay'] = "��֧��";} else {$output_data[$key]['is_pay'] = "��֧��";}
        $output_data[$key]['pending_amount'] = $val['pending_amount'];
        $output_data[$key]['total_amount'] = $val['total_amount'];
        $output_data[$key]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
        if( $val['pay_time']>0 ) {$output_data[$key]['pay_time'] = date("Y-m-d H:i:s", $val['pay_time']);} else {$output_data[$key]['pay_time'] = "";}
        if( $val['close_time']>0 ) {$output_data[$key]['close_time'] = date("Y-m-d H:i:s", $val['close_time']);} else {$output_data[$key]['close_time'] = "";}
        if( $val['accept_time']>0 ) {$output_data[$key]['accept_time'] = date("Y-m-d H:i:s", $val['accept_time']);} else {$output_data[$key]['accept_time'] = "";}
        if( $val['sign_time']>0 ) {$output_data[$key]['sign_time'] = date("Y-m-d H:i:s", $val['sign_time']);} else {$output_data[$key]['sign_time'] = "";}
        if( $val['sign_by'] == 'sys' )
        {
            $output_data[$key]['sign_by'] = 'ϵͳ�Զ�ǩ��';
        }
        elseif( $val['sign_by'] == 'buyer' )
        {
            $output_data[$key]['sign_by'] = '�̼�ǩ��';
        }
        elseif( $val['sign_by'] == '' )
        {
            $output_data[$key]['sign_by'] = 'δǩ��';
        }
        if( $val['is_use_coupon']>0 ) {$output_data[$key]['is_use_coupon'] = "��";} else {$output_data[$key]['is_use_coupon'] = "��";}
        $output_data[$key]['discount_amount'] = $val['discount_amount'];
        if( $val['is_use_coupon'] == 1 )
        {
            $coupon_info = POCO::singleton('pai_coupon_class')->get_coupon_detail_by_sn($detail_list[0]['coupon_sn'], $user_id=0);
            $output_data[$key]['coupon_name'] = $coupon_info['batch_name'];
        }
        else
        {
            $output_data[$key]['coupon_name'] = "";
        }
        $output_data[$key]['coupon_sn'] = $val['coupon_sn'];
        $output_data[$key]['service_people'] = $detail_list[0]['service_people'];
        $output_data[$key]['service_address'] = $detail_list[0]['service_address'];
        if( $detail_list[0]['service_time']>0 ) {$output_data[$key]['service_time'] = date("Y-m-d H:i:s", $detail_list[0]['service_time']);} else {$output_data[$key]['service_time'] = "";}

        $output_data[$key]['goods_id'] = $detail_list[0]['goods_id'];
        $output_data[$key]['goods_name'] = $detail_list[0]['goods_name'];
        $output_data[$key]['type_id'] = task_mall_bill_get_type($val['type_id']);
        $output_data[$key]['type_expand'] = str_replace('<br>', '', $type_expand);
        $output_data[$key]['prices'] = $detail_list[0]['prices'];
        $output_data[$key]['seller_user_id'] = $val['seller_user_id'];
        $output_data[$key]['seller_user_name'] = get_user_nickname_by_user_id($val['seller_user_id']);
//        $output_data[$key]['org_user_id'] = $org_obj->get_org_name_by_user_id($val['org_user_id']);
        $output_data[$key]['org_user_id'] = $val['org_user_id'];
        $output_data[$key]['belong_user_id'] = $belong_user_id;
        $output_data[$key]['seller_user_phone'] = POCO::singleton('pai_user_class')->get_phone_by_user_id($val['seller_user_id']);

        $output_data[$key]['buyer_user_id'] = $val['buyer_user_id'];
        $output_data[$key]['buyer_user_name'] = get_user_nickname_by_user_id($val['buyer_user_id']);
        $output_data[$key]['buyer_user_phone'] = POCO::singleton('pai_user_class')->get_phone_by_user_id($val['buyer_user_id']);

        $no++;
    }
    return $output_data;
}

function task_mall_bill_format_order($input)
{

    /* ����������� */
    $filter['action'] = 'list';
    $filter['status'] = isset($input['status']) ? intval($input['status']) : -1;
    $filter['keyword'] = isset($input['keyword']) ? trim($input['keyword']) : '';
    $filter['order_sn'] = isset($input['order_sn']) ? trim($input['order_sn']) : '';
    $filter['is_pay'] = isset($input['is_pay']) ? intval($input['is_pay']) : -1;
    $filter['type_id'] = isset($input['type_id']) ? intval($input['type_id']) : -1;
    $filter['min_total'] = isset($input['min_total']) ? trim($input['min_total']) : '';
    $filter['max_total'] = isset($input['max_total']) ? trim($input['max_total']) : '';
    $filter['min_discount'] = isset($input['min_discount']) ? trim($input['min_discount']) : '';
    $filter['max_discount'] = isset($input['max_discount']) ? trim($input['max_discount']) : '';
    $filter['sign_time_begin'] = empty($input['sign_time_begin']) ? '' :  strtotime($input['sign_time_begin']);
    $filter['sign_time_end'] = empty($input['sign_time_end']) ? '' : strtotime(trim($input['sign_time_end']))+24*3600-1;
    $filter['add_time_begin'] = empty($input['add_time_begin']) ? '' :  strtotime(trim($input['add_time_begin']));
    $filter['add_time_end'] = empty($input['add_time_end']) ? '' : strtotime(trim($input['add_time_end']))+24*3600-1;
    $filter['pay_time_begin'] = empty($input['pay_time_begin']) ? '' :  strtotime($input['pay_time_begin']);
    $filter['pay_time_end'] = empty($input['pay_time_end']) ? '' : strtotime(trim($input['pay_time_end']))+24*3600-1;
    $filter['org_user_id'] = empty($input['org_user_id']) ? '' : trim($input['org_user_id']);
    $filter['buyer_user_id'] = isset($input['buyer_user_id']) ? intval($input['buyer_user_id']) : -1;
    $filter['seller_user_id'] = intval($input['seller_user_id']);
    $filter['referer'] = empty($input['referer']) ? '' : trim($input['referer']);
    $filter['is_use_coupon'] = isset($input['is_use_coupon']) ? intval($input['is_use_coupon']) : -1;
    $filter['is_org_user'] = isset($input['is_org_user']) ? intval($input['is_org_user']) : -1;
    $filter['buyer_user_phone'] = isset($input['buyer_user_phone']) ? trim($input['buyer_user_phone']) : '';
    $filter['seller_user_phone'] = isset($input['seller_user_phone']) ? trim($input['seller_user_phone']) : '';
    $filter['search_selected'] = empty($input['search_selected']) ? 'order_sn' : trim($input['search_selected']);
    $filter['goods_id'] = isset($input['goods_id']) ? intval($input['goods_id']) : -1 ;

//    var_dump($filter);

    /* �����ѯ���� */
    $where = ' 1 ';
    if( $filter['goods_id']>0 )
    {
        $filter['search_selected_val'] = $filter['goods_id'];
        $where = '';
    }
    if( $filter['keyword'] )
    {
        $where .= " AND (order_id = {$filter['keyword']} or order_sn like '%".pai_mall_change_str_in($filter['keyword'])."%') ";
    }
    if( $filter['order_sn'] )
    {
        $filter['search_selected_val'] = $filter['order_sn'];
        $where .= " AND order_sn = '".$filter['order_sn'] ."'";
    }
    if( $filter['is_pay']>-1 )
    {
        $where .= " AND is_pay = {$filter['is_pay']} ";
    }
    if( $filter['min_total'] )
    {
        $where .= " AND total_amount >= {$filter['min_total']} ";
    }
    if( $filter['max_total'] )
    {
        $where .= " AND total_amount <= {$filter['max_total']} ";
    }
    if( $filter['min_discount'] )
    {
        $where .= " AND discount_amount >= {$filter['min_discount']} ";
    }
    if( $filter['max_discount'] )
    {
        $where .= " AND discount_amount <= {$filter['max_discount']} ";
    }
    if( $filter['sign_time_begin'] )
    {
        $where .= " AND sign_time >= {$filter['sign_time_begin']} ";
    }
    if( $filter['sign_time_begin'] )
    {
        $where .= " AND sign_time <= {$filter['sign_time_end']} ";
    }
    if( $filter['add_time_begin'] )
    {
        $where .= " AND add_time >= {$filter['add_time_begin']} ";
    }
    if( $filter['add_time_begin'] )
    {
        $where .= " AND add_time <= {$filter['add_time_end']} ";
    }
    if( $filter['pay_time_begin'] )
    {
        $where .= " AND pay_time >= {$filter['pay_time_begin']} ";
    }
    if( $filter['pay_time_begin'] )
    {
        $where .= " AND pay_time <= {$filter['pay_time_end']} ";
    }
    if( $filter['org_user_id'] )
    {
        $filter['search_selected_val'] = $filter['org_user_id'];
        $where .= " AND org_user_id = {$filter['org_user_id']} ";
    }

    if( $filter['is_org_user'] == 1 )
    {
        $where .= " AND org_user_id > 0 ";
    }
    elseif( $filter['is_org_user'] == 0 )
    {
        $where .= " AND org_user_id = 0 ";
    }
    if( $filter['is_use_coupon'] > -1 )
    {
        $where .= " AND is_use_coupon = {$filter['is_use_coupon']} ";
    }
    if( $filter['seller_user_id']>0 )
    {
        $filter['search_selected_val'] = $filter['seller_user_id'];
        $where .= " AND seller_user_id = {$filter['seller_user_id']} ";
    }
    if( $filter['buyer_user_id'] > 0 )
    {
        $filter['search_selected_val'] = $filter['buyer_user_id'];
        $where .= " AND buyer_user_id = {$filter['buyer_user_id']} ";
    }
    if( $filter['referer'] )
    {
        $filter['search_selected_val'] = $filter['referer'];
        $where .= " AND referer = '".$filter['referer']."' ";
    }
    if( $filter['buyer_user_phone'] )
    {
        $buyer_user_id = POCO::singleton('pai_user_class')->get_user_id_by_phone($filter['buyer_user_phone']);
        if( $buyer_user_id )
        {
            $filter['search_selected_val'] = $filter['buyer_user_phone'];
            $where .= " AND buyer_user_id = {$buyer_user_id} ";
        }
    }
    if( $filter['seller_user_phone'] )
    {
        $seller_user_id = POCO::singleton('pai_user_class')->get_user_id_by_phone($filter['seller_user_phone']);
        if( $seller_user_id )
        {
            $filter['search_selected_val'] = $filter['seller_user_phone'];
            $where .= " AND seller_user_id = {$seller_user_id} ";
        }
    }

    return array('filter'=>$filter,'where'=>$where);
}

function task_mall_bill_export_data($fileName,$headArr,$data){
    if(empty($data) || !is_array($data)){
        die("data must be a array");
    }
    if(empty($fileName)){
        exit;
    }

    $date = date("Y_m_d",time());
    $fileName .= "_{$date}.xls";

    //�����µ�PHPExcel����
    $objPHPExcel = new PHPExcel();

    //���ñ�ͷ
    $key = 0;
    $objActSheet = $objPHPExcel->getActiveSheet();
    $objActSheet->getRowDimension('1')->setRowHeight(22);
    foreach($headArr as $v){
        $colum = PHPExcel_Cell::stringFromColumnIndex($key);
        $objActSheet->getColumnDimension($colum)->setWidth(20);
        $objActSheet->getStyle($colum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objActSheet->getStyle($colum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $v = iconv('GB2312','utf-8', $v);
        $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
        $key += 1;
    }

    $column = 2;
    $objActSheet = $objPHPExcel->getActiveSheet();
    foreach($data as $key => $rows){ //��д��
        $span = 0;
        foreach($rows as $keyName=>$value){// ��д��
            $j = PHPExcel_Cell::stringFromColumnIndex($span);
            $objActSheet->getColumnDimension($j)->setWidth(20);
            $value = iconv('GB2312', 'utf-8',  $value);
            $objActSheet->setCellValue($j.$column, $value);
            $span++;
        }
        $column++;
    }

    //��������
    $objPHPExcel->getActiveSheet()->setTitle('Simple');
    //���û��ָ������һ����,����Excel�����ǵ�һ����
    $objPHPExcel->setActiveSheetIndex(0);
    //������ض���һ���ͻ���web�����(Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}

?>
