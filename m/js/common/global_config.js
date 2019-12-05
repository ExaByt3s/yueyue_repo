/**
 * 全局参数设置
 */
define(function(require, exports)
{
    var action_ver = 'action';

    // 处理origin兼容性问题
    if (!window.location.origin)
    {
        window.location.origin = window.location.protocol + '//' + window.location.hostname + (window.location.port ? (':' + window.location.port) : '');
    }

    // 根目录地址
    var root_address = window.location.origin;

    var action_name = 'action';

    var _action_mode = window._action_mode;

    // 切换版本来使用不同的action
    if(_action_mode == 'action_beta')
    {
        action_name = 'action_beta';
    }
    else
    {
        action_name = action_ver;
    }

    var ajax_root = root_address + '/m/'+action_name+'/';

    var config =
    {
        ajax_url :
        {
            find : ajax_root + "find.php",
            hot : ajax_root + "index.php",
            search : ajax_root + "search.php",
            search_result : ajax_root + "search_result.php",
            ad_pic : ajax_root + "ad_pic.php",
            //hot : ajax_root + "index_t.php",
            model_card : ajax_root + "model_card.php",
            location_city : ajax_root + "hot_location.php",
            model_style : ajax_root + "model_style.php",
            pay : ajax_root + "pay.php",
            get_user_info : ajax_root + "get_user_info.php",
            act_list : ajax_root + "act_list.php",
            act_info : ajax_root + "act_info.php",
            pay_return_url : ajax_root + 'pay_return.php',
            get_club : ajax_root + 'get_club_list.php',
            add_act  : ajax_root + 'add_act.php',
            //login : 'https://ypays.yueus.com/action/login.php',
            login : ajax_root + 'login.php',
            update_user_info : ajax_root + 'update_user_info.php',
            get_act_ticket : ajax_root + 'get_act_ticket.php',
            get_act_joiners : ajax_root + 'get_act_joiners.php',
            join_act : ajax_root + 'join_act.php',
            follow_act : ajax_root + 'follow_act.php',
            consider_list : ajax_root + 'get_date_list.php',
            zone_info : ajax_root + 'get_zone_info.php',
            accept_invite : ajax_root + 'send_date_ret.php',
            status_list : ajax_root + 'get_my_act_list.php',
            withdraw : ajax_root + 'withdraw.php',
            bind_act : ajax_root + 'bind_act.php',
            reg : ajax_root + 'reg.php',
            follow_user : ajax_root + 'follow_user.php',
            get_model_card_base_info : ajax_root + 'model_card_base_info.php',
            save_model_card :  ajax_root + 'save_model_card.php',
            recharge :  ajax_root + 'pay_recharge.php',
            bill_act : ajax_root + 'bill_act.php',
            logout :  ajax_root + 'logout.php',
            fans_or_follow :  ajax_root + 'get_fans_or_follow.php',
            get_enroll_detail_info : ajax_root + 'get_enroll_detail_info.php',
            join_again_act : ajax_root + 'join_again_act.php',
            del_enroll_act : ajax_root + 'del_enroll_act.php',
            set_event_end_act : ajax_root + 'set_event_end_act.php',
            get_date_by_date_id : ajax_root + 'get_date_by_date_id.php',
            set_event_cancel_act : ajax_root + 'set_event_cancel_act.php',
            verify_code : ajax_root + 'verify_code.php',
            a_img : root_address+'/a_img.php',
            add_cameraman_comment : ajax_root + 'add_cameraman_comment_act.php',
            add_model_comment : ajax_root + 'add_model_comment_act.php',
            add_event_comment : ajax_root + 'add_event_comment_act.php',
            get_comment : ajax_root + 'get_comment.php',
            get_model_date_rank_list : ajax_root + 'get_model_date_rank_list.php',
            get_model_score_rank_list : ajax_root + 'get_model_score_rank_list.php',
            sso_login : ajax_root + 'sso_login.php',
            change_role : ajax_root + 'change_role.php',
            get_act_ticket_detail : ajax_root + 'get_act_ticket_detail.php',
            get_location_by_gps : ajax_root + 'get_location_by_gps.php',
            get_topic_list : ajax_root + 'get_topic_list.php',
            get_bail_available_balance : ajax_root + 'get_bail_available_balance.php',
            check_cameraman_require : ajax_root + 'check_cameraman_require.php',
            report_model : ajax_root + 'add_examine_report.php',
            submit_date_cancel_application : ajax_root + 'submit_date_cancel_application.php',
            force_refund : ajax_root + 'force_refund.php',
            update_agree_status : ajax_root + 'update_agree_status.php',
            bind_alipay : ajax_root + 'bind_alipay.php',
            level_list : ajax_root + 'level_list.php',
            level_detail : ajax_root + 'level_detail.php',
            add_id : ajax_root + 'add_id.php',
            get_topic_info : ajax_root + 'get_topic_info.php',
            get_scan_code : ajax_root + 'get_scan_code.php',
            test_wx_pay : ajax_root + '__get_pay_code.php',// 临时测试 测试完毕要删除 hudw 2014.12.2
            auth_act : ajax_root + 'auth_act.php',
            get_model_rank_list : ajax_root + 'get_model_rank_list.php',
            get_city_change : ajax_root + 'get_location_by_id.php',
            get_ip_location_by_cookie : ajax_root + 'get_ip_location_by_cookie.php',
            location_data_v2 : ajax_root + 'location_data_v2.php',
            person_order_info : ajax_root + 'person_order_info.php',
            send_person_order : ajax_root + 'send_person_order.php',
            wx_get_js_api_sign_package : ajax_root + 'wx_get_js_api_sign_package.php',
            get_intro_text : ajax_root + 'get_intro_text.php',
            pay_finish_text : ajax_root + 'pay_finish_text.php',
            tag_result : ajax_root + 'tag_result.php',
            send_share_coupon : ajax_root + 'send_share_coupon.php'

        },
        romain : root_address + '/m/app',
        debug_romain : root_address + '/m/appbeta',
        default_index_route : 'hot',
        _show_footer_hash :
        {
            'hot': 1,
            'find': 1,
            'mine': 1,
            'act/list': 1,
            'message' : 1
        },
        WeiXin_Version : 'default',
        fixed_header_pos_page : [],//["model_card"]
        no_share_pages :
            [
                '#mine',
                '#mine/consider',
                '#mine/consider_details_camera',
                '#act/security',
                '#mine/money/bill',
                '#comment/model',
                '#model_date/submit_application',
                '#model_date/submit_success',
                '#model_date/payment',
                '#account/fans_follows',
                '#edit_page'
            ] //隐藏右上角分享按钮的页面
    };

    return config;
});