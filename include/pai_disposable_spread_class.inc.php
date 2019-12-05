<?php
/**
 * @file pai_disposable_spread_class.inc.php
 * @synopsis һ�����ƹ���
 * @author wuhy@yueus.com
 * @version null
 * @date 2015-11-26
 */

class pai_disposable_spread_class extends POCO_TDG
{
    public function __construct()
    {
        // $this->setServerId(101);
        // $this->setDBName('pai_coupon_db');
    }

    //���ۺ󴥷�����溣���Ч����2015-12-10 23:59:59
    public function __dajing_spread_mission_A($order_info)
    {
        if( !is_array($order_info) || count($order_info)<1 )
        {
            return true;
        }

        //�������̼����ã���溣���Ч����2015-12-10 23:59:59
        $cate_5_seller_arr = array(
            // 154408,
            // 129342,
            // 195022,
            // 188044,
            // 209222,
            // 205020,
            // 350029,
            // 150116,
        );

        //������ȯ�ƹ����ӣ���溣���Ч����2015-12-10 23:59:59
        $cate_message_info = array(
            //Լ��ʳ
            41 => array(
                'msg_type' => 2,
                'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
                'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
                'link_url' => '/mall/user/topic/index.php?topic_id=831&online=1',
            ),
            //Լ�
            42 => array(
            ),
            //Լ��Ȥ
            43 => array(
                'msg_type' => 2,
                'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
                'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
                'link_url' => '/mall/user/topic/index.php?topic_id=833&online=1',
            ),
            //Լ��Ӱʦ
            40 => array(
                'msg_type' => 2,
                'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
                'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
                'link_url' => '/mall/user/topic/index.php?topic_id=834&online=1',
            ),
            //ģ�ط���
            31 => array(
                'msg_type' => 2,
                'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
                'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
                'link_url' => '/mall/user/topic/index.php?topic_id=842&online=1',
            ),
            //��������
            12 => array(
                'msg_type' => 2,
                'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
                'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
                'link_url' => '/mall/user/topic/index.php?topic_id=834&online=1',
            ),
            //��ױ����
            3  => array(
                'msg_type' => 2,
                'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
                'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
                'link_url' => '/mall/user/topic/index.php?topic_id=834&online=1',
            ),
            //Լ��ѵ
            5  => array(
                'msg_type' => 2,
                'card_text1' => '1Ԫ��ֵ��Ʒ��1000+����ʱ�� ���񻶣����µ������򵥣�',
                'card_title' => '�Ż�ȯ�����ͣ�����ʹ�ã�',
                'link_url' => '/mall/user/topic/index.php?topic_id=836&online=1',
            ),
        );

        $cur_time = time();
        $order_id = intval($order_info['order_id']);
        $type_id = intval($order_info['type_id']);
        $buyer_user_id = $order_info['buyer_user_id'];
        $seller_user_id = $order_info['seller_user_id'];
        $order_total_amount = $order_info['total_amount'];
        $order_pending_amount = $order_info['pending_amount'];
        $goods_id = $order_info['detail_list'][0]['goods_id'];

        $message_info = $cate_message_info[$type_id];

        $message_data = '';
        if( !empty($message_info) )
        {
            $link_url = 'http://yp.yueus.com'.trim($message_info['link_url']);
            $wifi_link_url = 'http://yp-wifi.yueus.com'.trim($message_info['link_url']);

            $message_data = array(
                'media_type' => 'card', // (text:����; notify:ϵͳ֪ͨ; card:��Ƭ)
                'card_style' => 2, // (1:�м��н���Ǹ�ģ��; 2:ֻ��һ�������Ǹ�ģ��)
                'card_text1' => trim($message_info['card_text1']), // (����media_type=card�ģ����ϱ���)
                'card_title' => trim($message_info['card_title']), // (����media_type=card�ģ��ײ�)
                'link_url' => 'yueyue://goto?type=inner_web&showtitle=2&url=' . urlencode($link_url) . '&wifi_url=' . urlencode($wifi_link_url),
            );
        }

        if( $order_total_amount >= 3000 && in_array($seller_user_id, $cate_5_seller_arr) && $type_id==5 && $cur_time>=strtotime('2015-11-07 00:00:00') && $cur_time<=strtotime('2015-11-06 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_100_CATE_5';
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id);
        }
        elseif( $order_total_amount >= 2000 && in_array($seller_user_id, $cate_5_seller_arr) && $type_id==5 && $cur_time>=strtotime('2015-11-07 00:00:00') && $cur_time<=strtotime('2015-11-06 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_50_CATE_5';
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id);
        }
        elseif( $order_pending_amount >= 1000 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_68';
            $where_str = "give_code='{$give_code}' AND user_id={$buyer_user_id}";
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $count_queue = $coupon_give_obj->get_queue_list(-1, true, $where_str);
            if( $count_queue<5 ) //��ˢ������Ϊ5��
            {
                $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id, array('message_data' => $message_data));
            }
        }
        elseif( $order_pending_amount >= 600 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_48';
            $where_str = "give_code='{$give_code}' AND user_id={$buyer_user_id}";
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $count_queue = $coupon_give_obj->get_queue_list(-1, true, $where_str);
            if( $count_queue<5 ) //��ˢ������Ϊ5��
            {
                $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id, array('message_data' => $message_data));
            }
        }
        elseif( $order_pending_amount >= 500 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_38';
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id, array('message_data' => $message_data));
        }
        elseif( $order_pending_amount >= 300 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_28';
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id, array('message_data' => $message_data));
        }
        elseif( $order_pending_amount >= 200 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_18';
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id, array('message_data' => $message_data));
        }
        elseif( $order_pending_amount >= 100 && $cur_time>=strtotime('2015-11-10 00:00:00') && $cur_time<=strtotime('2015-12-10 23:59:59') )
        {
            $give_code = 'Y2015M11D09_CONSUMPTION_BACK_8';
            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $order_id, array('message_data' => $message_data));
        }
        return true;
    }

    //ǩ���󴥷�����л����Ч����2015-11-30 23:59:59
    public function __laoxie_spread_mission_A($order_info)
    {
        //�齱��ȯָ����Ʒ, ��л����Ч��δ֪
        $goods_arr = array(
            2131785,
        );

        $cur_time = time();
        $order_id = intval($order_info['order_id']);
        $type_id = intval($order_info['type_id']);
        $buyer_user_id = $order_info['buyer_user_id'];
        $seller_user_id = $order_info['seller_user_id'];
        $order_total_amount = $order_info['total_amount'];
        $order_pending_amount = $order_info['pending_amount'];
        $goods_id = $order_info['detail_list'][0]['goods_id'];

        if( in_array($goods_id, $goods_arr) && $cur_time <= strtotime("2015-11-30 23:59:59") )
        {
            $rand_no = rand(1, 100);//���ȿɵ�
            switch(true)//��������
            {
                case $rand_no>=1 && $rand_no<=10://10%
                    $give_code = 'Y2015M11D27_ONE_BUCK_1';
                    $message_data = array(
                        'media_type' => 'card', // (text:����; notify:ϵͳ֪ͨ; card:��Ƭ)
                        'card_style' => 2, // (1:�м��н���Ǹ�ģ��; 2:ֻ��һ�������Ǹ�ģ��)
                        'card_text1' => 'ԼԼר���ж�����������һ�����㶯��', // (����media_type=card�ģ����ϱ���)
                        'card_title' => '�����ѵ���������ȡ', // (����media_type=card�ģ��ײ�)
                        'link_url' => 'yueyue://goto?type=inner_app&pid=1220102&goods_id=2131790_80',
                    );
                    break;
                case $rand_no>=11 && $rand_no<=25://15%
                    $give_code = 'Y2015M11D27_ONE_BUCK_2';
                    $message_data = array(
                        'media_type' => 'card', // (text:����; notify:ϵͳ֪ͨ; card:��Ƭ)
                        'card_style' => 2, // (1:�м��н���Ǹ�ģ��; 2:ֻ��һ�������Ǹ�ģ��)
                        'card_text1' => 'ԼԼר���ж�����������һ�����㶯��', // (����media_type=card�ģ����ϱ���)
                        'card_title' => '�����ѵ���������ȡ', // (����media_type=card�ģ��ײ�)
                        'link_url' => 'yueyue://goto?type=inner_app&pid=1220102&goods_id=2131783_81',
                    );
                    break;
                case $rand_no>=26 && $rand_no<=35://10%
                    $give_code = 'Y2015M11D27_ONE_BUCK_3';
                    $message_data = array(
                        'media_type' => 'card', // (text:����; notify:ϵͳ֪ͨ; card:��Ƭ)
                        'card_style' => 2, // (1:�м��н���Ǹ�ģ��; 2:ֻ��һ�������Ǹ�ģ��)
                        'card_text1' => 'ԼԼר���ж�����������һ�����㶯��', // (����media_type=card�ģ����ϱ���)
                        'card_title' => '�����ѵ���������ȡ', // (����media_type=card�ģ��ײ�)
                        'link_url' => 'yueyue://goto?type=inner_app&pid=1220102&goods_id=2131784_82',
                    );
                    break;
                case $rand_no>=36 && $rand_no<=45://10%
                    $give_code = 'Y2015M11D27_ONE_BUCK_4';
                    $message_data = array(
                        'media_type' => 'card', // (text:����; notify:ϵͳ֪ͨ; card:��Ƭ)
                        'card_style' => 2, // (1:�м��н���Ǹ�ģ��; 2:ֻ��һ�������Ǹ�ģ��)
                        'card_text1' => 'ԼԼר���ж�����������һ�����㶯��', // (����media_type=card�ģ����ϱ���)
                        'card_title' => '�����ѵ���������ȡ', // (����media_type=card�ģ��ײ�)
                        'link_url' => 'yueyue://goto?type=inner_app&pid=1220102&goods_id=2131791',
                    );
                    break;
                case $rand_no>=46 && $rand_no<=85://40%
                    $give_code = 'Y2015M11D27_ONE_BUCK_5';
                    $message_data = array(
                        'media_type' => 'text', // (text:����; notify:ϵͳ֪ͨ; card:��Ƭ)
                        'content' => '���ã���ϲ����á�1Ԫ����񡱻10Ԫ�Ż݄����Ͻ���ѡ�������Ʒʹ�ðɣ�', // (����media_type=card�ģ����ϱ���)
                    );
                    break;
                case $rand_no>=86 && $rand_no<=100://15%
                    $give_code = 'Y2015M11D27_ONE_BUCK_6';
                    $message_data = array(
                        'media_type' => 'text', // (text:����; notify:ϵͳ֪ͨ; card:��Ƭ)
                        'content' => '���ã���ϲ����á�1Ԫ����񡱻50Ԫ�Ż݄����Ͻ���ѡ�������Ʒʹ�ðɣ�', // (����media_type=card�ģ����ϱ���)
                    );
                    break;
                default:
                    return true;
                    break;
            }

            $coupon_give_obj = POCO::singleton('pai_coupon_give_class');
            $submit_rst = $coupon_give_obj->submit_queue($give_code, '', $buyer_user_id, $goods_id, array('message_data' => $message_data));
        }
        return true;
    }
}
