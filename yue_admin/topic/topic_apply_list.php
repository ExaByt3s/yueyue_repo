<?php

/**
 *
 * 活动报名
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-02-28 15:04:18
 * @version 1
 */

include_once 'common.inc.php';
include_once 'top.php';
//引入常用函数
include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php';
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
$tpl = new SmartTemplate("topic_apply_list.tpl.htm");
$act            = $_INPUT['act'] ? $_INPUT['act'] :'add';
$topic_id       = $_INPUT['topic_id'] ? (int)$_INPUT['topic_id'] : 0 ;
$price          = $_INPUT['price'] ? $_INPUT['price'] : 'all';
$style          = $_INPUT['style'] ? $_INPUT['style'] : '';
$state          = $_INPUT['state'] ? $_INPUT['state'] : 'all';
$add_time       = $_INPUT['add_time'] ? $_INPUT['add_time'] : 'all';
 //约拍次数
$yue_count      = $_INPUT['yue_count'] ? $_INPUT['yue_count'] : 'all';
//约拍金额
$yue_price      = $_INPUT['yue_price'] ? $_INPUT['yue_price'] : 'all';
//身高
$height         = $_INPUT['height'] ? $_INPUT['height'] : 'all';
//自定义uid
$uid_str        = $_INPUT['uid_str'] ? trim($_INPUT['uid_str']) : '';
$topic_obj         = POCO::singleton('pai_topic_class');
$topic_enroll_obj  = POCO::singleton('pai_topic_enroll_class');
//风格获取
$model_style_v2_obj  = POCO::singleton('pai_model_style_v2_class');
$order_org_obj  = POCO::singleton('pai_order_org_class');
//模特卡类
$model_card_obj = POCO::singleton('pai_model_card_class');
//用户表
$user_obj      = POCO::singleton('pai_user_class');
//风格获取
//$style_v2_obj  = POCO::singleton('pai_model_style_v2_class');
//次数表
//$date_rank_obj = POCO::singleton ( 'pai_date_rank_class' );
//所有专题列表
//活动状态
$alter_obj = POCO::singleton('pai_alter_price_class');

//修改状态部分
if ($act == 'check')
{
	$ids = $_INPUT['ids'] ? $_INPUT['ids'] : 0;
	$sort_status = $_INPUT['sort_status'] ? (int)$_INPUT['sort_status'] : 0;
	if (!$ids) 
	{
		echo "<script type='text/javascript'>window.alert('非法操作');history.back();</script>";
		exit;
	}
	//trim($ids, ',');
	$ids = implode(',', $ids);
	$update_str = "id IN({$ids})";
	$data['sort_status'] = $sort_status;
	$info = $topic_enroll_obj->update_info_where_str($data,$update_str);
	echo "<script type='text/javascript'>window.alert('更新数据成功');location.href='{$_SERVER['HTTP_REFERER']}'</script>";
	exit();
}
//列表部分
$total_count = $topic_obj->get_topic_list(true);
$topic_list = $topic_obj->get_topic_list(false, '', 'is_effect DESC, add_time DESC,sort DESC', "0,{$total_count}","id,title");

//报名列表
$where_str = "topic_id={$topic_id} GROUP BY user_id";
$total_count = $topic_enroll_obj->get_topic_enroll_list(true,$where_str);
$user_arr_id = $topic_enroll_obj->get_topic_enroll_list(false, $where_str, $order_by = 'sort_status ASC,id DESC', "0,{$toal_count}", "user_id");
if (empty($user_arr_id) || !is_array($user_arr_id)) {$user_arr_id = array();}
//条件开始筛选
//价格和风格筛选
$where_style_str = "1";
switch ($price)
{
	case 1:
		$where_style_str .= " AND price BETWEEN 0 AND 99";
		break;
	case 100:
		$where_style_str .= " AND price BETWEEN 100 AND 199";
		break;
	case 200:
		$where_style_str .= " AND price BETWEEN 200 AND 399";
		break;
	case 200:
		$where_style_str .= " AND price BETWEEN 200 AND 399";
		break;
	case 400:
		$where_style_str .= " AND price >= 400";
		break;
}
//echo $where_style_str;
if ($style && !in_array('全部', $style))
{
    $style_str = "";
	foreach ($style as $key => $val) 
	{
		if ($key != 0) {$style_str .= " OR ";}
		$style_str .= "style like '%$val%'";
	}
	$where_style_str .= " AND ({$style_str})";
}
if ($where_style_str != "1")
{
   if (!empty($user_arr_id) && is_array($user_arr_id)) 
   {
     //转换为一维数组||且转换为字符串10000,100003
     $user_str = implode(',', array_change_by_val($user_arr_id, 'user_id'));
     $where_style_str .= " AND user_id IN ($user_str)";
     //echo $where_style_str."<br/>";
     $style_count = $model_style_v2_obj->get_model_style_list(true, $where_style_str);
     $user_arr_id = $model_style_v2_obj->get_model_style_list(false, $where_style_str, 'id DESC', "0,{$style_count}", $fields = 'DISTINCT(user_id)');
   }

}

