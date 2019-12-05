<?php
die();
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$user_id  			  = trim($_INPUT['user_id']);
$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
$ret = $pai_bind_account_obj->del_bind_by_rel($user_id,'alipay_account');
if( $ret>0 ){

	echo '删除绑定成功';

}
else{

	echo '该用户没绑定';

}
?>