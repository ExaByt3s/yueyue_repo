<?php
/**
 * @desc: 服务数据
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/26
 * @Time:   18:10
 * version: 1.0
 */
ignore_user_abort(true);
set_time_limit(36000);
ini_set('memory_limit', '512M');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

exit;
$task_goods_obj = POCO::singleton( 'pai_mall_goods_class' );//商品

for($i=67;$i<=200;$i++)
{
    $start_num = $i*1000;
    $sql_str = "SELECT param FROM yueyue_interface_db.interface_analysis_log_tbl_for_sell_services LIMIT {$start_num},1000";
    $result = db_simple_getdata($sql_str, FALSE, 22);
    if(empty($result)) exit("数据为空");
    echo $sql_str."\n<br/>";
//print_r($result);
    foreach($result AS $key=>$val)
    {
        //print_r($val);
        $param = unserialize($val['param']);
        //var_dump($param);
        if($param['goods_id'])
        {
            $goods_id = (int)$param['goods_id'];
            $tmp_result = $task_goods_obj->get_goods_info($goods_id);

            $user_id = $param['user_id']?$param['user_id']:0;
            //print_r($tmp_result);
            //print_r($tmp_result['goods_data']['type_id']);
            $user_id = (int)$user_id;
            $type_id = $tmp_result['goods_data']['type_id'];
            $type_id = (int)$type_id;


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
                    VALUES ($type_id, '{$type_name}', $user_id, $goods_id)";
                try{
                    db_simple_getdata($sql_str, TRUE, 22);
                }catch (Exception $e)
                {
                    echo '链接有误Caught exception: ',  $e->getMessage(), "\n";
                }
            }


        }

    }
}