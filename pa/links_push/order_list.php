<?php
/**
 * @desc:   ע������Ķ�������
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/19
 * @Time:   15:51
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '512M');
include_once('common.inc.php');
include_once('top.php'); //�����ʽ����Ҫ����
include_once(YUE_PA_CLASS_ROOT.'pai_url_qrcode_class.inc.php');
$pai_pa_dt_register_obj = POCO::singleton( 'pai_pa_dt_register_class' );//����ע���
$mall_order_obj = POCO::singleton( 'pai_mall_order_class' );//������
$pai_organization_obj = POCO::singleton("pai_organization_class");//������
$mall_certificate_service_obj = POCO::singleton('pai_mall_certificate_service_class');//�����������
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//������
$page_obj = new show_page();
$show_count = 20;

$tpl = new SmartTemplate( TEMPLATES_ROOT.'order_list.tpl.htm' );

$act = trim($_INPUT['act']);
$puid = trim($_INPUT['puid']);//����ID

$setParam = array();

if(strlen($puid) <1) js_pop_msg_v2('�Ƿ�����');
$setParam['puid'] = $puid;
$result = $pai_pa_dt_register_obj->get_pa_dt_register_list(false,$puid,'','user_id DESC','0,99999999','user_id'); //�ƹ���Ա���û��Ķ���

if(!is_array($result)) $result = array();

$sql_in_str = '';
foreach($result as $val)
{
   if(strlen($sql_in_str)>0) $sql_in_str .= ',';
    $sql_in_str .= $val['user_id'];
}
unset($result);
$page_obj->setvar($setParam);

$where_str = '';
if(strlen($sql_in_str)>0)//�������û�
{
    $where_str .= "buyer_user_id IN ({$sql_in_str})";
    if($act == 'export')//��������
    {
        $data = array();
        $list = $mall_order_obj->get_order_full_list(0,8,false, $where_str,'sign_time DESC,close_time DESC,order_id DESC',"0,99999999");
        $data = array();
        foreach($list as $key=>$val)
        {
            $data[$key]['order_id'] = $val['order_id'];
            $data[$key]['order_sn'] = $val['order_sn'];
            $data[$key]['seller_name'] = $val['seller_name'];
            $data[$key]['seller_user_id'] = $val['seller_user_id'];
            $data[$key]['buyer_name'] = $val['buyer_name'];
            $data[$key]['buyer_user_id'] = $val['buyer_user_id'];
            $data[$key]['type_name'] = $val['type_name'];
            $data[$key]['goods_id'] = $val['detail_list'][0]['goods_id'];
            $data[$key]['goods_name'] = $val['detail_list'][0]['goods_name'];
            $data[$key]['total_amount'] = $val['total_amount'];
            $package_sn = get_exchange_sn_by_package_sn($val['coupon_sn']);
            $data[$key]['package_sn'] = $package_sn ? $package_sn: '--';;
            $data[$key]['coupon_sn'] = $val['coupon_sn'] ? $val['coupon_sn'] : '--';
            $data[$key]['discount_amount'] = $val['discount_amount'];
            $data[$key]['pay_time_str'] = $val['pay_time_str'];
            $data[$key]['sign_time'] = date('Y-m-d H:i:s',$val['sign_time']);
            $data[$key]['sign_time'] = date('Y-m-d H:i:s',$val['sign_time']);
            $data[$key]['service_time'] = date('Y-m-d H:i:s',$val['detail_list'][0]['service_time']);
            $data[$key]['service_address'] = $val['detail_list'][0]['service_address'];
            $data[$key]['status_str2'] = $val['status_str2'];
            $data[$key]['org_user_name'] = $val['org_user_id'] == 0 ? '��' : $pai_organization_obj->get_org_name_by_user_id($v['org_user_id']);;
            $data[$key]['audit_name'] = $mall_certificate_service_obj->get_user_option_name($val['type_id'],$val['seller_user_id']);
        }
        unset($list);
        $fileName = '�����б�';
        $headArr  = array("����ID","�������","�̼��ǳ�","�̼�ID","����ǳ�","���ID","��ƷƷ��","��ƷID","��Ʒ��","���׶�","�ײ���","�Ż���","�Ż�ȯ���","����ʱ��","ǩ��ʱ��","����ʱ��","����ص�","����״̬","�̼һ�������","�̼������Ա");
        Excel_v2::start($headArr,$data,$fileName);
        exit;
    }
    $total_count = $mall_order_obj->get_order_full_list(0,8,true,$where_str);
    $page_obj->set($show_count,$total_count);
    $list = $mall_order_obj->get_order_full_list(0,8,false, $where_str,'sign_time DESC,close_time DESC,order_id DESC',$page_obj->limit(), $fields='*');
    if(!is_array($list)) $list = array();
    foreach($list as &$v)
    {
        $v['detail_list'][0]['service_address_cut'] = poco_cutstr($v['detail_list'][0]['service_address'], 20, '....');
        $v['org_user_name'] = $v['org_user_id'] == 0 ? '��' : $pai_organization_obj->get_org_name_by_user_id($v['org_user_id']);
        $v['audit_name'] = $mall_certificate_service_obj->get_user_option_name($v['type_id'],$v['seller_user_id']);
        $package_sn = get_exchange_sn_by_package_sn($v['coupon_sn']);
        $v['package_sn']  = $package_sn ? $package_sn: '--';
    }
}

//��ȡ�ײ���
function get_exchange_sn_by_package_sn($coupon_sn)
{
    $coupon_sn = trim($coupon_sn);
    if(strlen($coupon_sn)<1) return '--';
    $sql_str = "SELECT E.package_sn FROM pai_coupon_db.coupon_exchange_ref_coupon_tbl C,pai_coupon_db.coupon_exchange_tbl AS E WHERE E.exchange_id=C.exchange_id AND C.coupon_sn ='{$coupon_sn}'";
    $result = db_simple_getdata($sql_str,true,101);
    if(!is_array($result)) $result = array();
    return trim($result['package_sn']);
}



$tpl->assign($setParam);
$tpl->assign('list',$list);
$tpl->assign('page',$page_obj->output(true));
$tpl->assign('YUE_ADMIN_V2_ADMIN_TEST_HEADER',$_YUE_ADMIN_V2_ADMIN_TEST_HEADER);
$tpl->output();



