<?php
ignore_user_abort(true);
set_time_limit(36000);
ini_set('memory_limit', '512M');

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$task_goods_obj = POCO::singleton('pai_mall_goods_class');

for($i=0; $i<194269; $i=$i+1000)
{
    $sql_str = "SELECT param FROM yueyue_interface_db.interface_analysis_log_tbl_for_sell_services LIMIT $i, 1000";
    $result = db_simple_getdata($sql_str, FALSE, 22);
    foreach($result AS $key=>$val)
    {
        $param = unserialize($val['param']);
        if($param['goods_id'])
        {
            $goods_id = (int)$param['goods_id'];
            $tmp_result = $task_goods_obj->get_goods_info($goods_id);

            $user_id = $param['user_id']?$param['user_id']:0;

            $type_id = $tmp_result['goods_data']['type_id'];



            switch($type_id)
            {
                case 31:
                    $type_name = $tmp_result['goods_att'][46];
                    break;

                case 5:
                    $type_name = $tmp_result['goods_att'][62];
                    break;

                case 40:
                    $type_name = $tmp_result['goods_att'][90];
                    break;

                case 43:
                    $type_name = $tmp_result['goods_att'][278];
                    break;

                case 12:
                    $type_name = $tmp_result['goods_att'][17];
                    break;

                case 41:
                    $type_name = $tmp_result['goods_att'][219];
                    break;

                case 3:
                    $type_name = $tmp_result['goods_att'][68];
                    break;
            }

            if($type_id && $goods_id)
            {
                $type_name = strip_tags($type_name);
                $sql_str = "INSERT INTO yueyue_interface_db.interface_data_tbl(type_id, type_name, user_id, goods_id)
                    VALUES ($type_id, :x_type_name, $user_id, $goods_id)";
                sqlSetParam($sql_str, 'x_type_name', $type_name);
                db_simple_getdata($sql_str, TRUE, 22);
            }


        }

    }
}

exit();
$sql_str = "SELECT * FROM pai_user_library_db.model_stature_tbl";
$result  = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{

    $model_card_info    = get_model_card_info($val['uid']);
    
    /**
    if($val['height'] == 0 && $model_card_info['height'] <> 0)
    {
        $sql_str = "UPDATE pai_user_library_db.model_stature_tbl SET height='{$model_card_info[height]}' WHERE uid=$val[uid]";
        echo $sql_str;
        echo "<BR>";
        db_simple_getdata($sql_str, TRUE, 101);
    }
    **/
    /**
    if($val['weight'] == 0 && $model_card_info['weight'] <> 0)
    {
        $sql_str = "UPDATE pai_user_library_db.model_stature_tbl SET weight='{$model_card_info[weight]}' WHERE uid=$val[uid]";
        echo $sql_str;
        echo "<BR>";
        db_simple_getdata($sql_str, TRUE, 101);        
    }
    **/
    
    //print_r($model_card_info);
    //echo $num++;
}


function get_user_info($user_id)
{
    $sql_str = "SELECT * FROM pai_db.pai_user_tbl WHERE user_id=$user_id";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    
    return $result;
}

function get_model_card_info($user_id)
{
    $sql_str = "SELECT * FROM pai_db.pai_model_card_tbl WHERE user_id=$user_id";
    $result = db_simple_getdata($sql_str, TRUE, 101);
    
    return $result;
}
?>