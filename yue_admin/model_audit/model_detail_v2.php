<?php 


  /**
  *模特内容页版本为2
  * @authors xiao xiao  (xiaojm@yueus.com)
  * @date    2015-04-15 13:53:37
  * @version 1
  */
	include_once 'common.inc.php';
    include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
    include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/locate_file.php';
    include_once 'include/common_function.php';
    $user_obj     = POCO::singleton('pai_user_class');
    //账号绑定
    $payment_obj      = POCO::singleton ( 'pai_payment_class' );
    $model_add_obj    = POCO::singleton('pai_model_add_class');
	$model_card_obj   = POCO::singleton('pai_model_card_class');
    //获取支付宝账号
    $user_account_obj = POCO::singleton('pai_bind_account_class');
    //机构
    $organization_obj  = POCO::singleton('pai_organization_class');
    $model_relate_org  = POCO::singleton('pai_model_relate_org_class');
    $model_order_org_obj  = POCO::singleton('pai_order_org_class');
    //关注数
    $user_follow_obj      = POCO::singleton('pai_user_follow_class');
    //评价
    $model_comment_log_obj = POCO::singleton('pai_model_comment_log_class');
    
    $tpl = new SmartTemplate("model_detail_v2.tpl.htm");
    $uid = trim($_INPUT['uid']);
    $uid = (int)$uid;
    if (empty($uid)) 
    {
    	js_pop_msg('非法操作');
   		exit;
    }
    //月统计
    $month_data['day_count_order']   = $model_order_org_obj->get_order_count_by_user_id(true, $uid, 'day');
    $month_data['week_count_order']  = $model_order_org_obj->get_order_count_by_user_id(true, $uid, 'week');
    $month_data['month_count_order'] = $model_order_org_obj->get_order_count_by_user_id(true, $uid, 'month');
    $month_data['day_sum_price']     = $model_order_org_obj->get_sum_price_by_user_id($uid, 'day');
    $month_data['week_sum_price']    = $model_order_org_obj->get_sum_price_by_user_id($uid, 'week');
    $month_data['month_sum_price']   = $model_order_org_obj->get_sum_price_by_user_id($uid, 'month');
    //关注数据
    $month_data['day_fans']          = $user_follow_obj->get_fans_by_user_id(true, $uid, 'day');
    $month_data['week_fans']         = $user_follow_obj->get_fans_by_user_id(true, $uid, 'week');
    $month_data['month_fans']        = $user_follow_obj->get_fans_by_user_id(true, $uid, 'month');
    //约拍信息
    $yue_count = $model_order_org_obj->get_order_count_by_user_id(true, $uid);
    $yue_data  = $model_order_org_obj->get_order_count_by_user_id(false, $uid, '' , $limit = '0,3','date_id DESC', $fields = '*');
    if ($yue_data) {
        foreach ($yue_data as $yue_key => $yue_vo) 
        {
           $yue_data[$yue_key]['date_time'] = date('Y-m-d', $yue_vo['date_time']);
           $yue_data[$yue_key]['cameraman_nick_name'] = get_user_nickname_by_user_id($yue_vo['from_date_id']);
           $model_coment_info = $model_comment_log_obj->get_model_comment_by_date_id($yue_vo['date_id']);
           $yue_data[$yue_key]['comment_text'] = $model_coment_info['comment'];
           $yue_data[$yue_key]['comment_desc'] = poco_cutstr($model_coment_info['comment'], 20, '....');
        }
    }
    
    //获取昵称
    $nickname     = $user_obj->get_user_nickname_by_user_id($uid);
    $model_data   = $model_add_obj->get_model_info($uid);    
    //模特信息处理
    if (!empty($model_data) && is_array($model_data)) 
    {
        $model_data['img_url']      = $model_data['img_url'] ? $model_data['img_url'] : 'resources/images/admin_upload_thumb.png';
        $model_data['inputer_name'] = get_user_nickname_by_user_id($model_data['inputer_id']);
    }
    
    $other_data   = $model_add_obj->get_model_other($uid);

    $follow_data  = $model_add_obj->get_model_follow(false,$uid);
    $follow_count = $model_add_obj->get_model_follow(true,$uid);
    $prof_data    = $model_add_obj->get_model_profession($uid);
    if(!empty($prof_data) && is_array($prof_data))
    {
    	$prof_data['join_time']   = $prof_data['join_time']   != '0000-00-00' ? date('Y-m',strtotime($prof_data['join_time'])) : '';
    	$prof_data['school_time'] = $prof_data['school_time'] != '0000-00-00' ? date('Y',strtotime($prof_data['school_time'])) : '';
    }
    $score_data   = $model_add_obj->get_model_score($uid);
    if (!empty($score_data)) 
    {
       $total_score = $score_data['join_score']+$score_data['appearance_score']+$score_data['honor_score']+$score_data['makeup_score']+$score_data['expressiveness_score']+$score_data['height_score']+$score_data['weight_score']+$score_data['cup_score']+$score_data['bwh_score'];
    }
    else
    {
        $total_score = 0;
    }
    $tpl->assign('total_score', $total_score);
    //身材信息
    $stature_data = $model_add_obj->get_model_stature($uid);
    if(!empty($stature_data) && is_array($stature_data)) $stature_data['birthday'] = $stature_data['birthday'] != '0000-00-00' ? date('Y-m',strtotime($stature_data['birthday'])) : '';
    //获取电话号码
    $cellphone = $user_obj->get_phone_by_user_id($uid);
    //获取模特卡数据
    $model_card = $model_card_obj->get_model_card_info($uid);
    if (!empty($model_card) && is_array($model_card)) 
    {
        $model_card_data['m_chest']      = (int)$model_card['chest'];
        $model_card_data['m_chest_inch'] = (int)$model_card['chest_inch'];
        $model_card_data['m_hip']        = (int)$model_card['hip'];
        $model_card_data['m_waist']      = (int)$model_card['waist'];
        $model_card_data['m_cup']        = $model_card['cup'];
        $model_card_data['m_height']     = $model_card['height'];
        $model_card_data['m_weight']     = $model_card['weight'];
    }
    //账户绑定内容提取
    $account_info = $payment_obj->get_user_account_info ( $uid );
    //获取模特风格
    $model_list1 = $model_add_obj->list_style($uid);
    $str = '';
    if (!empty($model_list1) && is_array($model_list1)) 
    {
        foreach ($model_list1 as $key => $vo) 
        {
	        $style_name = '';
	        switch ($vo['style']) 
	        {
	            case 0:
	                $style_name = '欧美';
	                break;
	            case 1:
	                $style_name = '情绪';
	                break;
	            case 2:
	                $style_name = '清新';
	                break;
	            case 3:
	                $style_name = '复古';
	                break;
	            case 4:
	                $style_name = '韩系';
	                break;
	            case 5:
	                $style_name = '日系';
	                break;
	            case 6:
	                $style_name = '性感';
	                break;
	            case 7:
	                $style_name = '街拍';
	                break;
	            case 8:
	                $style_name = '胶片';
	                break;
	            case 9:
	                $style_name = '商业';
	                break;
	            
	            default:
	                $style_name = '';
	                break;
	        }
            if ($key != 0) 
            {
                $str .= ',';
            }
            $str .= $style_name;
        }
    }
	
    //省接口
    $location_id = isset($model_data['location_id']) ? $model_data['location_id'] : 0;
    $province_list = change_assoc_arr($arr_locate_a);
    $prov_id       = substr($location_id, 0 , 6);
    
    //省市选择
    if($prov_id >0)
    {
    	//省选择
    	foreach ($province_list as $key => $vo)
    	{
    		if ($vo['c_id'] == $prov_id)
    		{
    			$province_list[$key]['selected_prov'] = "selected='true'";
    		}
    	}
    	//市选择
    	$city_list = ${'arr_locate_b'.$prov_id};
    	if (!empty($city_list) && is_array($city_list) )
    	{
    		$city_list = change_assoc_arr($city_list);
    		foreach ($city_list as $c_key => $vo)
    		{
    			if ($vo['c_id'] == $location_id)
    			{
    				$city_list[$c_key]['selected_city'] = "selected='true'";
    			}
    		}
    	}
    	
    }
    
    //获取app基本信息
    $app_user = $user_obj->get_user_info($uid);
    $app_user['city'] = get_poco_location_name_by_location_id($app_user['location_id']);
    //获取支付宝账号
    $user_alipay = $user_account_obj->get_alipay_account_by_user_id($uid);
    $tpl->assign('user_alipay', $user_alipay);
    //城市
    //显示机构
    $ret = $model_relate_org->get_org_info_by_user_id($uid);
    if (!empty($ret) && is_array($ret)) 
    {
        $org_name = $organization_obj->get_org_name_by_user_id($ret['org_id']);
    }
    //样片链接
    $p_url = $model_add_obj->find_purl_info($uid);
    $enter = $model_add_obj->find_enter_info($uid);
    $join  = $model_add_obj->find_join_info($uid);
    $label = $model_add_obj->find_label_info($uid);
    $tpl->assign($p_url);
    $tpl->assign($enter);
    $tpl->assign($join);
    $tpl->assign($label);
    $tpl->assign('str', $str);
    $tpl->assign($account_info);
    $tpl->assign('province_list', $province_list);
    $tpl->assign('city_list', $city_list);
    $tpl->assign('nickname', $nickname);
    $tpl->assign('cellphone', $cellphone);
    $tpl->assign($model_card_data);
    $tpl->assign($model_data);
    $tpl->assign($other_data);
    $tpl->assign('list', $follow_data);
    $tpl->assign('follow_count', $follow_count);
    $tpl->assign($prof_data);
    $tpl->assign($score_data);
    $tpl->assign($app_user);
    $tpl->assign('org_name', $org_name);
    //月统计输出
    $tpl->assign($month_data);
    //约拍信息
    $tpl->assign('yue_data',$yue_data);
    $tpl->assign('yue_count',$yue_count);
    $tpl->assign($stature_data);
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();



 ?>