//活动获取数据
if ($state != 'all') 
{
	if ($state == 2 && $add_time != 'all') {$user_arr_id = array();}
	if(!empty($user_arr_id) && is_array($user_arr_id))
	{
		foreach ($user_arr_id as $state_key =>$state_vo) 
		{
			$alter_ret= $alter_obj->check_topic_user_online($state_vo['user_id']);
			if($state == 1 && !is_array($alter_ret)){unset($user_arr_id[$state_key]);}
			if($state == 2 && is_array($alter_ret)){unset($user_arr_id[$state_key]);}
			
		}
	}
}
//有活动判断
if ($add_time != 'all' && $state != 2) 
{
	// "ajj";
	if(!empty($user_arr_id) && is_array($user_arr_id))
	{
		foreach ($user_arr_id as $state_key =>$state_vo) 
		{
			$alter_ret= $alter_obj->check_topic_user_online($state_vo['user_id']);
			if(!is_array($alter_ret)){unset($user_arr_id[$state_key]);}
			if (is_array($alter_ret))
			{
				$interval_time = $alter_ret['end_time'] - time();
				//echo $interval_time."<br/>";
				/*echo $add_time."add_time<br/>";
				echo ceil($interval_time/3600*24)."天数<br/>";*/
				if($add_time == -1 && $interval_time >0)
				{
					unset($user_arr_id[$state_key]);
				}
				//1天
				elseif ($add_time == 1 && ceil($interval_time/3600*24) != 1) 
				{
					unset($user_arr_id[$state_key]);
				}
				//1-6
				elseif ($add_time == 6 && ceil($interval_time/3600*24) <= 1 || ceil($interval_time/3600*24) >= 7)
				{
					//echo "<br/>7";
					unset($user_arr_id[$state_key]);
				}
				elseif ($add_time == 7 && ceil($interval_time/3600*24) < 7) 
				{
					unset($user_arr_id[$state_key]);
				}
				unset($interval_time);
			}
		}
	}
}


//print_r($user_arr_id);
$where_yue_str = "1";
//约拍次数
switch ($yue_count)
{
	case -1:
		$where_yue_str .= " AND yue_count > 0 ";
		break;
	case 1:
		$where_yue_str .= " AND yue_count BETWEEN 1 AND 3";
		break;
	case 4:
		$where_yue_str .= " AND yue_count BETWEEN 4 AND 10";
		break;
	case 10:
		$where_yue_str .= " AND yue_count >= 10";
		break;
}

