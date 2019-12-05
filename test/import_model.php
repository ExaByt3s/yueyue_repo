<?php
set_time_limit(0);

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$mall_basic_check_obj = POCO::singleton ( 'pai_mall_certificate_basic_class' );
//$mall_basic_check_obj->set_debug ();

$mall_seller_obj = POCO::singleton ( 'pai_mall_seller_class' );
//$mall_seller_obj->set_debug ();

$goods_obj = POCO::singleton ( 'pai_mall_goods_class' );
//$goods_obj->set_debug ();

$user_obj = POCO::singleton ( 'pai_user_class' );

$sql = "select user_id from pai_db.pai_model_audit_tbl where is_approval in (1,3) and is_import=0 limit 500";

$arr = db_simple_getdata ( $sql, false, 101 );


exit;
foreach ( $arr as $val )
{
	
	$user_id = $val['user_id'];

    if(!$user_id)
    {
        continue;
    }

    $check_seller_exist = $mall_seller_obj->get_seller_info ( $user_id, 2 );

    if($check_seller_exist)
    {
        continue;
    }
	
	$model_info = $user_obj->get_user_info_by_user_id ( $user_id );
	
	$rs = $mall_basic_check_obj->batch_open_service ( $user_id, 31 );
	
	$seller_info = $mall_seller_obj->get_seller_info ( $user_id, 2 );
	$seller_profile_id = $seller_info ['seller_data'] ['profile'] [0] ['seller_profile_id'];
	
	$data ['type_id'] = array (31 );
	$data ['avatar'] = get_user_icon ( $user_id, $size = 165 );
	$data ['cover'] = $model_info ['cover_img'];
	$data ['name'] = get_user_nickname_by_user_id ( $user_id );
	$data ['location_id'] = $model_info ['location_id'];
	$data ['introduce'] = $model_info ['intro'];
	$data ['m_height'] = $model_info ['height'];
	$data ['m_weight'] = $model_info ['weight'];
	$data ['m_bwh'] = $model_info ['chest'] . '-' . $model_info ['waist'] . '-' . $model_info ['hip'];
	$data ['m_cups'] = $model_info ['chest_inch'];
	$data ['m_cup'] = $model_info ['cup_word'];
	$data ['m_level'] = $model_info ['level_require'];
	
	foreach ( $model_info ['model_pic'] as $k => $img_val )
	{
		$img_arr [$k] ['img_url'] = $img_val ['img'];
		$img_arr [$k] ['profile_id'] = $seller_profile_id;
		$_temp_img[] = $img_val ['img'];
	}
	
	
	$data ['img'] = $img_arr;
	
	$mall_seller_obj->update_seller_profile ( $seller_profile_id, $data );
	
	unset($data);
	
	$store_id = $mall_seller_obj->get_first_store_id_by_user_id ( $user_id );
	
	foreach ( $model_info ['model_style_v2'] as $style_val )
	{
		
		$goods_data ['store_id'] = $store_id;
		$goods_data ['type_id'] = 31;
		$goods_data ['default_data'] ['content'] = $model_info['intro'];
		$goods_data ['default_data'] ['audit_time'] = time();
		$goods_data ['default_data'] ['status'] = 1;
		
		if($style_val ['style']=='欧美')
		{
			$style_hash_arr = array("67c6a1e7ce56d3d6fa748ab6d9af3fd7");
			$style_hash_name = array("欧美");
		}
		elseif($style_val ['style']=='情绪')
		{
			$style_hash_arr = array("642e92efb79421734881b53e1e1b18b6");
			$style_hash_name = array("情绪");
		}
		elseif ($style_val ['style']=='清新')
		{
			$style_hash_arr = array("f457c545a9ded88f18ecee47145a72c0");
			$style_hash_name = array("糖水");
		}
		elseif ($style_val ['style']=='街拍')
		{
			$style_hash_arr = array("b53b3a3d6ab90ce0268229151c9bde11");
			$style_hash_name = array("甜美");
		}
		elseif ($style_val ['style']=='复古')
		{
			$style_hash_arr = array("c0c7c76d30bd3dcaefc96f40275bdc0a","9a1158154dfa42caddbd0694a4e9bdc8");
			$style_hash_name = array("古装","文艺复古");
		}
		elseif ($style_val ['style']=='韩系')
		{
			$style_hash_arr = array("2838023a778dfaecdc212708f721b788");
			$style_hash_name = array("韩系");
		}
		elseif ($style_val ['style']=='日系')
		{
			$style_hash_arr = array("2838023a778dfaecdc212708f721b788");
			$style_hash_name = array("日系");
		}
		elseif ($style_val ['style']=='性感')
		{
			$style_hash_arr = array("a684eceee76fc522773286a895bc8436");
			$style_hash_name = array("内衣/比基尼");
		}
		elseif ($style_val ['style']=='胶片')
		{
			continue;
		}
		elseif ($style_val ['style']=='商业')
		{
			$style_hash_arr = array("72b32a1f754ba1c09b3695e0cb6cde7f","9f61408e3afb633e50cdf1b20de6f466","d1f491a404d6854880943e5c3cd9ca25","9b8619251a19057cff70779273e95aa6");
			$style_hash_name = array("淘宝","礼仪","车展","走秀");
		}
		
		$goods_data ['system_data'] ['66f041e16a60928b05a7e228a89c3799'] = $model_info ['limit_num'];
		
		
		
		
		if ($style_val ['hour'] == 2)
		{
			$goods_data ['prices_de'] ['76'] = $style_val ['old_price'] ? $style_val ['old_price'] : $style_val ['price'];
		}
		else
		{
			$goods_data ['prices_de'] ['77'] = $style_val ['old_price'] ? $style_val ['old_price'] : $style_val ['price'];
		}
		

		if($model_info ['model_pic'])
		{
			foreach ( $model_info ['model_pic'] as $bk => $img_val )
			{
				if($bk==3) break;
				$goods_data ['img'][$bk]['img_url'] = $img_val ['img'];
			}
		}
		
		foreach($style_hash_arr as $style_k=>$hash_val)
		{
			$goods_data ['default_data'] ['titles'] = $style_hash_name [$style_k];
			$goods_data ['system_data'] ['d9d4f495e875a2e075a1a4a6e1b9770f'] = $hash_val;
			$goods_id = $goods_obj->add_goods ( $goods_data );
			$goods_obj->change_goods_status($goods_id['result'],1);
		}
		
	}
	
	unset($goods_data);
	
	$time = time();
	$sql = "update pai_db.pai_model_audit_tbl set is_import=1 , import_time=$time where user_id=$user_id";
	db_simple_getdata ( $sql, false, 101 );
}

?>