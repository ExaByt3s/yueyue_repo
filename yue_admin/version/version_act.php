<?php
include_once 'common.inc.php';
include_once 'top.php';

switch($_POST['step'])
{
    case 1:
        $insert_id = yueyue_version_add();
        if($insert_id){
            header("location:version_submit.php?id=$insert_id");
            exit(); 
        }       
        break;
    
    case 2:
        $id             = $_POST['id'];
        $software_md5   = $_POST['software_md5'];
        if(yueyue_version_update($id, $software_md5, 2))
        {
            header("location:version_list.php");
            exit();             
        }
        break;
        
    case 3:
    case 4:
    case 5:
        $id             = $_POST['id'];
        $software_md5   = $_POST['software_md5'];
        if(yueyue_version_update($id, $software_md5, $_POST['step']))
        {
            header("location:version_list.php");
            exit();             
        }
        break;

        
    case 6:
        $id             = $_POST['id'];
        $software_md5   = $_POST['software_md5'];
        if(yueyue_version_update($id, $software_md5, 6))
        {
            header("location:index.php");
            exit();             
        }
        break;
        
    default:
        break;
}

function yueyue_version_add()
{
    $software_name      = $_POST['software_name'];
    $software_url       = $_POST['software_url'];
    $software_version   = $_POST['software_version'];
    $software_sys       = $_POST['software_sys'];
    $software_remark    = $_POST['software_remark'];
    
    $software_md5       = md5(file_get_contents($software_url));
    
    if($software_md5)
    {
        $add_time = date('Y-m-d H:i:s');
        $sql_str = "INSERT INTO pai_admin_db.pai_release_tbl(url, url_md5, version, sys, remark, add_time, status, update_id) 
                    VALUES ('{$software_url}', '{$software_md5}', '{$software_version}', '{$software_sys}', '{$software_remark}', '{$add_time}', '1', '{$yue_login_id}')";
        db_simple_getdata($sql_str, TRUE, 101);
        return db_simple_get_insert_id();
    }else{
        die('创建MD5失败');
    }
}

function yueyue_version_update($id, $md5, $step)
{
    $sql_str = "SELECT * FROM pai_admin_db.pai_release_tbl WHERE id='{$id}' AND url_md5='{$md5}'";
    echo $sql_str;
    $result = db_simple_getdata($sql_str, TRUE, 101);
    
    if($result['id'])
    {
        $update_time = date('Y-m-d H:i:s');
        $sql_str = "UPDATE pai_admin_db.pai_release_tbl SET status='{$step}', update_time='{$update_time}', update_id='{$yue_login_id}' WHERE id='{$result[id]}'";
        db_simple_getdata($sql_str, TRUE, 101);
        return TRUE;
    }else{
        die('MD5不正确，状态更新失败');
    }
}
?>