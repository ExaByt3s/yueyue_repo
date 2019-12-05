<?php
$s=microtime(true);
//print_r($_SERVER);
//exit;
set_time_limit(0);
//ignore_user_abort(true);
ini_set('memory_limit', '256M');
//include_once 'common.inc.php';
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/authority.php');

$class = array('pai_mall_goods_type_class','pai_mall_goods_class','pai_mall_seller_class','pai_mall_goods_type_attribute_class','pai_mall_statistical_class','pai_mall_order_class');
$seller_obj = POCO::singleton($_GET['a'] == 'setcon'?$class[2]:$class[$_GET['c']?$_GET['c']:1]);


if($_GET['a'] == 'setcon')
{
	$name=$_GET['n'];
	$value=$_GET['v'];
	echo $name ."=>".$value;
	$seller_obj->set_system_config(array($name=>$value));
	exit;
}

/*		include_once("/disk/data/htdocs233/mypoco/fulltext_search_helper/lucoservice/real_search_client.class.php");
		$querys["prices_prices"] = $_GET['p_s'].",".$_GET['p_e'];
		$querys["limit"] = "0,20";
		$lucoclient_server_conf = $GLOBALS['LUCOCLIENT_SERVER_CONFIG']['test'];
		$client = new LucoClient($lucoclient_server_conf['host'], $lucoclient_server_conf['port']);
		$res = $client ->searchFun("actions.MallFunction.searchMallModelTest",$querys);
		print_r($res);	
		exit;
*/
$data=array(
            'pv'=>10,
			'bill_finish_num'=>2,
			);
			
		$type_id = array(3,5,12,31,40,41,43);	
		//$querys["keywords"] = "活动";//关键字 titles,content,introduction,keyword
		$querys["user_id"] = 100031;//关键字 titles,content,introduction,keyword
		//$querys["type_id"] = $type_id[rand(0,6)];//类型
		$querys["type_id"] = 42;//类型
		//$querys["edit_status"] = 1;//类型		
		$querys["orderby"] = rand(1,2);//
		//$querys["location_id"] = 101003001;
		//$querys["start_time"] = "2015-08-10";//上架
		//$querys["end_time"] = "2015-08-26 23:59:59";//上架
		//$querys["audit_time_s"] = "2015-08-10";//上架
		//$querys["audit_time_e"] = "2015-08-10";//上架
		//$querys["is_black"] = "0";//上架
		//$querys["is_show"] = "1";//上架
		//$querys["keywords"] = "2115309";//上架
		//$querys["seller_onsale_num"] = 1;//上架
		//$querys["name_46"] = "47";//上架
		//$querys["is_black"] = "0";//上架
		//$querys["user_id"] = "110482";//用户
		//$querys["brand_id"] = "0";//品牌
		//$querys["keywords"] = "114072";//审核
//		$querys["goods_status"] = "1";//审核
//
//		$querys["seller_user_id"] = "2104677";//审核
//		$querys["goods_id"] = "2104677";//审核
//		$querys["is_show"] = "1";//上架
//		$querys["is_black"] = "0";//黑名单
//		$querys["seller_id"] = "203898";//商家
//		$querys["store_id"] = "203898";//店铺
//		$querys["user_id"] = "100519";//用户
		//$querys["location_id"] = "101029001";//地区
//		$querys["prices_prices"] = "700,1000";//价格
//		$querys["add_time"] = "2015/7/14";//添加时间
//		$querys["begin_time"] = "2015/7/13";//上架时间
//		$querys["end_time"] = "2015/7/15";//上架时间
//		//////////////模特
		//$querys["name_46"] = "47";
//		//$querys["name_58"] = "1,3";
//		$querys["m_bwh"] = "85-67-90";
//		$querys["m_cup"] = "B";
		//$querys["m_cups"] = "30";
		///$querys["m_height"] = "160,170";
//		$querys["m_level"] = "1,3";
//		$querys["m_weight"] = "45,52";
//		//////////////
//		$querys["profile_sex"] = "2";
//		//$querys["order"] = "";
		//$querys["order"] = 2;//排序方法 默认请填写
		//$querys["order"] = "seller_id DESC 4";//排序方法 默认请填写
		$querys["limit"] = "0,60";
		$querys["debug"] = true;

//$seller_obj->synchronous_shownum();
//$seller_obj->synchronous_goodsnum();
//$type_list=$seller_obj->search_goods_list_by_fulltext($querys,$querys["limit"]);
//$type_list1=$seller_obj->check_can_buy(2124194,array('type_id'=>'144539960137777','num'=>10));
//$type_list1=$seller_obj->synchronous_goods();
//$type_list1=$seller_obj->synchronous_seller();
//$type_list=$seller_obj->change_goods_att_5();
//$type_list=$seller_obj->synchronous_goods_42();
//$type_list=$seller_obj->get_goods_info(2131839);
$type_list2=$seller_obj->get_goods_info(2125880);
$type_list=$seller_obj->get_goods_prices(2125880, array('num'=>1, 'type_id'=>310));
//$type_list=$seller_obj->check_goods_prices_type_id($_GET['id']);
//$type_list=$seller_obj->update_allgoods_prices();
//$type_list = $seller_obj->goods_data_for_front_packing($type_list);
//print_r($querys);
//$type_list=$seller_obj->search_goods_list_by_fulltext($querys,$querys["limit"]);
//

//$type_list=$seller_obj->synchronous_goods($_GET['type_id']?$_GET['type_id']:3);
//$type_list=$seller_obj->property_for_search_get_data($_GET['type_id']?$_GET['type_id']:3);
//$seller_obj->synchronous_goods(41);//同步商品

//$type_list = $seller_obj->batch_insert_or_update_goods_type_id_tbl(109650,2);
//$type_list = $seller_obj->show_seller_data_for_temp('10000420150912043449');

//print_r($type_list);

print_r($type_list);
print_r($type_list2);


/*
$type_list=$seller_obj->search_seller_list_by_fulltext($querys,$querys["limit"]);
$type_list2 = $seller_obj->search_seller_list_by_sql($querys["type_id"]);
$t1=array();
foreach($type_list['data'] as $val)
{
	$t1[$val['seller_id']] = $val;
}
$t2=array();
foreach($type_list2 as $val)
{
	$t2[$val['seller_id']] = $val;
}
echo count($t1)."<br>";
echo count($t2)."<br>";
$in=1;
foreach($t2 as $val)
{
	echo $in."==".$val['seller_id']."-->".$val['type_id'];
	if($t1[$val['seller_id']])
	{
		echo "=======ok";
	}
	else
	{
		echo "=======bad[".$t1[$val['seller_id']]['profile_id']."]";
	}
	echo "<br>";
	$in++;
}
*/
$e=microtime(true);
pai_log_class::add_log(($e-$s), 'time', 'koko_text');

echo "<br>time:".($e-$s);
?>
