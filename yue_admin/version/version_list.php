<?php 
	include_once 'common.inc.php';
    include_once 'top.php';
    
    $tpl = new SmartTemplate("version_list.tpl.htm"); 
    
    $sql_str = "SELECT * FROM pai_admin_db.pai_release_tbl WHERE 1=1 "; 
    
    if($_POST['status'] > 0)
    {
        $sql_str .= " AND status = " . $_POST['status'];    
    }
    
    if($_POST['sys'] <> '')
    {
        $sql_str .= " AND sys = '{$_POST['sys']}'";
    }
    
    
    $sql_str .= " ORDER BY add_time DESC";
    $result = db_simple_getdata($sql_str, FALSE, 101);
    
    global $array_status;
    
    foreach($result AS $key=>$val)
    {
        $version_list[$key]                 = $val;
        $version_list[$key]['status_name']  = $array_status[$val[status]];
    }
    
    $status = 'status_' . $_POST['status'];
     
    $tpl->assign($status, 'selected');
    $tpl->assign($_POST['sys'], 'selected');
    $tpl->assign('version_list', $version_list);
    $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();
 ?>