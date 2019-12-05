<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
include_once 'common.inc.php';
include_once 'top.php';

$tpl = new SmartTemplate("promotion_enroll_list.tpl.htm");

$page_obj = new show_page ();

$topic_obj = POCO::singleton('pai_topic_class');
$user_obj = POCO::singleton('pai_user_class');

$type_arr = $topic_obj->promotion_type_conf;

$begin_time = $_INPUT['begin_time'];
$end_time = $_INPUT['end_time'];
$topic_id = (int)$_INPUT['topic_id'];
$type_id = (int)$_INPUT['type_id'];

$where = 1;

if($begin_time && $end_time)
{
    $bt = strtotime($begin_time);
    $et = strtotime($end_time)+86400;
    $where .= " AND add_time BETWEEN $bt AND $et";
}

if($topic_id)
{
    $where .= " AND topic_id={$topic_id}";
}

if($type_id)
{
    $where .= " AND type_id={$type_id}";
}


if($_INPUT['output']==1)
{
    $fileName = "promotion";
    $headArr = array ("专题名称","用户ID","手机","品类", "商品ID", "规格", "数量", "报名时间","导入促销系统" );

    $list = $topic_obj->get_promotion_enroll_list(false, $where, "id desc","0,10000");
    foreach($list as $k=>$val)
    {
        $topic_info = $topic_obj->get_promotion_detail($val['topic_id']);
        $out_list[$k]['topic_name'] = $topic_info['title'];
        $out_list[$k]['user_id'] = $val['user_id'];
        $out_list[$k]['phone'] = $user_obj->get_phone_by_user_id($val['user_id']);
        $out_list[$k]['type'] = $type_arr[$val['type_id']];
        $out_list[$k]['goods_id'] = $val['goods_id'];
        $out_list[$k]['type_text'] = $val['type_text'];
        $out_list[$k]['num'] = $val['num'];
        $out_list[$k]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
        $out_list[$k]['import'] = "mall_order#".$val['goods_id'].'#'.$val['type_key'];
    }
    getExcel ( $fileName, $headArr, $out_list );
    exit;
}

$topic_list = $topic_obj->get_promotion_list(false, '', 'id DESC', '0,1000');

$show_count = 40;

$page_obj->setvar (array("begin_time"=>$begin_time,"end_time"=>$end_time,"topic_id"=>$topic_id,"type_id"=>$type_id) );

$total_count = $topic_obj->get_promotion_enroll_list(true,$where);

$page_obj->set ( $show_count, $total_count );

$list = $topic_obj->get_promotion_enroll_list(false, $where, "id desc",$page_obj->limit());

foreach($list as $k=>$val){
	$list[$k]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);

    $topic_info = $topic_obj->get_promotion_detail($val['topic_id']);

    $list[$k]['topic_name'] = $topic_info['title'];
}


$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('list', $list);
$tpl->assign('begin_time', $begin_time);
$tpl->assign('end_time', $end_time);
$tpl->assign('topic_list', $topic_list);
$tpl->assign('topic_id', $topic_id);
$tpl->assign('type_id', $type_id);

$tpl->output();




function getExcel($fileName, $headArr, $data)
{
    if (empty ( $data ) || ! is_array ( $data ))
    {
        die ( "没数据" );
    }
    if (empty ( $fileName ))
    {
        exit ();
    }
    $date = date ( "Y_m_d_H_i_s", time () );
    $fileName .= "_{$date}.xlsx";

    //创建新的PHPExcel对象
    $objPHPExcel = new PHPExcel ();
    $objProps = $objPHPExcel->getProperties ();
    //设置表头
    $key = ord ( "A" );
    $objActSheet = $objPHPExcel->getActiveSheet ();
    $objActSheet->getRowDimension ( '1' )->setRowHeight ( 22 );
    foreach ( $headArr as $v )
    {
        $colum = chr ( $key );
        $objActSheet->getColumnDimension ( $colum )->setWidth ( 20 );
        $objActSheet->getStyle ( $colum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
        $objActSheet->getStyle ( $colum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::VERTICAL_CENTER );
        $v = iconv ( 'GB2312', 'utf-8', $v );
        $objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( $colum . '1', $v );
        $key += 1;
    }
    //exit;
    $column = 2;
    //$objActSheet = $objPHPExcel->getActiveSheet();
    foreach ( $data as $key => $rows )
    { //行写入
        $span = ord ( "A" );
        foreach ( $rows as $keyName => $value )
        { // 列写入
            $j = chr ( $span );
            $objActSheet->getColumnDimension ( $j )->setWidth ( 20 );
            $value = iconv ( 'GBK', 'utf-8', $value );
            $objActSheet->setCellValue ( $j . $column, $value );
            $span ++;
        }
        $column ++;
    }

    //$fileName = iconv("utf-8", "gb2312", $fileName);
    //重命名表
    //$objActSheet->getColumnDimension( 'B')->setAutoSize(true);   //内容自适应
    $objPHPExcel->getActiveSheet ()->setTitle ( iconv ( 'GBK', 'utf-8', '促销报名列表' ) );
    //设置活动单指数到第一个表,所以Excel打开这是第一个表
    $objPHPExcel->setActiveSheetIndex ( 0 );
    //将输出重定向到一个客户端web浏览器(Excel2007)
    //ob_end_clean();//清除缓冲区,避免乱码
    header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
    header ( "Content-Disposition: attachment; filename=\"$fileName\"" );
    header ( 'Cache-Control: max-age=0' );
    $objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
    /*   if(!empty($_GET['excel'])){
            $objWriter->save('php://output'); //文件通过浏览器下载
        }else{
          $objWriter->save($fileName); //脚本方式运行，保存在当前目录
        }*/
    $objWriter->save ( 'php://output' );
    exit ();

}
?>