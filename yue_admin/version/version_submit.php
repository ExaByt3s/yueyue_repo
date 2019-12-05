<?php 
	include_once 'common.inc.php';
    include_once 'top.php';
    
    global $array_status;
    
    $tpl = new SmartTemplate("version_submit.tpl.htm"); 
    
    $id = $_GET['id'];
    
    if($id)
    {
        $sql_str = "SELECT * FROM pai_admin_db.pai_release_tbl WHERE id=$id";  
        $result = db_simple_getdata($sql_str, TRUE, 101);
        
        $tpl->assign('step_str', $array_status[$result['status']]);
        
        $tpl->assign('software_version', $result['version']);
        $tpl->assign('software_sys', $result['sys']);
        $tpl->assign('software_remark', $result['remark']);
        
        if($result['status'] < 5)
        {
            $step = (int)$result['status'] + 1;
            $tpl->assign('step', $step);  
            $tpl->assign('next_step_str', $array_status[$step]);            
        }
        
        if($_GET['type'] == 'close')
        {
            $step = 6;
            $tpl->assign('step', $step);  
            $tpl->assign('next_step_str', $array_status[$step]);                
        }

    }
     
    $tpl->assign('id', $id);
    $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();
 ?>