<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id     = $client_data['data']['param']['user_id'];


$score_obj              = POCO::singleton ('pai_score_rank_class');
$date_obj               = POCO::singleton ('pai_date_rank_class');
$user_obj               = POCO::singleton ('pai_model_style_v2_class');
$pic_obj                = POCO::singleton ('pai_pic_class');
$pic_score_obj          = POCO::singleton ('pai_score_rank_class');
$model_card_obj         = POCO::singleton ('pai_model_card_class');
$cameraman_card_obj     = POCO::singleton ('pai_cameraman_card_class');
$rank_event_obj         = POCO::singleton('pai_rank_event_class');
$pai_user_obj           = POCO::singleton ( 'pai_user_class' );
$pai_cms_parse_obj      = POCO::singleton( 'pai_cms_parse_class' );
$cms_obj                = new cms_system_class();


if(version_compare($client_data['data']['version'], '1.0.5', '>='))
{
//������
    $role = $pai_user_obj->check_role($user_id);

//��ʼ������
    if(empty($role))        $role         = 'cameraman';
    if(empty($location_id)) $location_id  = '101029001';

    $ranking_array = $rank_event_obj->get_rank_event_by_location_id($location_id, $role);
//print_r($ranking_array);
    $data = $pai_cms_parse_obj->cms_parse_by_array($ranking_array);
    $options['data'] = $data;
}
else
{
//���а�
    $ranking_array = array('12'=>array('ÿ����ģ', 'new_model', '����', ''),
        '14'=>array('˽�ı����ĸ�����', 'spbwpgpy', 'Сʱ', ''),
        '11'=>array('�Ȼ����ջ�', 'xiong_model', '����', ''),
        '3'=>array('�������а�', 'score_list', '����', 'http://yp.yueus.com/mobile/app?from_app=1#mine/explain/charm_list'),
        '5'=>array('�������а�', 'comment_list', '��', 'http://yp.yueus.com/mobile/app?from_app=1#mine/explain/date_list'),
        '10'=>array('�Ը�Ů�� ˽��ר��', 'hot_model', 'Сʱ', ''),
        '13'=>array('�����ӵ���Լ', 'recommend_model', 'Сʱ', ''),
        '27'=>array('����Ů�� ��������', 'ctns_wmbl', '����', ''),
        '28'=>array('Ů��ѧ�ñ�ҵ��', 'nsxtbyl', '����', ''),
        '29'=>array('΢Ц��ʹ ��Ц����', 'wxts_axax', '����', ''),
        '31'=>array('ԼԼ�Ƽ� ��Ƭ��֤', 'yytj_cpbz', 'Сʱ', ''),
        '30'=>array('����ģ��', 'gdmt', '����', ''),
    );


    $pai_user_obj = POCO::singleton ( 'pai_user_class' );
    if($pai_user_obj->check_role($user_id) == 'model')
    {
        $role = 1;
        $ranking_array = array('15'=>array('', 'pay_cameraman', '', ''),
            '17'=>array('������ģ��', 'search_cameraman', '', ''),
            '21'=>array('��Ӱʦ Լ�����а�', 'date_cameraman', '', ''),
            '20'=>array('��Ӱʦ �������а�', 'comment_cameraman', '', '')
        );
    }

    if(version_compare($client_data['data']['version'], '1.0.4', '>='))
    {
        if($pai_user_obj->check_role($user_id) == 'model')
        {
            $role = 1;
            $ranking_array = array('15'=>array('', 'pay_cameraman', '', ''),
                '17'=>array('������ģ��', 'search_cameraman', '', ''),
                '21'=>array('��Ӱʦ Լ�����а�', 'date_cameraman', '', ''),
                '20'=>array('��Ӱʦ �������а�', 'comment_cameraman', '', '')
            );
        }else{
            $role = 0;
            switch($location_id)
            {
                case 101019001:
                    $ranking_array = array('24'=>array('ÿ����ģ', 'whb', 'Сʱ', ''),
                        //'32'=>array('����ֵ���а�', 'wh_mlbxb', '����', ''),
                        //'33'=>array('�Ը�Ů�� ˽��ר��','wh_xgnw', '����', ''),
                        '34'=>array('ԼԼ�Ƽ� ��Ƭ��֤','wh_yytj', 'Сʱ', ''),
                        //'35'=>array('����ģ��', 'wh_gdmt', '����', '')
                    );
                    break;

                case 101001001:
                    $ranking_array = array('25'=>array('ÿ����ģ', '25', 'Сʱ', ''),
                        //'36'=>array('����ֵ���а�', '36', '����', ''),
                        '37'=>array('�Ը�Ů�� ˽��ר��','37', '����', ''),
                        '38'=>array('ԼԼ�Ƽ� ��Ƭ��֤','38', 'Сʱ', ''),
                        '39'=>array('����ģ��', '39', '����', ''));
                    break;

                case 101003001:
                    $ranking_array = array('26'=>array('ÿ����ģ', '26', 'Сʱ', ''),
                        //'40'=>array('����ֵ���а�', '40', '����', ''),
                        '41'=>array('�Ը�Ů�� ˽��ר��','41', '����', ''),
                        '42'=>array('ԼԼ�Ƽ� ��Ƭ��֤','42', 'Сʱ', ''),
                        '43'=>array('����ģ��', '43', '����', ''));
                    break;

                case 101004001:
                    $ranking_array = array('44'=>array('ÿ����ģ', '44', 'Сʱ', ''),
                        //'45'=>array('����ֵ���а�', '45', '����', ''),
                        //'46'=>array('�Ը�Ů�� ˽��ר��','46', '����', ''),
                        '47'=>array('ԼԼ�Ƽ� ��Ƭ��֤','47', 'Сʱ', ''),
                        //'48'=>array('����ģ��', '48', '����', '')
                    );
                    break;

                case 101022001:
                    $ranking_array = array('49'=>array('ÿ����ģ', '49', 'Сʱ', ''),
                        //'50'=>array('����ֵ���а�', '50', '����', ''),
                        '51'=>array('�Ը�Ů�� ˽��ר��','51', '����', ''),
                        '52'=>array('ԼԼ�Ƽ� ��Ƭ��֤','52', 'Сʱ', ''),
                        '53'=>array('����ģ��', '53', '����', ''));
                    break;
            }
        }
    }

    /**
    if($client_data['data']['version'] == '88.8.8'){
    $ranking_array = array('22'=>array('�������а�', 'test_list', 'Сʱ', ''));
    }
     **/

    foreach($ranking_array AS $key=>$val)
    {
        $data  = '';
        if($val[1] == 'score_list' || $val[1] == 'comment_list')
        {
            $info = $cms_obj->get_last_issue_record_list(false, '0,4', 'place_number DESC', $key);
            $info_count = $cms_obj->get_last_issue_record_list(TRUE, '0,4', 'place_number DESC', $key);
        }else{
            $info = $cms_obj->get_last_issue_record_list(false, '0,4', 'place_number ASC', $key);
            $info_count = $cms_obj->get_last_issue_record_list(TRUE, '0,4', 'place_number DESC', $key);
        }


        $data['name']  = $val[0];
        $data['query'] = $val[1];
        if(version_compare($client_data['data']['version'], '1.0.5', '>='))
        {
            if($info_count > 4) $data['query_str'] ='����';
        }

        if(version_compare($client_data['data']['version'], '1.0.5', '>='))
        {
            $data['mid']   = "122PT02001";
            $data['dmid']  = "$key";
        }

        if($val[3])
        {
            $data['about'] = $val[3];
        }else{
            $data['about'] = "";
        }

        $unit = $val[2];
        $record = '';
        foreach($info AS $k=>$v)
        {
            $record['user_id']      = $v['user_id'];
            if(version_compare($client_data['data']['version'], '1.0.5', '>='))
            {
                $record['vid']          = $v['user_id'];
                $record['jid']          = "001";
                $record['dmid']         = "$key";

            }

            if($role)
            {

                $record['url']          = 'http://yp.yueus.com/mobile/app?from_app=1#zone/' . $v['user_id'] . '/cameraman';
                $record['url_wifi']     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#zone/' . $v['user_id'] . '/cameraman';


                if($key == 15)
                {
                    $record['user_icon'] = $v['img_url'];
                    $record['nickname']  = $v['title'];
                    $record['url']       = $v['link_url'];
                    $record['url_wifi']  = str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']);
                    $record['num']       = $v['remark'];
                    $record['style']     = '����';
                }elseif($role){
                    $record['user_icon'] = $v['img_url'];
                    $record['nickname']  = $v['title'];
                    $record['num']       = $v['remark'];
                    $record['style']     = '';
                }
                //ͷ���޸�
                $pic_array = $pic_obj->get_user_pic($v['user_id'],  '0,5');
                foreach($pic_array AS $a=>$b)
                {

                    $num = explode('?', $b['img']);
                    $num = explode('x', $num[1]);
                    $num_v2 = explode('_', $num[1]);

                    $width = $num[0];
                    $height = $num_v2[0];

                    if($width<$height)
                    {
                        $record['user_icon'] = str_replace("_260.", "_440.", $b['img']);
                        break;
                    }
                    $record['user_icon'] = str_replace("_260.", "_440.", $b['img']);
                }

                if(version_compare($client_data['data']['version'], '1.0.5', '>=')){
                    $cameraman_card_info = $cameraman_card_obj->get_cameraman_card_info($v['user_id']);
                    if($cameraman_card_info['cover_img'])
                    {
                        $record['user_icon'] = $cameraman_card_info['cover_img'];
                    }
                }

            }else{
                $record['url']          = 'http://yp.yueus.com/mobile/app?from_app=1#model_card/' . $v['user_id'];
                $record['url_wifi']     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#model_card/' . $v['user_id'];

                if($unit == '����')
                {
                    $result                 = $pic_score_obj->get_score_rank($v['user_id']);
                    $record['num']          = $result['score'];
                    $record['unit']         = $unit;
                }elseif($unit == '��'){
                    $record['num']          = $v['place_number'] * 2;
                    $record['unit']         = $unit;
                }elseif($unit == 'Сʱ'){
                    $sql_str = "SELECT hour, price FROM pai_db.pai_model_style_v2_tbl WHERE user_id=$record[user_id] AND group_id=1";
                    $result = db_simple_getdata($sql_str, TRUE, 101);
                    if($result['hour'])
                    {
                        $record['num']          = $result['price'] . "/" . $result['hour'];
                        $record['unit']         = $unit;
                    }

                }

                $record['nickname']     = $v['user_name'];
                $record['user_icon']    =  get_user_icon($v['user_id'], $size = 468);

                //ͷ���޸�
                $pic_array = $pic_obj->get_user_pic($v['user_id'], $limit = '0,5');
                foreach($pic_array AS $a=>$b)
                {

                    $num = explode('?', $b['img']);
                    $num = explode('x', $num[1]);
                    $num_v2 = explode('_', $num[1]);

                    $width = $num[0];
                    $height = $num_v2[0];

                    if($width<$height)
                    {
                        $record['user_icon'] = str_replace("_260.", "_440.", $b['img']);
                        break;
                    }
                    $record['user_icon'] = str_replace("_260.", "_440.", $b['img']);
                }
                $user_info              = $user_obj->get_model_style_combo($v['user_id']);
                $style_array            = explode(' ', $user_info['main'][0]['style']);
                $record['style']        = $style_array[0]?$style_array[0]:'����';



                if(version_compare($client_data['data']['version'], '1.0.5', '>=')){

                    $model_card_info = $model_card_obj->get_model_card_info($v['user_id']);

                    if($model_card_info['cover_img'])
                    {
                        $record['user_icon'] = $model_card_info['cover_img'];
                    }
                }

            }

            $data['user_list'][]      = $record;

        }



        $options['data'][] = $data;
        $data = '';
    }
}

$cp->output($options);
?>