<?php
set_time_limit(0);
include_once "../poco_app_common.inc.php";
include_once "pai_score_config.inc.php";
global $operate_array_num;

//while(1)
{
    $sql_str = "SELECT * FROM pai_score_db.pai_operate_queue_tbl WHERE run_state = 0 ORDER BY id ASC";
    $result  = db_simple_getdata($sql_str, FALSE, 101);
    if($result)
    {
        foreach($result AS $key=>$val)
        {
            switch($val['operate'])
            {
                case 'consume':
                    insert_consume_record($val['user_id'], $val['operate_num']);
                    break;
                    
                case 'income':
                    insert_income_record($val['user_id'], $val['operate_num']);
                    break;
                    
                case 'regedit':
                    insert_record($val['user_id'], $val['operate'], $val['operate_num']);               
                    break;
                
                case 'update_model':
                    if(check_update_model($val['user_id']))
                    {
                        insert_record($val['user_id'], $val['operate'], $val['operate_num']);
                    }
                    break;
            }
            
            update_run_state($val);           
        }
        //update_user_level();        
    }
    update_user_level_v2();
//    sleep(1);
}

function update_run_state($result, $state = 1)
{
    $id = $result['id'];
    $sql_str = "UPDATE pai_score_db.`pai_operate_queue_tbl` 
                SET run_state = $state 
                WHERE id = $id";
    db_simple_getdata($sql_str, TRUE, 101);
}

function update_user_level_v2()
{
    $sql_str = "SELECT * FROM pai_score_db.pai_user_score_tbl";
    $result = db_simple_getdata($sql_str, FASEL, 101);
    foreach($result AS $key=>$val)
    {
        if($val['recently_score'] > 0 && $val['recently_score'] <= 149) $level=1;
        if($val['recently_score'] >= 150 && $val['recently_score'] <= 499) $level=2;
        if($val['recently_score'] >= 500 && $val['recently_score'] <= 1999) $level=3;
        if($val['recently_score'] >= 2000 && $val['recently_score'] <= 6499) $level=4;
        if($val['recently_score'] >= 6500 && $val['recently_score'] <= 20000) $level=5;
        if($val['recently_score'] >= 20000) $level=6;
        
        $sql_str = "UPDATE pai_score_db.pai_user_score_tbl SET level = $level WHERE user_id=$val[user_id]";
        db_simple_getdata($sql_str, TRUE, 101);
        if(db_simple_get_affected_rows() == 1)
        {
            $to_user_id = $val['user_id'];
            $old_level = $level-1;
            if($old_level < 1) $old_level=1;
            $content    = '恭喜,您的等级已从' . $old_level . '级提升到'. $level .'级啦!';
            send_message_for_10002($to_user_id, $content);
        }
    }
}

function create_table($datetime)
{
    $datetime = date('Y-m-d H:i:s', strtotime($datetime));
    
}

function insert_consume_record($user_id, $score)
{
    return FALSE;
}

function insert_income_record($user_id, $score)
{
    global $operate_array_num;
    $score = (int)$score * $operate_array_num['income'] + 50;
    $date_time = date('Y-m-d H:i:s');
    $sql_str = "UPDATE pai_score_db.pai_user_score_tbl SET 
                total_score=total_score+$score, 
                effective_score=effective_score+$score, 
                recently_score=recently_score+$score 
                WHERE user_id = $user_id";
    
    db_simple_getdata($sql_str, TRUE, 101);
    if(db_simple_get_affected_rows() < 1)
    {
        $sql_str = "INSERT IGNORE INTO pai_score_db.pai_user_score_tbl(user_id, total_score, effective_score, recently_score, level) 
                    VALUES ($user_id, $score, $score, $score, 1)";
        db_simple_getdata($sql_str, TRUE, 101);
    }
    insert_operation_record($user_id, 'income', $score);
    echo $sql_str . "<BR>";
    
    //$to_user_id = $user_id;
    //$content    = '恭喜,您获得了' . $score .  '分';
    //send_message_for_10002($to_user_id, $content);
    return TRUE;
}

function insert_record($user_id, $operate, $score)
{
    global $operate_array_num;
    $score = (int)($score * $operate_array_num[$operate]);
    $date_time = date('Y-m-d H:i:s');
    $sql_str = "UPDATE pai_score_db.pai_user_score_tbl SET 
                total_score=total_score+$score, 
                effective_score=effective_score+$score, 
                recently_score=recently_score+$score 
                WHERE user_id = $user_id";
    
    db_simple_getdata($sql_str, TRUE, 101);
    if(db_simple_get_affected_rows() < 1)
    {
        $sql_str = "INSERT IGNORE INTO pai_score_db.pai_user_score_tbl(user_id, total_score, effective_score, recently_score, level) 
                    VALUES ($user_id, $score, $score, $score, 1)";
        db_simple_getdata($sql_str, TRUE, 101);
    }
    insert_operation_record($user_id, $operate, $score);
    //echo $sql_str . "<BR>";
    //$to_user_id = $user_id;
    //$content    = '恭喜,您获得了' . $score .  '分';
    //send_message_for_10002($to_user_id, $content);
    return TRUE;
}

function update_user_level()
{
     $sql_str = "UPDATE pai_score_db.pai_user_score_tbl 
                SET level = 2 
                WHERE recently_score > 50 AND recently_score <= 499 ";
     db_simple_getdata($sql_str, TRUE, 101);

     $sql_str = "UPDATE pai_score_db.pai_user_score_tbl 
                SET level = 3 
                WHERE recently_score > 499 AND recently_score <= 1999";
     db_simple_getdata($sql_str, TRUE, 101);   
     
     $sql_str = "UPDATE pai_score_db.pai_user_score_tbl 
                SET level = 4
                WHERE recently_score > 19999 AND recently_score <= 6499";
     db_simple_getdata($sql_str, TRUE, 101);  
     
     $sql_str = "UPDATE pai_score_db.pai_user_score_tbl 
                SET level = 5 
                WHERE recently_score > 6499";
     db_simple_getdata($sql_str, TRUE, 101);           
}

function insert_operation_record($user_id, $operation, $score, $operation_time = 0)
{
    if($operation_time == 0) $operation_time = date('Y-m-d', time()+3600*24*90);
    $sql_str = "INSERT INTO pai_score_db.pai_operation_tbl(user_id, operation, operation_num, operation_time) 
                VALUES ('{$user_id}', '{$operation}', '{$score}', '{$operation_time}')";
    db_simple_getdata($sql_str, TRUE, 101);
}

function check_update_model($login_id)
{
    $sql_str = "INSERT IGNORE INTO pai_score_db.pai_update_model_log_tbl(login_id) VALUES ($login_id)";
    db_simple_getdata($sql_str, TRUE, 101);
    return db_simple_get_affected_rows();
}
?>