<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$pai_obj = POCO::singleton('pai_user_class');

$sql_str = 'SELECT `id`, `date_id`,`model_user_id`,`cameraman_user_id`,`comment`
            FROM `pai_db`.`pai_cameraman_comment_log_tbl` WHERE model_user_id IN ("100060","100062","100098","100104","100108","100114","100116","100117","100122","100124","100126","100125","100127","100129","100131","100137","100144","100145","100146","100148","100154","100161","100166","100167","100168","100171","100172","100174","100173","100175","100176","100177","100178","100197","100201","100200","100203","100207","100209","100225","100229","100237","100243","100250","100262","100265","100276","100277","100278","100282","100284","100287","100288","100294","100295","100298","100305","100309","100310","100315","100380","100387","100389","100406","100409","100412","100415","100420","100426","100427","100437","100458","100489","100492","100517","100518","100519","100522","100524","100525","100537","100592","100593","100597","100605","100614","100616","100617","100637","100649","100655","100656","100789","100788","100787","100784","100779","100773","100766","100751","100696","100799","100829","100837","100867","100893","100897","100907","100911","100915","100930","100943","100971","101111","101116","101117","101118","101126","101128","101143","101145","101159","101246","101176","101266","101280","101340","101250","101383")';
$result  = db_simple_getdata($sql_str, FALSE, 101);
echo "<table>";
foreach($result AS $key=>$val)
{
    echo "<tr>";
    echo "<td>" . $val['date_id'] . "</td>";
    echo "<td>" . $val['model_user_id'] . "</td>";
    echo "<td>" . __t_sql_get_user_info($val['model_user_id']) . "</td>";
    echo "<td>" . $val['cameraman_user_id'] . "</td>";
    echo "<td>" . __t_sql_get_user_info($val['cameraman_user_id']) . "</td>"; 
    echo "<td>" . $val['comment'] . "</td>";
    
    $date_info = __t_sql_get_date_info($val['date_id']);
    $date_str = '';
    foreach($date_info AS $key=>$val)
    {
        if($key == 'date_time') 
        {
            $date_str .= $key . ":" . date('Y-m-d H:i:s', $val) . ';'; 
        }else{
            $date_str .= $key . ":" . $val . ';';            
        }

    }
    echo "<td>" . $date_str . "</td>";
    echo "</tr>";
}
echo "</table>";

function __t_sql_get_date_info($date_id)
{
    $sql_str = "SELECT	date_time, date_address,  date_style, date_hour, date_price, source 
                FROM `event_db`.`event_date_tbl` 
                WHERE date_id=$date_id";
    return db_simple_getdata($sql_str, TRUE);
    
}

function __t_sql_get_user_info($user_id)
{
    $sql_str = "SELECT  `nickname` FROM `pai_db`.`pai_user_tbl` WHERE user_id =  $user_id";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    return $result['nickname'];
}


exit();
set_time_limit(3600);
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$cms_obj = new cms_system_class();
$pai_obj = POCO::singleton('pai_user_class');
$comment_score_rank_obj = POCO::singleton('pai_comment_score_rank_class');
$ret = $comment_score_rank_obj->get_comment_rank(101029001,'0,30');

foreach($ret AS $val)
{
    $issue_id = 18;
    $score = (int)($val[num] * 2);
    $nickname = $pai_obj->get_user_nickname_by_user_id($val['user_id']);
    $cms_obj->add_record_by_issue_id($issue_id, $val['user_id'], $nickname, $score);    
}




exit();
$pic_obj   = POCO::singleton ('pai_pic_class');
$pic_array = $pic_obj->get_user_pic(100170, $limit = '0,5');
print_r($pic_array);
foreach($pic_array AS $key=>$val)
{
    $num = explode('?', $val['img']);
    $num = explode('x', $num[1]);
    $num_v2 = explode('_', $num[1]);
    
    $width = $num[0];
    $height = $num_v2[0];
    
    if($width<$height)
    {
        
    }
    
}
$score_val['user_icon'] =  str_replace("_260.", "_440.", $pic_val[0]['img']);
exit();
$cms_obj = new cms_system_class();
$pai_obj = POCO::singleton('pai_user_class');

$sql_str = "SELECT * FROM pai_db.pai_goddess_model_tbl WHERE type = 'test_model'";
$result  = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{
    $issue_id = 27;
    $nickname = $pai_obj->get_user_nickname_by_user_id($val['user_id']);
    $cms_obj->add_record_by_issue_id($issue_id, $val['user_id'], $nickname, $val['sort']);
}


exit();
$sql_str = "SELECT * FROM pai_db.`pai_model_audit_tbl` WHERE is_approval = 1";
$result  = db_simple_getdata($sql_str, FALSE, 101);

