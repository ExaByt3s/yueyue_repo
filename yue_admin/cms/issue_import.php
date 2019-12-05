<?php
/**
 * issue 用户导入
 * @author xiao xiao
 */

/**
 * common
 */
include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php';
ini_set('memory_limit', '256M');
define("G_YUE_CMS_CHECK_ADMIN",1);
include_once("cms_common.inc.php");
/**
 * 模板
 */
//$tpl = new SmartTemplate("cms_import.tpl.htm");

/**
 * 初始化参数　
 */
$act = $_INPUT['act'] ? $_INPUT['act'] : 'import';
$issue_id = $_INPUT["issue_id"]*1;
if(!$issue_id)
{
    echo "<script type='text/javascript'>window.alert('导入数据格式不正确');parent.location.reload();</script>";;exit;
}
//导入
if ($act == 'import') 
{
    $filename = $_FILES['inputExcel']['name'];
    $prefixName = getPrefix($filename);
    if ($prefixName != '.xls') 
    {
        echo "<script type='text/javascript'>window.alert('导入数据格式不正确');parent.location.reload();</script>";
        exit;
    }
    $tmp_name = $_FILES['inputExcel']['tmp_name'];
    $info = uploadFile($filename,$tmp_name,$issue_id);
    if ($info) 
    {
        echo "<script type='text/javascript'>window.alert('导入成功');parent.location.reload();</script>";
        exit;
        
    }
    echo "<span style='font-size:14px;line-height:24x;'>导入失败！<a href='cms_upload_excel_frm.php?issue_id={$issue_id}'>重新导入</a><span>";
    exit;
    
}
//导出数据
elseif ($act == 'export') 
{
    $count = cms_system_class::get_record_list_by_issue_id(true, $issue_id);
    $list  = cms_system_class::get_record_list_by_issue_id(false, $issue_id, "0,{$count}");
    if (!is_array($list) || empty($list)) 
    {
            echo "<script type='text/javascript'>window.alert('数据为空');parent.location.reload();</script>";
            exit;
    }
    $data = array();
    foreach($list as $key=>$vo)
    {
        $data[$key]['user_id']   = $vo['user_id'];
        $data[$key]['user_name'] = $vo['user_name'];
        $data[$key]['img_url']   = $vo['img_url'];
    }
    $fileName = "榜单期数表";
    $title    = "榜单列表";
    $headArr = array('user_id', 'APP用户名', '图片链接');
    getExcel($fileName,$title,$headArr,$data);
    exit;
}



function uploadFile($file,$filetempname,$issue_id)
{
	 setlocale(LC_ALL, 'zh_CN');
	 $objReader     = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format 
	 //$objReader->setInputEncoding('GB2312');
	 $objPHPExcel   = $objReader->load($filetempname); 
	 $sheet         = $objPHPExcel->getSheet(0);
	 $highestRow    = $sheet->getHighestRow();   //取得总行数 
	 $highestColumn = $sheet->getHighestColumn(); //取得总列数
	 $objWorksheet  = $objPHPExcel->getActiveSheet();
	 $highestRow    = $objWorksheet->getHighestRow(); 
	 $highestColumn = $objWorksheet->getHighestColumn();
	 $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
	 $arr = array();
	 for ($row = 2;$row <= $highestRow; $row ++)
	 {
	 	$strs = array();
        //注意highestColumnIndex的列数索引从0开始
        for ($col = 0; $col < $highestColumnIndex ; $col++)
        {
            //$strs[$col] = trim(iconv('utf-8','GB2312', $objWorksheet->getCellByColumnAndRow($col, $row)->getValue()));
            $strs[$col] = trim(iconv('utf-8','GB2312', $objWorksheet->getCellByColumnAndRow($col, $row)->getValue()));
        }
        $user_id = (int)$strs[0];
        if($user_id >= 100000)
        {
        	$info = cms_system_class::add_record_by_issue_id($issue_id,$user_id,'','','','','','','');
        }
	 }
    return true;
}
exit;
?>