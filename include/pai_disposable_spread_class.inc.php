<?php
/**
 * @file pai_disposable_spread_class.inc.php
 * @synopsis 一次性推广活动类
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

    //评价后触发，大婧，有效期至2015-12-10 23:59:59
    public function __dajing_spread_mission_A($order_info)
    {
        if( !is_array($order_info) || count($order_info)<1 )
        {
            return true;
        }

        //合作的商家配置，大婧，有效期至2015-12-10 23:59:59
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

        //评价赠券推广链接，大婧，有效期至2015-12-10 23:59:59
        $cate_message_info = array(
            //约美食
            41 => array(
                'msg_type' => 2,
                'card_text1' => '1元超值爆品，1000+份限时抢 来狂欢，不孤单，我买单！',
                'card_title' => '优惠券已赠送，马上使用！',
                'link_url' => '/mall/user/topic/index.php?topic_id=831&online=1',
            ),
            //约活动
            42 => array(
            ),
            //约有趣
            43 => array(
                'msg_type' => 2,
                'card_text1' => '1元超值爆品，1000+份限时抢 来狂欢，不孤单，我买单！',
                'card_title' => '优惠券已赠送，马上使用！',
                'link_url' => '/mall/user/topic/index.php?topic_id=833&online=1',
            ),
            //约摄影师
            40 => array(
                'msg_type' => 2,
                'card_text1' => '1元超值爆品，1000+份限时抢 来狂欢，不孤单，我买单！',
                'card_title' => '优惠券已赠送，马上使用！',
                'link_url' => '/mall/user/topic/index.php?topic_id=834&online=1',
            ),
            //模特服务
            31 => array(
                'msg_type' => 2,
                'card_text1' => '1元超值爆品，1000+份限时抢 来狂欢，不孤单，我买单！',
                'card_title' => '优惠券已赠送，马上使用！',
                'link_url' => '/mall/user/topic/index.php?topic_id=842&online=1',
            ),
            //场地租赁
            12 => array(
                'msg_type' => 2,
                'card_text1' => '1元超值爆品，1000+份限时抢 来狂欢，不孤单，我买单！',
                'card_title' => '优惠券已赠送，马上使用！',
                'link_url' => '/mall/user/topic/index.php?topic_id=834&online=1',
            ),
            //化妆服务
            3  => array(
                'msg_type' => 2,
                'card_text1' => '1元超值爆品，1000+份限时抢 来狂欢，不孤单，我买单！',
                'card_title' => '优惠券已赠送，马上使用！',
                'link_url' => '/mall/user/topic/index.php?topic_id=834&online=1',
            ),
            //约培训
            5  => array(
                'msg_type' => 2,
                'card_text1' => '1元超值爆品，1000+份限时抢 来狂欢，不孤单，我买单！',
                'card_title' => '优惠券已赠送，马上使用！',
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
                'media_type' => 'card', // (text:文字; notify:系统通知; card:卡片)
                'card_style' => 2, // (1:中间有金额那个模板; 2:只有一条文字那个模板)
                'card_text1' => trim($message_info['card_text1']), // (服务media_type=card的，最上标题)
                'card_title' => trim($message_info['card_title']), // (服务media_type=card的，底部)
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
            if( $count_queue<5 ) //防刷单限制为5次
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
            if( $count_queue<5 ) //防刷单限制为5次
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

    //签到后触发，老谢，有效期至2015-11-30 23:59:59
    public function __laoxie_spread_mission_A($order_info)
    {
        //抽奖送券指定商品, 老谢，有效期未知
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
            $rand_no = rand(1, 100);//精度可调
            switch(true)//比例调整
            {
                case $rand_no>=1 && $rand_no<=10://10%
                    $give_code = 'Y2015M11D27_ONE_BUCK_1';
                    $message_data = array(
                        'media_type' => 'card', // (text:文字; notify:系统通知; card:卡片)
                        'card_style' => 2, // (1:中间有金额那个模板; 2:只有一条文字那个模板)
                        'card_text1' => '约约专属感恩回馈，总有一款让你动心', // (服务media_type=card的，最上标题)
                        'card_title' => '好礼已到，即刻领取', // (服务media_type=card的，底部)
                        'link_url' => 'yueyue://goto?type=inner_app&pid=1220102&goods_id=2131790_80',
                    );
                    break;
                case $rand_no>=11 && $rand_no<=25://15%
                    $give_code = 'Y2015M11D27_ONE_BUCK_2';
                    $message_data = array(
                        'media_type' => 'card', // (text:文字; notify:系统通知; card:卡片)
                        'card_style' => 2, // (1:中间有金额那个模板; 2:只有一条文字那个模板)
                        'card_text1' => '约约专属感恩回馈，总有一款让你动心', // (服务media_type=card的，最上标题)
                        'card_title' => '好礼已到，即刻领取', // (服务media_type=card的，底部)
                        'link_url' => 'yueyue://goto?type=inner_app&pid=1220102&goods_id=2131783_81',
                    );
                    break;
                case $rand_no>=26 && $rand_no<=35://10%
                    $give_code = 'Y2015M11D27_ONE_BUCK_3';
                    $message_data = array(
                        'media_type' => 'card', // (text:文字; notify:系统通知; card:卡片)
                        'card_style' => 2, // (1:中间有金额那个模板; 2:只有一条文字那个模板)
                        'card_text1' => '约约专属感恩回馈，总有一款让你动心', // (服务media_type=card的，最上标题)
                        'card_title' => '好礼已到，即刻领取', // (服务media_type=card的，底部)
                        'link_url' => 'yueyue://goto?type=inner_app&pid=1220102&goods_id=2131784_82',
                    );
                    break;
                case $rand_no>=36 && $rand_no<=45://10%
                    $give_code = 'Y2015M11D27_ONE_BUCK_4';
                    $message_data = array(
                        'media_type' => 'card', // (text:文字; notify:系统通知; card:卡片)
                        'card_style' => 2, // (1:中间有金额那个模板; 2:只有一条文字那个模板)
                        'card_text1' => '约约专属感恩回馈，总有一款让你动心', // (服务media_type=card的，最上标题)
                        'card_title' => '好礼已到，即刻领取', // (服务media_type=card的，底部)
                        'link_url' => 'yueyue://goto?type=inner_app&pid=1220102&goods_id=2131791',
                    );
                    break;
                case $rand_no>=46 && $rand_no<=85://40%
                    $give_code = 'Y2015M11D27_ONE_BUCK_5';
                    $message_data = array(
                        'media_type' => 'text', // (text:文字; notify:系统通知; card:卡片)
                        'content' => '您好，恭喜您获得“1元抽好礼”活动10元优惠劵。赶紧挑选中意的商品使用吧！', // (服务media_type=card的，最上标题)
                    );
                    break;
                case $rand_no>=86 && $rand_no<=100://15%
                    $give_code = 'Y2015M11D27_ONE_BUCK_6';
                    $message_data = array(
                        'media_type' => 'text', // (text:文字; notify:系统通知; card:卡片)
                        'content' => '您好，恭喜您获得“1元抽好礼”活动50元优惠劵。赶紧挑选中意的商品使用吧！', // (服务media_type=card的，最上标题)
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
