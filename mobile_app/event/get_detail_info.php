<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$model_card_obj 	= POCO::singleton ( 'pai_model_card_class' );
$date_obj 			= POCO::singleton ( 'event_date_class' );
$user_level_obj 	= POCO::singleton ( 'pai_user_level_class' );
$user_obj 			= POCO::singleton ( 'pai_user_class' );
$cameraman_card_obj = POCO::singleton ( 'pai_cameraman_card_class' );
$score_rank_obj 	= POCO::singleton ( 'pai_score_rank_class' );

$user_id = (int)$client_data ['data'] ['param'] ['v_id'];
$login_id = (int)$client_data ['data'] ['param'] ['user_id'];

$role = $user_obj->check_role ( $user_id );

$login_role = $user_obj->check_role ( $login_id );

$version_config = include('/disk/data/htdocs232/poco/pai/config/appstore_version_config.php');
$appstore_version = $version_config['version'];

if ($role == 'model')
{
	$data ['mid'] 		= '122RO01001'; //����ID
	$model_info 		= $model_card_obj->get_model_card_by_user_id ( $user_id, 15 );
	$data ['nickname'] = $model_info ['nickname']; //�û���
	
	/**  �û�ͼƬ ��ʼ */
	foreach ( $model_info ['model_pic'] as $k => $val )
	{
		$img_arr [$k] ['small_url'] 	= $val ['img'];
		$img_arr [$k] ['url'] 			= yueyue_resize_act_img_url ( $val ['img'] );
		$img_arr [$k] ['text'] 			= '';
	}
	if(!$img_arr) $img_arr=array();
	$data ['img_arr'] = $img_arr;
	/**  �û�ͼƬ ���� */
	
	/** �û����� ��ʼ */
	$figure ['height'] 	= $model_info ['height'] . 'CM';
	$figure ['weight'] 	= $model_info ['weight'] . 'KG';
	$figure ['cup_size'] 	= $model_info ['cup_v2'];
	$figure ['bwh_size'] 	= $model_info ['chest'] . '-' . $model_info ['waist'] . '-' . $model_info ['hip'];
	$data ['figure'] = $figure;
	/** �û����� ���� */
	
	/** �û����� ��ʼ */
	$_score 			= round ( $model_info ['score'] );
	$score ['name1'] 	= '�ۺ�����';
	$score ['value1'] 	= "{$_score}";
	$score ['unit1']    = '��';
	$score ['name2'] 	= '��Ƭ����';
	$score ['value2'] 	= "{$model_info ['take_photo_times']}";
	//$score['name3'] = '������';
	//$score['value3'] = "{$model_info ['attendance']}%";
	$score['url']         = "yueyue://goto?type=inner_app&pid=1220075&user_id=" . $user_id;
	$data ['score'] = $score;
	/** �û����� ���� */
	
	$main_style 	= $model_info ['model_style_combo'] ['main'] [0] ['style'];
	$main_price 	= $model_info ['model_style_combo'] ['main'] [0] ['price'];
	$main_hour 		= $model_info ['model_style_combo'] ['main'] [0] ['hour'];
	
	/** �û���� ��ʼ */
	$style ['name1'] 			= '�ó����';
	$style ['value1'] 			= $main_style;
	$style ['name2'] 			= '�۸�';
	$style ['value2'] 			= "{$main_price}Ԫ({$main_hour}Сʱ)";
	$style ['more'] 			= '�鿴ȫ�����ͼ۸�';
	$style ['more_url'] 		= "http://yp.yueus.com/mobile/app?from_app=1#model_style/{$user_id}";
	$style ['more_url_wifi'] 	= "http://yp-wifi.yueus.com/mobile/app?from_app=1#model_style/{$user_id}";

	//���˴���
	$style ['more_url'] 		= "yueyue://goto?type=inner_web&url=" . urlencode($style ['more_url']) . "&wifi_url=" . urlencode($style ['more_url_wifi']);
	$style ['more_url_wifi']  = $style ['more_url'];
	unset($style ['more_url_wifi']);

	$data ['style'] = $style;
	/** �û���� ���� */
	
	/** �û���Ϣ ��ʼ */
	$state ['name1'] 		= '����Ҫ��';
	$state ['value1'] 		= "{$model_info ['level_require']}";
	$state ['name2'] 		= '��������';
	$state ['value2'] 		= "������{$model_info ['limit_num']}��";
	if($model_info ['intro'])
	{
		$state ['name3'] 		= '��ע';
		$state ['value3'] 		= "{$model_info ['intro']}";
	}

	$data ['state'] = $state;
	/** �û���Ϣ ���� */
	
	/** �û�������Ϣ ��ʼ */
	
	$date_log = $date_obj->get_model_date_log ( $user_id );
	
	$history ['name'] = '��Щ��ʦԼ�Ĺ�';
	
	foreach ( $date_log as $k => $val )
	{
		$level = $user_level_obj->get_user_level ( $val );
		$user_list [$k] ['u_name'] = '';
		$user_list [$k] ['u_id'] = "{$val}";
		$user_list [$k] ['u_icon'] = get_user_icon ( $val );
		;
		$user_list [$k] ['u_rating'] = "{$level}";
	}
	
	if($appstore_version)
	{
		if($client_data['data']['version'] == $appstore_version)
		{
			unset($user_list);
		}
	}
	
	if (empty ( $user_list ))
		$user_list = array();
	$history ['user_list'] = $user_list;
	$data ['history'] = $history;
	/** �û�������Ϣ ���� */
	
	$pai_user_follow_obj = POCO::singleton ( 'pai_user_follow_class' );
	if ($login_id)
	{
		$is_follow 		= $pai_user_follow_obj->check_user_follow ( $login_id, $user_id );
		$is_be_follow 	= $pai_user_follow_obj->check_user_follow ( $user_id, $login_id );
	}
	if ($is_follow && $is_be_follow)
	{
		$follow_status = 2; //�໥��ע
	}
	elseif ($is_follow)
	{
		$follow_status = 1; //�ѹ�ע
	}
	else
	{
		$follow_status = 0; //δ��ע
	}
	
	if ($model_info ['sex'] == '��')
	{
		$sex = "1";
	}
	else
	{
		$sex = "0";
	}
	
	/** �û�������Ϣ ��ʼ */
	$user_info ['u_name'] 		= $model_info ['nickname'];
	$user_info ['sex'] 			= $sex;
	$user_info ['u_id'] 		= 'ID:' . $user_id;
	$user_info ['u_icon'] 		= get_user_icon ( $user_id );
	$user_info ['u_location'] = $model_info ['city_name'];
	//$user_info['u_credit'] = "{$model_info['jifen']}";
	$user_info ['is_follow'] = "{$follow_status}";
	$user_info ['follow_url'] = '';
	

	$user_info ['dec_num1'] = "{$model_info['be_follow_count']}";
	
	if($appstore_version)
	{
		if($client_data['data']['version'] == $appstore_version)
		{
			$user_info ['dec_num1'] = "0";
		}
	}
	
	$user_info ['dec_name1'] = '��˿';
	$user_info ['dec_url1'] = "yueyue://goto?type=inner_app&pid=1220073&user_id={$user_id}";
	$user_info ['dec_num2'] = 'Lv' . $model_info ['level'];
	$user_info ['dec_name2'] = '����';
	$user_info ['dec_url2'] = 'http://yp.yueus.com/mobile/app?from_app=1#mine/explain/lev';
	$user_info ['dec_url_wifi2'] = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/explain/lev';

	//����
	$user_info ['dec_url2'] 		= "yueyue://goto?type=inner_web&url=" . urlencode($user_info ['dec_url2']) . "&wifi_url=" . urlencode($user_info ['dec_url_wifi2']);
	$user_info ['dec_url_wifi2']  = $user_info ['dec_url2'];
	unset($user_info ['dec_url_wifi2']);

	$user_info ['dec_num3'] = "{$model_info['jifen']}";
	$user_info ['dec_name3'] = '����ֵ';
	$user_info ['dec_url3'] = 'http://yp.yueus.com/mobile/app?from_app=1#mine/explain/charm';
	$user_info ['dec_url_wifi3'] = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/explain/charm';

	//����
	$user_info ['dec_url3'] 		= "yueyue://goto?type=inner_web&url=" . urlencode($user_info ['dec_url3']) . "&wifi_url=" . urlencode($user_info ['dec_url_wifi3']);
	$user_info ['dec_url_wifi3']	= $user_info ['dec_url3'];
	unset($user_info ['dec_url_wifi3']);

	$data ['user_info'] = $user_info;
	/** �û�������Ϣ ���� */
	
	/** ������Ϣ ��ʼ */
	$share ['url'] 				= $model_info ['share_text'] ['url'];
	$share ['qrcodeurl'] 		= $model_info ['share_text'] ['qrcodeurl'];
	$share ['img'] 				= $model_info ['share_text'] ['img'];
	$share ['content'] 		= $model_info ['share_text'] ['content'];
	$share ['title'] 			= $model_info ['share_text'] ['title'];
	$share ['sina_content'] 	= $model_info ['share_text'] ['sina_content'];
	$data ['share'] = $share;
	/** ������Ϣ ���� */
	
	$data ['yue_url'] = "http://yp.yueus.com/mobile/app?from_app=1#model_style/{$user_id}";
	$data ['yue_url_wifi'] = "http://yp-wifi.yueus.com/mobile/app?from_app=1#model_style/{$user_id}";

	//����
	$data ['yue_url'] 			= "yueyue://goto?type=inner_web&url=" . urlencode($data ['yue_url']) . "&wifi_url=" . urlencode($data ['yue_url_wifi']);
	unset($data ['yue_url_wifi']);
	
	$chat_permis = $model_card_obj->check_cameraman_level_require ( $login_id, $user_id, 'chat' );
	//$yue_permis = $model_card_obj->check_cameraman_level_require ( $login_id, $user_id, 'yue' );
	
	$chat_permis = (int)$chat_permis;
	
	if($login_role=='model')
	{
		$yue_permis = 0;
		$yue_permis_text = "ģ�ز���Լģ��";
	}
	else
	{
		$yue_permis = 1;
		$yue_permis_text = "";
	}
	
	
	$model_level_require = $model_card_obj->get_model_level_require($user_id);
	
	if($model_level_require==2)
	{
		$auth_url = "http://yp.yueus.com/mobile/app?from_app=1&from_chat_v2=1&model_id={$user_id}#mine/authentication/v2";
		$auth_url_wifi = "http://yp-wifi.yueus.com/mobile/app?from_app=1&from_chat_v2=1&model_id={$user_id}#mine/authentication/v2";
	}
	elseif($model_level_require==3)
	{
		$auth_url = "http://yp.yueus.com/mobile/app?from_app=1&from_chat_v3=1&model_id={$user_id}#mine/authentication/v3";
		$auth_url_wifi = "http://yp-wifi.yueus.com/mobile/app?from_app=1&from_chat_v3=1&model_id={$user_id}#mine/authentication/v3";
	}
	
	$data ['yue_permis'] = $yue_permis;
	$data ['yue_permis_text'] = $yue_permis_text;
	
	$data ['auth_url'] = "yueyue://goto?type=inner_web&url=" . urlencode($auth_url) . "&wifi_url=" . urlencode($auth_url_wifi);
	
//	if($login_role=='cameraman')
//	{
//		$data ['chat_permis'] = $chat_permis;
//	}
//	else
//	{
//		$data ['chat_permis'] = 1;
//	}
    $data ['chat_permis'] = 1;
	//���۲���
	$obj = POCO::singleton ( 'pai_alter_price_class',array(true) );
	$tag = $obj->get_topic_user_tag($user_id);
	if($tag)
	{
		$data['tips'] = $tag;
		$data['tips_icon'] = 'yes';
	}

	//$data['tips'] = '����';
	//$data['tips_icon'] = 'yes';
}
elseif($role='cameraman')
{
	$data ['mid'] = '122RO02001'; //����ID

	$cameraman_info = $cameraman_card_obj->get_cameraman_card_by_user_id ( $user_id );
	
	$data ['nickname'] = $cameraman_info ['nickname']; //�û���
	
	/**  �û�ͼƬ ��ʼ */
	foreach ( $cameraman_info ['pic_arr'] as $k => $val )
	{
		$img_arr [$k] ['small_url'] = $val ['img'];
		$img_arr [$k] ['url'] = yueyue_resize_act_img_url ( $val ['img'] );
		$img_arr [$k] ['text'] = '';
	}
	if(!$img_arr) $img_arr=array();
	$data ['img_arr'] = $img_arr;
	
	/**  �û�ͼƬ ���� */
	
	
	/** �û����� ��ʼ */
	$_score 				= round ( $cameraman_info ['score'] );
	$score ['name1'] 		= '�ۺ�����';
	$score ['value1'] 		= "{$_score}";
	$score ['unit1']        = '��';
	$score ['name2'] 		= '��Ƭ����';
	$score ['value2'] 		= "{$cameraman_info ['take_photo_times']}";
	$score['name3'] 		= '������';
	$score['value3'] 		= "{$cameraman_info ['attendance']}%";
	if($cameraman_info ['intro'])
	{
		$score['name4'] 		= '��ע';
		$score['value4'] 		= $cameraman_info ['intro'];
	}
	$score['url']         = "yueyue://goto?type=inner_app&pid=1220075&user_id=" . $user_id;
	$data ['score'] 		= $score;

	/** �û����� ���� */
	

	
	/** �û�������Ϣ ��ʼ */
	
	$date_log = $date_obj->get_cameraman_date_log ( $user_id );
	$history ['name'] = 'Լ�Ĺ���ģ��';
	foreach($date_log as $k=>$__user_id)
	{
		$user_list[$k]['u_icon'] 	= get_user_icon($__user_id);
		$user_list[$k]['u_id'] 		= $__user_id;
		$score_arr = $score_rank_obj->get_score_rank($__user_id);
		$user_list[$k]['u_name'] = change_format_number($score_arr['score'])."����";
	}
	
	if($appstore_version)
	{
		if($client_data['data']['version'] == $appstore_version)
		{
			unset($user_list);
		}
	}
	
	if (empty ( $user_list ))
		$user_list = array();
	$history ['user_list'] = $user_list;
	$data ['history'] = $history;
	/** �û�������Ϣ ���� */
	
	$pai_user_follow_obj = POCO::singleton ( 'pai_user_follow_class' );
	if ($login_id)
	{
		$is_follow 		= $pai_user_follow_obj->check_user_follow ( $login_id, $user_id );
		$is_be_follow 	= $pai_user_follow_obj->check_user_follow ( $user_id, $login_id );
	}
	if ($is_follow && $is_be_follow)
	{
		$follow_status = 2; //�໥��ע
	}
	elseif ($is_follow)
	{
		$follow_status = 1; //�ѹ�ע
	}
	else
	{
		$follow_status = 0; //δ��ע
	}
	
	if ($cameraman_info ['sex'] == 'Ů')
	{
		$sex = "0";
	}
	else
	{
		$sex = "1";
	}
	
	/** �û�������Ϣ ��ʼ */
	$user_info ['u_name'] = $cameraman_info ['nickname'];
	$user_info ['sex'] = $sex;
	$user_info ['u_id'] = 'ID:' . $user_id;
	$user_info ['u_icon'] = get_user_icon ( $user_id );
	$user_info ['u_location'] = $cameraman_info ['city_name'];
	$user_info['u_credit'] = "{$cameraman_info['user_level']}";
	$user_info ['is_follow'] = "{$follow_status}";
	$user_info ['follow_url'] = '';
	$user_info ['dec_num1'] = "Lv{$cameraman_info['user_level']}";
	$user_info ['dec_name1'] = '��Ա�ȼ�';
	//$user_info ['dec_url1'] = "";
	//$user_info ['dec_url_wifi1'] = "";
	

	$user_info ['dec_num2'] =  "{$cameraman_info ['fans']}";
	$user_info ['dec_num3'] = "{$cameraman_info['follow']}";
	
	
	if($appstore_version)
	{
		if($client_data['data']['version'] == $appstore_version)
		{
			$user_info ['dec_num2'] = "0";
			$user_info ['dec_num3'] = "0";
		}
	}
	
	$user_info ['dec_name2'] = '��˿';
	$user_info ['dec_url2'] = "yueyue://goto?type=inner_app&pid=1220073&user_id={$user_id}";
	
	$user_info ['dec_name3'] = '��ע';
	$user_info ['dec_url3'] = "yueyue://goto?type=inner_app&pid=1220074&user_id={$user_id}";
	$data ['user_info'] = $user_info;
	/** �û�������Ϣ ���� */
	
	$data ['chat_permis'] = 1;
	
	
	/** ������Ϣ ��ʼ */
	$share ['user_id']          = $user_id; 
	$share ['url'] 				= $cameraman_info ['share_text'] ['url'];
	$share ['qrcodeurl'] 		= $cameraman_info ['share_text'] ['qrcodeurl'];
	$share ['img'] 				= $cameraman_info ['share_text'] ['img'];
	$share ['content'] 		= $cameraman_info ['share_text'] ['content'];
	$share ['title'] 			= $cameraman_info ['share_text'] ['title'];
	$share ['weixin_content'] 		= $cameraman_info ['share_text'] ['weixin_content'];
	$share ['weixin_title'] 			= $cameraman_info ['share_text'] ['weixin_title'];
	$share ['sina_content'] 	= $cameraman_info ['share_text'] ['sina_content'];
	$data ['share'] = $share;
	/** ������Ϣ ���� */
	
}



$options ['data'] = $data;
$cp->output ( $options );


function change_format_number($number) {
    if($number >= 1000) {
       return  number_format($number/1000,1) . "k";
    }
    else {
        return $number;
    }
}

?>