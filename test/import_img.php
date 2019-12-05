<?php 
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$sql = "select * from test.pic_num_tbl where status=0";
$arr = db_simple_getdata($sql,false,101);

foreach($arr as $val)
{
	if($val['num']>=15)
	{
		$max = 15;
	}
	else
	{
		$max = $val['num'];
	}
	
	$cellphone = $val['cellphone'];
	
	for($i=1;$i<=$max;$i++)
	{
		$data['profile_id'] = $val['profile_id'];
		$data['img'] = "http://img16.poco.cn/yueyue/tt_pic/{$cellphone}/{$i}_320.jpg";
		$data['add_time'] = 1234567;
		$insert_str = db_arr_to_update_str($data);
		
		$sql = "insert into pai_task_db.task_profile_img_tbl set ".$insert_str;
		db_simple_getdata($sql,false,101);
	}

	
	$sql = "update test.pic_num_tbl set status=1 where id=".$val['id'];
	db_simple_getdata($sql,false,101);
}

?>