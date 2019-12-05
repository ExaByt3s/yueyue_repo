<?php 
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

define ( 'YUE_OA_IMPORT_ORDER', 1 );

$user_obj = POCO::singleton ( 'pai_user_class' );

$pwd = rand(100000,999999);

$cameraman_reg_data ['nickname'] = "…Ãº“";
$cameraman_reg_data ['cellphone'] = rand(1000000,9999999);
$cameraman_reg_data ['pwd'] = $pwd;
$cameraman_reg_data ['role'] = "task_seller";
$cameraman_reg_data ['reg_from'] = "pc";

$cameraman_user_id = $user_obj->create_account ( $cameraman_reg_data, $err_msg );
echo "’ ∫≈£∫".$cameraman_user_id."<br />";
echo "√‹¬Î£∫".$pwd;

if($cameraman_user_id)
{
	$sql = "update pai_db.pai_user_tbl set cellphone={$cameraman_user_id} where user_id={$cameraman_user_id}";
	db_simple_getdata($sql,false,101);
	
	$task_coin_obj = POCO::singleton('pai_task_coin_class');
	$ret = $task_coin_obj->submit_give('SELLER_REG', $cameraman_user_id, 0);
}

?>