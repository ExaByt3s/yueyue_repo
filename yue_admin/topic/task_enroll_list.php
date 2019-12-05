<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
include_once 'common.inc.php';
include_once 'top.php';

$tpl = new SmartTemplate("task_enroll_list.tpl.htm");

$page_obj = new show_page ();

$topic_obj = POCO::singleton('pai_topic_class');


$begin_time = $_INPUT['begin_time'];
$end_time = $_INPUT['end_time'];

//$where = 1;

if($begin_time && $end_time)
{
    $bt = strtotime($begin_time);
    $et = strtotime($end_time)+86400;
    $where .= " AND e.add_time BETWEEN $bt AND $et";
}

$topic_id = (int)$topic_id;
if($topic_id)
{
    $where .= " AND topic_id={$topic_id}";
}

if($type)
{
    $where .= " and type='{$type}'";
}

//����
if($_INPUT['output']==1)
{
    $fileName = "task";
    $headArr = array ("���","Ʒ��","ר������","�û�ID", "����ʱ��" );

    $sql="SELECT e.id,t.`type`,e.topic_id,e.user_id,e.add_time FROM pai_db.pai_task_enroll_tbl e,pai_db.pai_task_topic_tbl t WHERE e.topic_id=t.id $where";
    $list= db_simple_getdata($sql);
    foreach($list as $k=>$val)
    {
        $topic_info = $topic_obj->get_task_detail($val['topic_id']);
        $out_list[$k]['id'] = $k+1;
        $out_list[$k]['type'] = $val['type'];
        $out_list[$k]['topic_name'] = $topic_info['title'];
        $out_list[$k]['user_id'] = $val['user_id'];
        $out_list[$k]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
    }
    getExcel ( $fileName, $headArr, $out_list );
    exit;
}

$topic_list = $topic_obj->get_task_list(false, '', 'id DESC', '0,100');

$show_count = 40;

$page_obj->setvar (array("begin_time"=>$begin_time,"end_time"=>$end_time,"type"=>$type,"topic_id"=>$topic_id) );


$sql="SELECT COUNT(*) AS c FROM pai_db.pai_task_enroll_tbl e,pai_db.pai_task_topic_tbl t WHERE e.topic_id=t.id $where";
$count_list= db_simple_getdata($sql,true);

$page_obj->set ( $show_count, $count_list['c'] );


$sql="SELECT e.id,t.`type`,e.topic_id,e.user_id,e.add_time FROM pai_db.pai_task_enroll_tbl e,pai_db.pai_task_topic_tbl t WHERE e.topic_id=t.id $where ORDER BY id DESC LIMIT {$page_obj->limit()}";
$list= db_simple_getdata($sql);

foreach($list as $k=>$val){
	$list[$k]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);

    $topic_info = $topic_obj->get_task_detail($val['topic_id']);
    $list[$k]['topic_name'] = $topic_info['title'];
}

if($topic_id)
{
    $count_enroll =  $topic_obj->get_task_enroll_list(true, "topic_id=".$topic_id);
}


$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('list', $list);
$tpl->assign('topic_list', $topic_list);
$tpl->assign('topic_id', $topic_id);
$tpl->assign('begin_time', $begin_time);
$tpl->assign('end_time', $end_time);
$tpl->assign('end_time', $end_time);
$tpl->assign('type', $type);
$tpl->assign('topic_id', $topic_id);
$tpl->assign('count_enroll', $count_enroll);
$tpl->output();



function getExcel($fileName, $headArr, $data)
{
    if (empty ( $data ) || ! is_array ( $data ))
    {
        die ( "û����" );
    }
    if (empty ( $fileName ))
    {
        exit ();
    }
    $date = date ( "Y_m_d_H_i_s", time () );
    $fileName .= "_{$date}.xlsx";

    //�����µ�PHPExcel����
    $objPHPExcel = new PHPExcel ();
    $objProps = $objPHPExcel->getProperties ();
    //���ñ�ͷ
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
    { //��д��
        $span = ord ( "A" );
        foreach ( $rows as $keyName => $value )
        { // ��д��
            $j = chr ( $span );
            $objActSheet->getColumnDimension ( $j )->setWidth ( 20 );
            $value = iconv ( 'GBK', 'utf-8', $value );
            $objActSheet->setCellValue ( $j . $column, $value );
            $span ++;
        }
        $column ++;
    }

    //$fileName = iconv("utf-8", "gb2312", $fileName);
    //��������
    //$objActSheet->getColumnDimension( 'B')->setAutoSize(true);   //��������Ӧ
    $objPHPExcel->getActiveSheet ()->setTitle ( iconv ( 'GBK', 'utf-8', 'ģ���б�' ) );
    //���û��ָ������һ����,����Excel�����ǵ�һ����
    $objPHPExcel->setActiveSheetIndex ( 0 );
    //������ض���һ���ͻ���web�����(Excel2007)
    //ob_end_clean();//���������,��������
    header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
    header ( "Content-Disposition: attachment; filename=\"$fileName\"" );
    header ( 'Cache-Control: max-age=0' );
    $objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
    /*   if(!empty($_GET['excel'])){
            $objWriter->save('php://output'); //�ļ�ͨ�����������
        }else{
          $objWriter->save($fileName); //�ű���ʽ���У������ڵ�ǰĿ¼
        }*/
    $objWriter->save ( 'php://output' );
    exit ();

}
?>