//echo $yue_count."<br/>".$yue_price;
//约拍金额
switch ($yue_price) {
	case 1:
		$where_yue_str .= " AND yue_price BETWEEN 0 AND 999";
		break;
	case 1000:
       $where_yue_str .= " AND yue_price BETWEEN 1000 AND 4999";
       break;
    case 5000:
       $where_yue_str .= " AND yue_price BETWEEN 5000 AND 9999";
       break;
    case 10000:
       $where_yue_str .= " AND yue_price >= 10000";
       break;
}
//约拍数据查询
if ($where_yue_str != "1") 
{
	//echo $where_yue_str;
	if (!empty($user_arr_id) && is_array($user_arr_id)) 
	{
		$user_str = implode(',', array_change_by_val($user_arr_id, 'user_id'));
        $where_yue_str .= " AND user_id IN ($user_str)";
        //echo $where_yue_str."<br/>";
        $tmp_arr_id = $order_org_obj->get_user_id_by_yue_where($where_yue_str, "GROUP BY user_id DESC", "0,10000", "to_date_id AS user_id,count(date_id) AS yue_count,SUM(date_price) AS yue_price");
	}
	//print_r($user_yue_arr);
}
if ($yue_count != -1 && $yue_count != 'all') 
{
	$user_arr_id = $tmp_arr_id;
}
//得出无约拍
elseif ($yue_count == -1 && $yue_count != 'all' && is_array($tmp_arr_id)) 
{
	$tmp_arr_id_v2 = array_change_by_val($tmp_arr_id, 'user_id');
	foreach ($user_arr_id as $tmp_key => $tmp_val) 
	{
		if(in_array($tmp_val['user_id'], $tmp_arr_id_v2))
		{
			unset($user_arr_id[$tmp_key]);
		}
	}
}
//身高
$where_height_str = "1";
switch ($height) {
	case 1:
        $where_height_str .= " AND height BETWEEN 0 AND 159";
		break;
    case 160:
        $where_height_str .= " AND height BETWEEN 160 AND 164";
		break;
	case 165:
        $where_height_str .= " AND height BETWEEN 165 AND 169";
		break;
	case 170:
        $where_height_str .= " AND height BETWEEN 170 AND 174";
		break;
	case 175:
        $where_height_str .= " AND height BETWEEN 175 AND 179";
		break;
	case 180:
        $where_height_str .= " AND height >= 180";
		break;
}
//身高数据查询
if ($where_height_str != "1")
{
	if (!empty($user_arr_id) && is_array($user_arr_id))
	{
        $user_str = implode(',', array_change_by_val($user_arr_id, 'user_id'));
        $where_height_str .= " AND user_id IN ($user_str)";
        $height_count    = $model_card_obj->get_model_card_list(true, $where_height_str);
        $user_arr_id = $model_card_obj->get_model_card_list(false, $where_height_str, $order_by = 'user_id DESC', "0,{$height_count}", 'DISTINCT(user_id)');
	}
}
//自定义数据
if ($uid_str)
{
	$user_arr = explode(',', $uid_str);
    //print_r($user_arr);
    if (is_array($user_arr_id))
    {
       foreach ($user_arr_id as $key => $arr_id) 
       {
            if(!in_array($arr_id['user_id'], $user_arr))
            {
               unset($user_arr_id[$key]);
            }
       }
    }
}
//var_dump($user_arr_id);
//开始循环数据
if (!empty($user_arr_id) || is_array($user_arr_id)) 
{
	$i = 0;
	foreach ($user_arr_id as $key => $vo) 
	{
		$user_info = $user_obj->get_user_info($vo['user_id']);
		if ($yue_login_id == 100293) 
		{
			/*print_r($user_info);
			exit;*/
			# code...
		}
		$list[$i]['user_id']       = $vo['user_id'];
		$list[$i]['location_name'] = get_poco_location_name_by_location_id($user_info['location_id']);
		$list[$i]['nickname']      = $user_info['nickname'];
		$enll_data =  $topic_enroll_obj->get_topic_enroll_add_time($topic_id,$vo['user_id']);
		$list[$i]['add_time']     = date('Y-m-d H:i:s', $enll_data['add_time']);
		$list[$i]['id']           = $enll_data['id'];
		$list[$i]['sort_status']  = $enll_data['sort_status'];
		$ret = $model_card_obj->get_model_card_info($vo['user_id']);
		$list[$i]['height'] = $ret['height'];
		$list[$i]['chest_inch'] = $ret['chest_inch'];
		$list[$i]['cup'] = $ret['cup'];
		$list[$i]['cover_img'] = $ret['cover_img'];
		$list[$i]['thumb'] = yueyue_resize_act_img_url($ret['cover_img'], 145);
		$list[$i]['cellphone'] = $user_info['cellphone'];
		$alter_price = $alter_obj->check_topic_user_online($vo['user_id']);
		if (is_array($alter_price)) 
		{
			$list[$i]['state'] = '活动中';
		}
		else
		{
			$list[$i]['state'] = '正常';
		}
		//$yue_count = $order_org_obj->get_order_count_by_user_id(true, $vo['user_id']);
		//echo $yue_count;
		$list[$i]['yue_count'] = $order_org_obj->get_order_count_by_user_id(true, $vo['user_id']);
		$list[$i]['yue_price'] = $order_org_obj->get_sum_price_by_user_id(true, $vo['user_id']);
		$style_data = $model_style_v2_obj->get_model_style_combo($vo['user_id']);
		//print_r($style_data);
		$list[$i]['main'] = $style_data['main'];
		$list[$i]['other'] = $style_data['other'];
		$i++;
	}
}

$style_hide = "";
$style_show = "";
if ($style) 
{
	foreach ($style as $key => $val_style) 
	{
		$style_hide .= "<input type='hidden' name='style[]' value='{$val_style}'/>";
		if ($key != 0) 
		{
			$style_show .= ",";
		}
		$style_show .= $val_style;
	}
	# code...
}
$tpl->assign('topic_list', $topic_list);
$tpl->assign('list', $list);
$tpl->assign('topic_id', $topic_id);
$tpl->assign('price', $price);
$tpl->assign('state', $state);
$tpl->assign('add_time', $add_time);
$tpl->assign('style', $style);
$tpl->assign('style_hide', $style_hide);
$tpl->assign('style_show', $style_show);
$tpl->assign('yue_count', $yue_count);
$tpl->assign('height', $height);
$tpl->assign('yue_price', $yue_price);
$tpl->assign('uid_str', $uid_str);
$tpl->assign('topic_id', $topic_id);
$tpl->assign('act', $act);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->output();

?>