foreach($result AS $key=>$val)
{
    insert_pai_model_style_v2_tbl($val['user_id']);
}


function insert_pai_model_style_v2_tbl($user_id)
{
    if(!check_pai_model_style_v2_tbl($user_id))
    {
        $sql_str = "INSERT INTO pai_db.pai_model_style_v2_tbl(user_id, group_id, style, hour, price, continue_price) 
                    VALUES ($user_id, 1, '清新', 2, 50, 50 )";
        echo $sql_str . "<BR>";
        db_simple_getdata($sql_str, TRUE, 101);
    }
}

function check_pai_model_style_v2_tbl($user_id)
{
    $sql_str = "SELECT COUNT(*) AS C FROM pai_db.pai_model_style_v2_tbl WHERE user_id=$user_id";
    $result  = db_simple_getdata($sql_str, TRUE, 101);
    
    if($result['C'] > 0)
    {
        return TRUE;
    }else{
        return FALSE;
    }   
}

exit();
$sql_str = "SELECT * FROM pai_topic_db.pai_topic_user_info_tbl";
$result = db_simple_getdata($sql_str, FALSE, 101);

foreach($result AS $key=>$val)
{
    //t_sql_update_pai_user_tbl($val);
    //t_sql_update_pai_model_card_tbl($val); 
    //t_sql_update_pai_pic_tbl($val);
    //t_sql_file_get_contents($val);
}

//print_r($result);

function t_sql_update_pai_user_tbl($result)
{
    if(t_sql_check_pai_user_tbl($result['yue_user_id']))
    {
        $sql_str = "UPDATE pai_db.pai_user_tbl SET nickname = '{$result[nickname]}' WHERE user_id = {$result[yue_user_id]}";
        echo $sql_str . "<BR>";
        db_simple_getdata($sql_str, TRUE, 101);
    }
}

function t_sql_check_pai_user_tbl($user_id)
{
    $sql_str = "SELECT * FROM pai_db.pai_user_tbl WHERE user_id = $user_id";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    
    $old_name = '手机用户' . $result[cellphone]%10000;
    if($result['nickname'] == $old_name)
    {
        return TRUE;
    }
    return FALSE;
}


function t_sql_update_pai_model_card_tbl($result)
{
    if(!t_sql_check_pai_model_card_tbl($result['yue_user_id']))
    {
        $sql_str = "UPDATE pai_db.pai_model_card_tbl SET chest='{$result[cup_num]}', chest_inch='{$result[cup_num]}', cup='{$result[cup]}', 
                                                         waist='{$result[b_size]}', hip='{$result[h_size]}', height='{$result[height]}', 
                                                         weight='{$result[weight]}' 
                    WHERE user_id = $result[yue_user_id]";
        echo $sql_str; 
        echo "<BR>";
        db_simple_getdata($sql_str, TRUE, 101);
    }
}

function t_sql_check_pai_model_card_tbl($user_id)
{
    $sql_str = "SELECT * FROM pai_db.pai_model_card_tbl WHERE user_id = $user_id";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    
    $return_str = 0;
    if($result['chest'] > 0)    $return_str =1;
    if($result['waist'] > 0)    $return_str =1;
    if($result['hip'] > 0)      $return_str =1;
    if($result['height'] > 0)   $return_str =1;
    if($result['weight'] > 0)   $return_str =1;

    return $return_str;
}

function t_sql_update_pai_pic_tbl($result)
{
    if(!t_sql_check_pai_pic_tbl($result['yue_user_id']))
    {
        $img_array = unserialize($result[img]);
        foreach($img_array AS $val)
        {
            $img        = str_replace('_260.jpg', '.jpg', $val);
            $add_time   = time();
            $sql_str    = "INSERT INTO pai_db.pai_pic_tbl(user_id, img, add_time) 
                            VALUES('{$result[yue_user_id]}', '{$img}', '{$add_time}')";
                            
            echo $sql_str . "<BR>"; 
            db_simple_getdata($sql_str, TRUE, 101);    
        }
        
    }
}

function t_sql_check_pai_pic_tbl($user_id)
{
    $sql_str = "SELECT COUNT(*) AS C FROM pai_db.pai_pic_tbl WHERE user_id = $user_id";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    
    if($result['C'] > 0)
    {
        return TRUE;
    }else{
        return FALSE;
    }
}

function t_sql_file_get_contents($result)
{
    $user_id = $result['yue_user_id'];
    $img_url = $result['icon'];
    if($user_id && $img_url)
    {
        $url = "http://imgup-yue.yueus.com/ultra_upload_service/yueyue_upload_user_icon_act.php?weixin_icon=$img_url&poco_id=$user_id";
        echo $url . "<BR>";
        //file_get_contents($url);
        //exit();
        
        
    }
}
?>