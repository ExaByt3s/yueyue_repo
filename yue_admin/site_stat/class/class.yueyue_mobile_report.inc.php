<?php
/**
 * Created by PhpStorm.
 * User: yaohua_he
 * Date: 2015/5/26
 * Time: 14:50
 */
class yueyue_mobile_reprt_class
{

    public function yueyue_mobile_reprt_class()
    {

    }

    public function get_mobile_list($mobile_parent_id, $pg)
    {
        echo "pg:" . $pg;
        $id = (int)$mobile_parent_id;
        $sql_str = "SELECT mobile_id, mobile_parent_id, mobile_type, mobile_name
                    FROM mobile_stat_db.mobile_stat_item_tbl
                    WHERE mobile_parent_id = $id
                    ORDER BY mobile_id DESC";
        $result = db_simple_getdata($sql_str, FALSE, 22);
        if($result)
        {
            foreach($result AS $key=>$val)
            {
                /*if($pg) {
                    for ($i = 1; $i <= $pg; $i++) {
                        echo " ";
                    }
                }*/
                echo $val['mobile_id'] . "<BR>";
                $pg++;
                $this->get_mobile_list($val['mobile_id'], $pg);

            }
        }else{
            return FALSE;
        }

    }
}

?>