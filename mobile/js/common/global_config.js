/**
 * 全局参数设置
 */
define(function(require, exports)
{
    var action_ver = 'action2.2.0';

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
        action_name = action_ver;
    }
    else
    {
        action_name = action_ver;
    }

    var ajax_root = root_address + '/mobile/'+action_name+'/';

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
			location_data_v2 : ajax_root + 'location_data_v2.php',
            person_order_info : ajax_root + 'person_order_info.php',
            send_person_order : ajax_root + 'send_person_order.php',
            send_share_coupon : ajax_root + 'send_share_coupon.php',
            get_intro_text : ajax_root + 'get_intro_text.php',
            get_user_coupon_list_by_tab : ajax_root + 'get_user_coupon_list_by_tab.php',
            get_single_coupon : ajax_root + 'get_single_coupon.php',
            get_supply_detail : ajax_root + 'get_supply_detail.php',
            give_supply_coupon : ajax_root + 'give_supply_coupon.php',
            get_user_coupon_list_by_check : ajax_root + 'get_user_coupon_list_by_check.php',
            give_coupon : ajax_root + 'give_coupon.php',
            ready_pay : ajax_root + 'ready_pay.php',
            pay_finish_text : ajax_root + 'pay_finish_text.php',

            demand_text : ajax_root + 'get_demand_text.php',
            add_requirement_act : ajax_root + 'add_requirement_act.php',
            requirement_cameraman_list : ajax_root + 'requirement_cameraman_list.php',
            requirement_detail : ajax_root + 'requirement_detail.php',
            sign_requirement_act : ajax_root + 'sign_requirement_act.php',
            requirement_model_list : ajax_root + 'requirement_model_list.php',
            auth_get_user_info : ajax_root + 'auth_get_user_info.php'


        },
        romain : root_address + '/mobile/app',
        debug_romain : root_address + '/mobile/appbeta',
        default_index_route : 'init',
        _show_footer_hash :
        {
            'hot': 1,
            'find': 1,
            'mine': 1,
            'act/list': 1,
            'message' : 1
        },
        share_url : 'http://www.yueus.com/',//客户端分享前缀
        fixed_header_pos_page : [],//["model_card"]
        analysis_page :
        {
            //模特卡页面
            'model_card' :
            {
                pid : '1220025',
                mid : '122RO01001'
            },
            //摄影师卡页面
            'cameraman_card' :
            {
                pid : '1220026',
                mid : '122RO02001'
            },
            //约拍订单列表
            'date_invite_list':
            {
                pid : '1220035',
                mid : '122LT03001'
            },
            //约拍详情页
            'date_info':
            {
                pid : '1220036',
                mid : '122RO06001'
            },
            //我要约拍
            'model_style' :
            {
                pid : '1220028',
                mid : '122OD02001'
            },
            //提交约拍申请
            'submit_application' :
            {
                pid : '1220031',
                mid : '122OD02002'
            },
            //支付
            'date_payment':
            {
                pid : '1220032',
                mid : '122OD02003'
            },
            //提交成功
            'date_submit_success' :
            {
                pid : '1220034',
                mid : '122OD02004'
            },
            //取消约拍
            'date_cancel':
            {
                pid : '1220038',
                mid : '122OD02005'
            },
            //专题列表
            'topic_list':
            {
                pid : '1220047',
                mid : '122LT01005'
            },
            //专题内容
            'topic_info':
            {
                pid : '1220048',
                mid : '122TP01001'
            },
            //数字签到
            'checkins' :
            {
                pid : '1220053',
                mid : '122OT02002'
            },
            //认证列表
            'authentication_list':
            {
                pid : '1220072',
                mid : '122LT08004'
            },
            //活动详情页
            'act_details':
            {
                pid : '1220049',
                mid : '122RO05001'
            }
        },
        analysis_event :
        {
            // 模特关注
            'model_follow':
            {
                id : '1220027'
            },
            // 模特约拍
            'model_date':
            {
                id : '1220029'
            },
            // 模特私聊
            'model_chat':
            {
                id : '1220030'
            },
            // 摄影师私聊
            'cameraman_chat':
            {
                id : '1220040'
            },
            // 摄影师关注
            'cameraman_follow':
            {
                id : '1220039'
            },
            // 约拍支付
            'date_pay':
            {
                id : '1220033'
            },
            //  取消理由
            'date_cancel':
            {
                id : '1220037'
            }
        }
    };

    return config;
});