<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
include_once 'common.inc.php';
include_once 'top.php';

$tpl = new SmartTemplate("task_topic_list.tpl.htm");

$page_obj = new show_page ();

$topic_obj = POCO::singleton('pai_topic_class');


$title = urldecode($_INPUT['title']);
$begin_time = $_INPUT['begin_time'];
$end_time = $_INPUT['end_time'];
$type = $_INPUT['type'];
$status = $_INPUT['status'];
$add_user_id = $_INPUT['add_user_id'];


if($_GET['act'] == 'act')
{
    $id = $_INPUT['id'];
    $status = $_INPUT['status'];
    $topic_obj->update_task_topic(array("status"=>$status), $id);
    echo "<script>alert('�����ɹ�')</script>";

}

$where = 1;

if($title)
{
    $where .= " and title like '%{$title}%'";
}

if($begin_time && $end_time)
{
    $where .= " and FROM_UNIXTIME(add_time,'%Y-%m-%d') between '{$begin_time}' and '{$end_time}'";
}

if($type)
{
    $where .= " and type='{$type}'";
}

if($add_user_id)
{
    $where .= " and add_user_id='{$add_user_id}'";
}

switch($status)
{
    case "up":
        $where .= " and status=1";
        break;

    case "down":
        $where .= " and status=0";
        break;
}


//����
if($_INPUT['output']==1)
{
    $fileName = "task_topic";
    $headArr = array ("Ʒ��","ר������","��������", "����ʱ��","״̬" );

    $list= $topic_obj->get_task_list(false, $where, 'add_time DESC', '0,1000');
    foreach($list as $k=>$val)
    {
        $out_list[$k]['type'] = $val['type'];
        $out_list[$k]['topic_name'] = $val['title'];
        $out_list[$k]['enroll'] = $topic_obj->get_task_enroll_list(true, "topic_id=".$val['id']);
        $out_list[$k]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
        if($val['status']==1)
        {
            $status="�ϼ�";
        }
        else
        {
            $status="�¼�";
        }
        $out_list[$k]['status'] = $status;


    }
    getExcel ( $fileName, $headArr, $out_list );
    exit;
}


$show_count = 40;
$page_obj->setvar (array("title"=>$title,"begin_time"=>$begin_time,"end_time"=>$end_time,"type"=>$type,"status"=>$status) );

$total_count = $topic_obj->get_task_list(true,$where);

$page_obj->set ( $show_count, $total_count );

$list = $topic_obj->get_task_list(false, $where, 'add_time DESC', $page_obj->limit());

foreach($list as $k=>$val){
	$list[$k]['add_time'] = date("Y-m-d H:i",$val['add_time']);
    $list[$k]['count_enroll'] = $topic_obj->get_task_enroll_list(true, "topic_id=".$val['id']);
}


$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('list', $list);
$tpl->assign('title', $title);
$tpl->assign('begin_time', $begin_time);
$tpl->assign('end_time', $end_time);
$tpl->assign('type', $type);
$tpl->assign('status', $status);
$tpl->assign('add_user_id', $add_user_id);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
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