<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id     = $client_data['data']['param']['user_id'];

$pai_user_obj           = POCO::singleton ( 'pai_user_class' );


//������
$role = $pai_user_obj->check_role($user_id);
if($role == 'model')
{
    $data['mid'] = '122PT02002';

    $result['dmid'] = 'm1';
    $result['text'] = '��������';
    $result['text1'] = '';
    $result['text_col'] = '0xffffffff';
    $result['bg_col'] = '0xff73b3eb';
    $result['bg_col_over'] = '0xff9dcaf1';
    $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#camera_demand/list') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#camera_demand/list');
    
    //if(version_compare($client_data['data']['version'], '2.1.10', '=') || $client_data['data']['version'] == '2.1.0_r3') $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/141') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/141');
    
    $pai_oa_obj             = POCO::singleton ( 'pai_oa_import_order_class' );
    $last_visit_time        = $pai_oa_obj->get_oa_last_visit($user_id, $location_id);
    $oa_order_obj           = POCO::singleton ( 'pai_model_oa_order_class' );
    $where = '';
    if($last_visit_time)    $where  =" AND audit_time>='{$last_visit_time}' ";
    if($location_id)        $where  .=" AND location_id = '{$location_id}'";

    $recom_num =  $oa_order_obj->get_requirement_list(true, $where);
    if($recom_num > 0)
    {
        $result['recom_num']  = $recom_num;
    }else{
        $result['recom_num'] = "";
    }

    $result['icon'] = '';
    $data[layout][] = $result;
    unset($result);


    $array_topic_id[101029001] = 114; //����
    $array_topic_id[101019001] = 131; //�人
    $array_topic_id[101001001] = 129; //����
    $array_topic_id[101003001] = 130; //�Ϻ�
    $array_topic_id[101022001] = 133; //�ɶ�
    $array_topic_id[101004001] = 132; //����
    $array_topic_id[101015001] = 134; //����
    $topid_id = $array_topic_id[$location_id]?$array_topic_id[$location_id]:112;

    $result['dmid'] = 'm2';
    $result['text'] = '��Ӱʦ����';
    $result['text1'] = '';
    $result['text_col'] = '0xffffffff';
    $result['bg_col'] = '0xff6ad4d4';
    $result['bg_col_over'] = '0xff96e1e1';
    $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic/$topid_id") . '&wifi_url=' . urlencode("http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/$topid_id");
    //if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/143') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/143');
    $result['recom_num'] = '';
    $result['icon'] = '';
    $data[layout][] = $result;
    unset($result);

    $result['dmid'] = 'm3';
    $result['text'] = 'ģ������';
    $result['text1'] = '';
    $result['text_col'] = '0xffffffff';
    $result['bg_col'] = '0xff58c7eb';
    $result['bg_col_over'] = '0xff8ad8f1';
    $result['url'] = 'yueyue://goto?type=inner_app&pid=1220005';
    //if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/144') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/144');
    $result['recom_num'] = '';
    $result['icon'] = '';
    $data[layout][] = $result;
    unset($result);

    $result['dmid'] = 'm4';
    $result['text'] = 'Ů��ѧ��';
    $result['text1'] = '';
    $result['text_col'] = '0xffffffff';
    $result['bg_col'] = '0xffff7ea0';
    $result['bg_col_over'] = '0xffffa4bc';
    $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/38') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/38');
    //if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/140') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/140');
    $result['recom_num'] = '';
    $result['icon'] = '';
    $data[layout][] = $result;
    unset($result);

    $result['dmid'] = 'm5';
    $result['text'] = 'ԼԼר��';
    $result['text1'] = '';
    $result['text_col'] = '0xffffffff';
    $result['bg_col'] = '0xffff8181';
    $result['bg_col_over'] = '0xffffa7a7';
    $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic_list') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic_list');
    //if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/145') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/145');
    $result['recom_num'] = '';
    $result['icon'] = '';
    $data[layout][] = $result;
    unset($result);

    $result['dmid'] = 'm6';
    $result['text'] = 'ԼԼ��Ʒ';
    $result['text1'] = '';
    $result['text_col'] = '0xffffffff';
    $result['bg_col'] = '0xfffe9f76';
    $result['bg_col_over'] = '0xfffebc9f';
    $result['url'] = 'yueyue://goto?type=inner_web&url='. urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/95') .'&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/95');
    //if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/142') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/142');
    $result['recom_num'] = '';
    $result['icon'] = '';
    $data[layout][] = $result;
    unset($result);
}else{
    $data['mid'] = '122PT02003';

    $result['dmid'] = 'c7';
    $result['text'] = '������ ��ģ��';
    $result['text1'] = '';
    $result['text_col'] = '0xffffffff';
    $result['bg_col'] = '0xff73b3eb';
    $result['bg_col_over'] = '0xff9dcaf1';
    $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#demand') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#demand') ;
    $result['recom_num'] = '';
    $result['icon'] = '';
    $data[layout][] = $result;
    unset($result);

    $result['dmid'] = 'c8';
    $result['text'] = '����С����';
    $result['text1'] = '';
    $result['text_col'] = '0xffffffff';
    $result['bg_col'] = '0xff6ad4d4';
    $result['bg_col_over'] = '0xff96e1e1';

    if(version_compare($client_data['data']['version'], '2.1.0', '>=')) {
        $result['url'] = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode(iconv('gbk','utf8','���˾�Ʒ����'));
    }else{
        $result['url'] = 'yueyue://goto?type=inner_app&pid=1220076&keyword_query=' . urlencode('���˾�Ʒ����');
    }

    $result['recom_num'] = '';
    $result['icon'] = '';
    $data[layout][] = $result;
    unset($result);

    $result['dmid'] = 'c9';
    $result['text'] = '�Ƽ�ģ��';
    $result['text1'] = '';
    $result['text_col'] = '0xffffffff';
    $result['bg_col'] = '0xff58c7eb';
    $result['bg_col_over'] = '0xff8ad8f1';

    $array_topic_id[101029001] = 112; //����
    $array_topic_id[101019001] = 125; //�人
    $array_topic_id[101001001] = 128; //����
    $array_topic_id[101003001] = 123; //�Ϻ�
    $array_topic_id[101022001] = 126; //�ɶ�
    $array_topic_id[101004001] = 127; //����
    $array_topic_id[101015001] = 124; //����
    $array_topic_id[101024001] = 243; //�½�
    $topid_id = $array_topic_id[$location_id]?$array_topic_id[$location_id]:112;

    $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic/$topid_id") . '&wifi_url=' . urlencode("http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/$topid_id");
    //if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/146') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/146');
    $result['recom_num'] = '';
    $result['icon'] = '';
    $data[layout][] = $result;
    unset($result);


    $result['dmid'] = 'c10';
    $result['text'] = '��Ӱ����';
    $result['text1'] = "Ӱ������" . "\r\n" . "��Ӱ��ѵ" . "\r\n" . "��ױ����" . "\r\n" . "...";
    $result['is_login'] = 1;
    $result['text_col'] = '0xffffffff';
    $result['bg_col'] = '0xffff7ea0';
    $result['bg_col_over'] = '0xffffa4bc';
    $task_request_obj = POCO::singleton('pai_task_request_class');
    if($task_request_obj->get_request_is_have($user_id))
    {
        $result['url'] = "yueyue://goto?type=inner_app&pid=1220079";
    }else{
        $result['url'] = "yueyue://goto?type=inner_app&pid=1220080";
    }
    //if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/145') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/145');
    $result['recom_num'] = '';
    $result['icon'] = '';
    $data[layout][] = $result;
    unset($result);


    $result['dmid'] = 'c11';
    $result['text'] = '����ģ��';
    $result['text1'] = '';
    $result['text_col'] = '0xffffffff';
    $result['bg_col'] = '0xfffe9f76';
    $result['bg_col_over'] = '0xfffebc9f';
    $result['url'] = 'yueyue://goto?type=inner_app&pid=1220005';
    //if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/147') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/147');
    $result['recom_num'] = '';
    $result['icon'] = '';
    $data[layout][] = $result;
    unset($result);


    $result['dmid'] = 'c12';
    $result['text'] = '����';
    $result['text1'] = '';
    $result['text_col'] = '0xffffffff';
    $result['bg_col'] = '0xffff8181';
    $result['bg_col_over'] = '0xffffa7a7';
    $result['url'] = 'yueyue://goto?type=inner_app&pid=1220006';
    //if(version_compare($client_data['data']['version'], '2.1.10', '=')) $result['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/148') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/148');
    $result['recom_num'] = '';
    $result['icon'] = '';
    $data[layout][] = $result;
    unset($result);

    if(version_compare($client_data['data']['version'], '2.1.20', '>')) {
        unset($data);

        $data['mid'] = '122PT02005';

        //ͳ�Ʋ���
        $tongji_obj = POCO::singleton('pai_tongji_class');

        $array_yuepai_id[101029001] = 9421; //����
        $array_yuepai_id[101019001] = 9474; //�人
        $array_yuepai_id[101001001] = 9479; //����
        $array_yuepai_id[101003001] = 9484; //�Ϻ�
        $array_yuepai_id[101022001] = 9518; //�ɶ�
        $array_yuepai_id[101004001] = 9489; //����
        $array_yuepai_id[101015001] = 12267; //����
        $array_yuepai_id[101024001] = 12268; //�½�

        //if(version_compare($client_data['data']['version'], '2.2.10', '=') || version_compare($client_data['data']['version'], '2.2.0_r3', '='))   $array_yuepai_id[101029001] = 15133; //����

        $button['str']    = 'ģ����Լ';
        $button['dmid']   = '';
        $button['url']    = 'yueyue://goto?type=inner_app&pid=1220094&query=' . $array_yuepai_id[$location_id];

        //if(version_compare($client_data['data']['version'], '2.2.10', '=')) $button['url'] = 'yueyue://goto?type=inner_app&pid=1220046&query=165';
        $n_data['button_list'][] = $button;

        $button['str']    = '��Ӱ��ѵ';
        $button['dmid']   = '';
        if($location_id == 101029001)
        {
            $button['url']    = 'yueyue://goto?type=inner_app&pid=1220094&query=9423';
        }else{
            $button['url']    = 'yueyue://goto?type=inner_web&url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic/253") . '&wifi_url=' . urlencode("http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/253");
        }
        //if(version_compare($client_data['data']['version'], '2.2.0_r3', '=')) $button['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic/241") . '&wifi_url=' . urlencode("http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/241");
        $n_data['button_list'][] = $button;

        $button['str']    = 'Ӱ������';
        $button['dmid']   = '';
        if($location_id == 101029001)
        {
            $button['url']    = 'yueyue://goto?type=inner_app&pid=1220094&query=9424';
        }elseif($location_id == 101001001){
            $button['url']    = 'yueyue://goto?type=inner_web&url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic_list/17624") . '&wifi_url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic_list/17624");
        }elseif($location_id == 101003001){
            $button['url']    = 'yueyue://goto?type=inner_web&url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic_list/17669") . '&wifi_url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic_list/17669");
        }elseif($location_id == 101004001){
            $button['url']    = 'yueyue://goto?type=inner_web&url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic_list/17670") . '&wifi_url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic_list/17670");
        }elseif($location_id == 101015001){
            $button['url']    = 'yueyue://goto?type=inner_web&url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic_list/17671") . '&wifi_url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic_list/17671");
        }else{
            $button['url']    = 'yueyue://goto?type=inner_web&url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic/253") . '&wifi_url=' . urlencode("http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/253");
        }
        //if(version_compare($client_data['data']['version'], '2.2.0_r3', '=')) $button['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic/314") . '&wifi_url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic/314");
        $n_data['button_list'][] = $button;

        $button['str']    = '��ױ����';
        $button['dmid']   = '';
        if($location_id == 101029001)
        {
            $button['url']    = 'yueyue://goto?type=inner_app&pid=1220094&query=9425';
        }else{
            $button['url']    = 'yueyue://goto?type=inner_web&url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic/253") . '&wifi_url=' . urlencode("http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/253");
        }
        //if(version_compare($client_data['data']['version'], '2.2.0_r3', '=')) $button['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic/315") . '&wifi_url=' . urlencode("http://yp.yueus.com/mobile/app?from_app=1#topic/315");
        $n_data['button_list'][] = $button;


        $button['str']    = '���Ļ';
        $button['dmid']   = '';
        $button['url']    = 'yueyue://goto?type=inner_app&pid=1220094&query=9948';
        //if(version_compare($client_data['data']['version'], '2.2.10', '=')) $button['url'] = 'yueyue://goto?type=inner_web&url=' . urlencode('http://yp.yueus.com/mobile/app?from_app=1#topic/139') . '&wifi_url=' . urlencode('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/139');

        $n_data['button_list'][] = $button;


        $array_list_info_id[101029001] = 196; //����
        $array_list_info_id[101019001] = 196; //�人
        $array_list_info_id[101001001] = 282; //����
        $array_list_info_id[101003001] = 290; //�Ϻ�
        $array_list_info_id[101022001] = 339; //�ɶ�
        $array_list_info_id[101004001] = 288; //����
        $array_list_info_id[101015001] = 283; //����
        $array_list_info_id[101024001] = 196; //�½�

        $cms_obj        = new cms_system_class();
        $key            = $array_list_info_id[$location_id]?$array_list_info_id[$location_id]:196;

        //if(version_compare($client_data['data']['version'], '2.2.0_r3', '=')) $key = 287;
        $list_info      = $cms_obj->get_last_issue_record_list(false, '0,10', 'place_number DESC', $key);

        foreach($list_info AS $val)
        {
            $banner['s_title'] = $val['title'];
            $banner['str'] = $val['content'];
            $banner['img'] = $val['img_url'];
            if($val['link_type'] != 'inner_web')
            {
                $banner['url'] = $val['link_url'];
            }else{
                $banner['url'] = "yueyue://goto?type=inner_web&url=" . urlencode($val['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $val['link_url']));
            }
            $n_data['banner_list'][] = $banner;
         //   $tongji_obj->add_tongji_log($banner['img'],$banner['url'], 'get_hot_top', $location_id, $user_id, $banner['s_title']);
        }

        /**
        $pai_home_page_topic_obj = POCO::singleton('pai_home_page_topic_class');
        $category_array = $pai_home_page_topic_obj->get_big_category($location_id);
        foreach($category_array AS $val)
        {
            $banner['s_title']      = $val['title'];
            $banner['str']          = $val['title'];
            $banner['img']          = $val['app_img'];
            if($val['link_type'] != 'inner_web')
            {
                $banner['url'] = $val['link_url'];
            }else{
                $banner['url'] = "yueyue://goto?type=inner_web&url=" . urlencode($val['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $val['link_url']));
            }
            $n_data['banner_list'][] = $banner;
        }
        * */

        $task_request_obj = POCO::singleton('pai_task_request_class');
        if($task_request_obj->get_request_is_have($user_id))
        {
            $n_data[tt_link] = "yueyue://goto?type=inner_app&pid=1220079";
        }else{
            $n_data[tt_link] = "yueyue://goto?type=inner_app&pid=1220080";
        }

        $data['new_layout'] = $n_data;

    }


}

$options['data'] = $data;

$cp->output($options);
?>