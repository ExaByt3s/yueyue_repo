<?php
include_once('../common.inc.php');

$upload_type = $_INPUT['upload_type'];

$obj_list = $_INPUT['obj_list'];

$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');

$upload_obj = POCO::singleton('pai_pic_class');

$token_str = $weixin_helper_obj->wx_get_access_token(1);

for($i=0;$i<count($obj_list);$i++) 
{
	$temp = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$token_str.'&media_id='.$obj_list[$i]['media_id'];

	$obj_list[$i]['wx_url'] = $temp;

	if($upload_type == 'pics'){
		$ret = $upload_obj->upload_works_pic($temp,$yue_login_id);
	}
	else if($upload_type == 'icon'){
		$ret = $upload_obj->upload_user_icon($temp,$yue_login_id);
	}
	$ret_arr = json_decode($ret,true);
	$obj_list[$i]['server_url'] = $ret_arr['url'][0];
}

$output_arr['obj_list'] = $obj_list;
mall_mobile_output($output_arr,false);

?>