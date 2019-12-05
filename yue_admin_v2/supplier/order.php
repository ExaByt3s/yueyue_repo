<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
include_once 'common.inc.php';
$task_supplier_obj = POCO::singleton('pai_mall_supplier_class');
$order_status_config = pai_mall_load_config('order_status');

/* 订单列表 */
if( $action == 'list'|| $action == '' )
{
    $order_list = yue_admin_v2_supplier_get_order_list($_INPUT);
    $tpl = new SmartTemplate( SUPPLIER_TEMPLATES_ROOT."order_list.tpl.htm" );

    /* 导单 */
    if ( $_INPUT['output']==1 )
    {
        $head_arr = array(
            '订单ID','订单编码','地区','订单状态','交易品类','消费者ID','消费者昵称','商家ID','商家昵称','数量|时长',
            '总价','优惠','付款时间','签到时间','签到状态','签到人','机构',
        );
        $output_data = yue_admin_v2_supplier_order_list_export_data($order_list['list']);
        $fileName = "订单";
        $title = '订单';
//        getExcel($fileName, $head_arr, $output_data);
        export_data($fileName,$title,$head_arr,$output_data);
        exit;
    }

    /* 模板赋值 */
    $tpl->assign( 'filter',$order_list['filter'] );
    $tpl->assign( 'order_amount',$order_list['order_amount'] );
    $tpl->assign( 'page', $order_list['page'] );
    $tpl->assign( 'list', $order_list['list'] );
    $tpl->assign( 'type_list', yue_admin_v2_supplier_get_type() );
    $tpl->assign( 'type_id', $order_list['filter']['type_id'] );
}
/* 订单详情 */
elseif( $action == 'detail' )
{
    $tpl = new SmartTemplate( SUPPLIER_TEMPLATES_ROOT."order_detail.tpl.htm" );
    $code_sn = trim($_INPUT['code_sn']);
    $is_sign = intval($_INPUT['is_sign']);
    $order_info = '';

    if( $is_sign )
    {
        $ret = yue_admin_v2_supplier_sign_order($code_sn);
        echo "<script type='text/javascript'>"
            ." alert('".$ret['message']."'); "
            ."</script>";
    }

    if( $code_sn )
    {
        $order_info = yue_admin_v2_supplier_get_order_info($code_sn);
    }

    $tpl->assign( 'order_info',$order_info );
    $tpl->assign( 'code_sn',$code_sn );
}
elseif( $action == 'supplier_list' )
{
    $tpl = new SmartTemplate( SUPPLIER_TEMPLATES_ROOT."supplier.tpl.htm" );
    $supplier_list = $task_supplier_obj->get_supplier_list();
    $tpl->assign( 'supplier_list',$supplier_list );
}

/* 显示模板 */
$tpl->output ();

/**
 * 订单列表
 * @return array
 */
