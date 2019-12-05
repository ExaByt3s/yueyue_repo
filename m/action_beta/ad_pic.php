<?php
/**
 * ���ͼƬ
 * zy 2014.10.9
 */


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
$cms_obj        = new cms_system_class();

$ads_obj = POCO::singleton('pai_ads_class');

/**
 * ҳ����ղ���
 */
$page = intval($_INPUT['page']);
$location_id = $_COOKIE['yue_location_id'];

switch($location_id)
{
    //����
    case 101029001:
        $rank_id = 8;
        break;

    //�人
    case 101019001:
        $rank_id = 54;
        break;

    //����
    case 101001001:
        $rank_id = 55;
        break;

    //�Ϻ�
    case 101003001:
        $rank_id = 56;
        break;

     //�ɶ�
    case 101022001:
        $rank_id = 58;
        break;

    //����
    case 101004001:
        $rank_id = 57;
        break;


    default:
        $rank_id = 8;
        break;
}

if($yue_login_id)
{
    $pai_user_obj = POCO::singleton ( 'pai_user_class' );
    if($pai_user_obj->check_role($yue_login_id) == 'model')
    {
        //ģ�ذ�ID
        $rank_id = 9;
    }
}


$info = $cms_obj->get_last_issue_record_list(false, '0,6', 'place_number DESC', $rank_id);
foreach($info AS $key=>$val)
{
	if($val['link_type']=='inner_web')
	{
		$id = str_replace("http://yp.yueus.com/mobile/app?from_app=1#topic/","",$val['link_url']);
		$new_info[$key]['img'] = $val['img_url'];
		$new_info[$key]['id'] = $id;
		$new_info[$key]['link_type'] = "inside";
		$new_info[$key]['link_address'] = "topic/{$id}";
		$new_info[$key]['index'] = "index";
	}
}
	


$output_arr['list'] = $new_info;



mobile_output($output_arr,false);



?>