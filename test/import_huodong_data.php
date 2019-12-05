<?php


include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



function tongbu_bill_num()
{
    $sql="SELECT user_id,event_id,COUNT(*) AS c FROM pai_db.pai_activity_code_tbl GROUP BY event_id ORDER BY c DESC;";
    $arr=db_simple_getdata($sql);
    foreach($arr as $val)
    {
        $bill_num = $val['c'];
        $sql="update test.test_event_info_tbl set bill_num='$bill_num' where event_id=".$val['event_id'];
        db_simple_getdata($sql);
    }
}


function tongbu_comment()
{
    $sql="INSERT INTO test.test_event_info_comment_tbl (`from_user_id`,`to_user_id`,event_id,table_id,overall_score,`comment`,is_anonymous,add_time) SELECT user_id,event_user_id,event_id,table_id,overall_score,`comment`,is_anonymous,add_time FROM pai_db.pai_event_comment_log_tbl;";
    db_simple_getdata($sql);
}

function insert_comment()
{
    $sql="select * from test.test_event_info_comment_tbl where status=0 limit 2";
    db_simple_getdata($sql);
}

function update_comment()
{
    $sql="update test.test_event_info_comment_tbl set type_id=42 where type_id=0";
    db_simple_getdata($sql);

    $sql="update test.test_event_info_comment_tbl set match_score=overall_score,manner_score=overall_score,quality_score=overall_score";
    db_simple_getdata($sql);

    $sql="UPDATE test.test_event_info_comment_tbl c,test.test_event_info_stage_tbl s SET c.goods_id=s.goods_id,c.stage_id=s.stage_id WHERE c.event_id=s.event_id AND c.table_id=s.table_id;";
    db_simple_getdata($sql);

}

function import_huodong()
{
    $sql="select event_id from event_db.event_details_tbl where type_icon='photo' and event_status in ('0','2') and new_version=2 and event_id>60691;";
    $arr=curl_event_data("event_details_class", "db_simple_getdata_api", array($sql));
    foreach($arr as $val)
    {
        $sql="insert ignore test.test_event_info_tbl set event_id=".$val['event_id'];
        db_simple_getdata($sql);
    }
}
import_huodong();

?>