function yue_admin_v2_supplier_get_order_list($input)
{
    global $task_supplier_obj;
    global $order_status_config;

    /* 构造过滤条件 */
    $filter['action'] = 'list';
    $filter['status'] = isset($input['status']) ? intval($input['status']) : -1;
    $filter['keyword'] = isset($input['keyword']) ? trim($input['keyword']) : '';
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
    $filter['buyer_user_id'] = empty($input['buyer_user_id']) ? '' : trim($input['buyer_user_id']);
    $filter['seller_user_id'] = empty($input['seller_user_id']) ? '' : trim($input['seller_user_id']);

    /* 构造查询条件 */
    $where = '';
    if( $filter['keyword'] )
    {
        $where .= " AND (o.order_id = {$filter['keyword']} or o.order_sn like '%".pai_mall_change_str_in($filter['keyword'])."%') ";
    }
    if( $filter['is_pay']>-1 )
    {
        $where .= " AND o.is_pay = {$filter['is_pay']} ";
    }
    if( $filter['min_total'] )
    {
        $where .= " AND o.total_amount >= {$filter['min_total']} ";
    }
    if( $filter['max_total'] )
    {
        $where .= " AND o.total_amount <= {$filter['max_total']} ";
    }
    if( $filter['min_discount'] )
    {
        $where .= " AND o.discount_amount >= {$filter['min_discount']} ";
    }
    if( $filter['max_discount'] )
    {
        $where .= " AND o.discount_amount <= {$filter['max_discount']} ";
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
        $where .= " AND org_user_id = {$filter['org_user_id']} ";
    }
    if( $filter['seller_user_id'] )
    {
        $where .= " AND o.seller_user_id = {$filter['seller_user_id']} ";
    }
    if( $filter['buyer_user_id'] )
    {
        $where .= " AND o.buyer_user_id = {$filter['buyer_user_id']} ";
    }

    /* 分页数据 */
    $page_arr['page'] = empty($input['page']) || (intval($input['page']) <= 0) ? 1 : intval($input['page']);
    if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
    {
        $page_arr['page_size'] = intval($input['page_size']);
    }
    else
    {
        $page_arr['page_size'] = 20;
    }
    $order_count = $task_supplier_obj->get_order_list($filter['type_id'],$filter['status'],true, $where);
    $page_arr['page_count'] = $order_count['total'];
    // 分页对象
    $page_obj = new show_page();
    // 设置分页参数
    $page_obj->setvar($filter);
    $page_obj->set($page_arr['page_size'], $page_arr['page_count']);
    if( $input['output']==1 )
    {
        $limit = "0,1000000";
    }
    else
    {
        $limit = $page_obj->limit();
    }
    $page = $page_obj->output(1);

    /* 获取订单列表 */
    $list = $task_supplier_obj->get_order_list($filter['type_id'],$filter['status'],false, $where,'o.order_id ', $limit);
    /* 格式化数据 */
    foreach($list as $key => &$val)
    {
        $val['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
        $val['status'] = $order_status_config[$val['status']];
        $val['type_id'] = yue_admin_v2_supplier_get_type($val['type_id']);
        $val['seller_user_name'] = get_user_nickname_by_user_id($val['seller_user_id'])."</br>[{$val['seller_user_id']}]";
        $val['buyer_user_name'] = get_user_nickname_by_user_id($val['buyer_user_id'])."</br>[{$val['buyer_user_id']}]";
        $val['org_user_name'] = get_user_nickname_by_user_id($val['org_user_id']);
        $val['is_pay'] = $val['is_pay'] == 0 ? '否' : '是';
        $val['is_use_coupon'] = $val['is_use_coupon'] == 0 ? '否' : '是';
        $val['goods_number'] = $val['detail_list'][0]['quantity'];
    }

    if( $filter['sign_time_begin'] ) $filter['sign_time_begin'] = date("Y-m-d",$filter['sign_time_begin']);
    if( $filter['sign_time_end'] ) $filter['sign_time_end'] = date('Y-m-d',$filter['sign_time_end']);
    if( $filter['add_time_begin'] ) $filter['add_time_begin'] = date("Y-m-d",$filter['add_time_begin']);
    if( $filter['add_time_end'] ) $filter['add_time_end'] = date("Y-m-d",$filter['add_time_end']);
    if( $filter['pay_time_begin'] ) $filter['pay_time_begin'] = date('Y-m-d',$filter['pay_time_begin']);
    if( $filter['pay_time_end'] ) $filter['pay_time_end'] = date('Y-m-d',$filter['pay_time_end']);

    $order_amount = $order_count['order_amount'];

    $filter['output'] = 1;
    /* 将filter数组转换为url为模板方便使用 */
    $filter['url'] = http_build_query($filter);

    return array('list'=>$list, 'filter'=>$filter, 'page'=>$page, 'order_amount'=>$order_amount);
}

/**
 * 获取品类信息
 * @param string $type_id 品类id
 * @return string
 */
function yue_admin_v2_supplier_get_type($type_id = '')
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
 * 导单数据整理
 * @param $order_list
 * @return array
 */
function yue_admin_v2_supplier_order_list_export_data($order_list)
{
    $output_data = array();
    foreach($order_list as $key => $val)
    {
        $detail_list = POCO::singleton('pai_mall_order_class')->get_detail_list_all($val['order_id']);
        $output_data[$key]['order_id'] = $val['order_id'];
        $output_data[$key]['order_sn'] = $val['order_sn'];
        $output_data[$key]['service_address'] = $detail_list[0]['service_address'];
        $output_data[$key]['status'] = $val['status'];
        $output_data[$key]['type_id'] = $val['type_id'];
        $output_data[$key]['buyer_user_id'] = $val['buyer_user_id'];
        $output_data[$key]['buyer_user_name'] = get_user_nickname_by_user_id($val['buyer_user_id']);
        $output_data[$key]['seller_user_id'] = $val['seller_user_id'];
        $output_data[$key]['seller_user_name'] = get_user_nickname_by_user_id($val['seller_user_id']);
        $output_data[$key]['service_time'] = date("Y-m-d H:i:s", $detail_list[0]['service_time']);
        $output_data[$key]['total_amount'] = $val['total_amount'];
        $output_data[$key]['discount_amount'] = $val['discount_amount'];
        $output_data[$key]['pay_time'] = $val['pay_time'] > 0 ? date("Y-m-d H:i:s", $val['pay_time']) : '未支付';
        $output_data[$key]['sign_time'] = $val['sign_time'] > 0 ? date("Y-m-d H:i:s", $val['sign_time']) : '未支付';
        $output_data[$key]['is_sign'] = $val['sign_time'] > 0 ? '已签到' : '未签到';
        if( $val['sign_by'] == 'sys' )
        {
            $output_data[$key]['sign_by'] = '系统自动签到';
        }
        elseif( $val['sign_by'] == 'buyer' )
        {
            $output_data[$key]['sign_by'] = '商家签到';
        }
        elseif( $val['sign_by'] == '' )
        {
            $output_data[$key]['sign_by'] = '未签到';
        }
        $output_data[$key]['org_user_name'] = $val['org_user_name'];
    }
    return $output_data;
}

/**
 * 查询订单信息
 * @param int $code_sn 签到码
 * @return array
 */
function yue_admin_v2_supplier_get_order_info($code_sn)
{
    global $task_supplier_obj;
    $order_ret = $task_supplier_obj->sign_order($code_sn,SUPPLIER_ADMIN_USER_ID,0);

    if( $order_ret['result']!=1 )
    {
        echo "<script type='text/javascript'>"
            . "alert('" . $order_ret['message'] . "');"
            . "</script>";
    }

    $order_info = $order_ret['message'];

    if( $order_ret['result']==1 )
    {
        $order_info['add_time'] = date('Y-m-d', $order_info['add_time']);
        $order_info['is_pay'] = $order_info['is_pay'] == 0 ? '否' : '是';
        $is_check = $order_info['code_list'][0]['is_check'];
        $order_info['is_check'] = $is_check;
        $order_info['code_sn'] = $order_info['code_list'][0]['code_sn'];
        $order_info['is_check_str'] = '是';
        if ($is_check == 0) {
            $order_info['is_check_str'] = '否';
        }
    }

    return $order_info;
}

/**
 * 签到订单
 * @param int $code_sn 签到码
 * @return mixed
 */
function yue_admin_v2_supplier_sign_order($code_sn)
{
    global $task_supplier_obj;
    $ret = $task_supplier_obj->sign_order($code_sn,SUPPLIER_ADMIN_USER_ID,1);
    return $ret;
}


function export_data($fileName,$title,$headArr,$data)
{
    if(empty($data) || !is_array($data)){
        die("data must be a array");
    }
    if(empty($fileName)){
        exit;
    }
    $date = date("Y_m_d",time());
    $fileName .= "_{$date}.xls";

    //创建新的PHPExcel对象
    $objPHPExcel = new PHPExcel();
    $objProps = $objPHPExcel->getProperties();
    //设置表头
    $key = ord("A");
    $objActSheet = $objPHPExcel->getActiveSheet();
    $objActSheet->getRowDimension('1')->setRowHeight(22);

    foreach($headArr as $key => $v){
        $colum = PHPExcel_Cell::stringFromColumnIndex($key);
        $objActSheet->getColumnDimension($colum)->setWidth(20);
        $objActSheet->getStyle($colum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objActSheet->getStyle($colum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $v = iconv('GB2312','utf-8', $v);
        $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
    }

    $column = 2;
    $objActSheet = $objPHPExcel->getActiveSheet();
    foreach($data as $key => $rows){ //行写入
        $span = 0;
        foreach($rows as $keyName=>$value){// 列写入
            $j = PHPExcel_Cell::stringFromColumnIndex($span);
            $objActSheet->getColumnDimension($j)->setWidth(20);
            $value = iconv('GB2312', 'utf-8',  $value);
            $objActSheet->setCellValue($j.$column, $value);
            $span++;
        }
        $column++;
    }

    //$fileName = iconv("utf-8", "gb2312", $fileName);
    //重命名表
    //$objActSheet->getColumnDimension( 'B')->setAutoSize(true);   //内容自适应
    $objPHPExcel->getActiveSheet()->setTitle(iconv('GB2312', 'utf-8',  $title));
    //设置活动单指数到第一个表,所以Excel打开这是第一个表
    $objPHPExcel->setActiveSheetIndex(0);
    //将输出重定向到一个客户端web浏览器(Excel2007)
    //ob_end_clean();//清除缓冲区,避免乱码
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    /*   if(!empty($_GET['excel'])){
         $objWriter->save('php://output'); //文件通过浏览器下载
     }else{
       $objWriter->save($fileName); //脚本方式运行，保存在当前目录
     }*/
    $objWriter->save('php://output');
    exit;
}

?>