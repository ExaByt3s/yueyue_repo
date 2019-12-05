/**
 * 封面 视图
 * hudw 2014.9.10
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var tpl = require('./tpl/main.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var global_config = require('../../common/global_config');
    var m_alert = require('../../ui/m_alert/view');
    var App = require('../../common/I_APP');


    var front_view = View.extend
    ({
        attrs:
        {
            template: tpl
        },
        events :
        {
            'swiperight' : function (){
                page_control.back();
            },

            'tap [data-role="select-role"]' : function(ev)
            {
                var self = this;

                if(confirm('选择身份后将无法更改，请再次确认。'))
                {
                    var role = $(ev.currentTarget).attr('data-role-val');

                    window._role = role;

                    var data = $.extend(self.params_data,{role : role,type:'reg_act'});

                    utility.user.reg(data);

                    //utility.storage.set('is_frist_time_open_app',1);


                }

            }

        },

        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {
            var self = this;

            // 这里表示验证码成功
            self.model
            .on('before:reg',function()
            {
                m_alert.show('账号正在创建中...','loading',{delay:-1});
            })
            .on('success:reg',function(response,options)
            {
                m_alert.hide();

                var response = response.result_data;

                var msg = response.msg;

                var code = response.code;

                if(code == 1)
                {
                    // 注册成功，接着要保存用户信息

                    m_alert.show(msg,'right');

                    var user_info = response.data;

                    utility.user.update_user(user_info);

                    utility.login_id = user_info.user_id;

                    // 保存用户信息到软件客户端

                    var params =
                    {
                        pocoid : utility.login_id,
                        username : utility.user.get('nickname'),
                        icon : utility.user.get('user_icon')
                    };

                    if(App.isPaiApp)
                    {
                        App.login(params);
                        // 保存设备信息
                        App.save_device();

                    }

                    // 根据角色进行跳转

                    if(window._role == 'model')
                    {
                        page_control.navigate_to_page('model_date/model_card/1');
                    }
                    else
                    {
                        page_control.navigate_to_page('mine/profile/1');
                    }

                }
                else
                {
                    m_alert.show(msg,'error');
                }

            })
            .on('error:reg',function(response,options)
            {
                m_alert.show('网络异常','error')
            })
        },

        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

            self.params_data = self.get('data');

            self._setup_events();

        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }

    });

    module.exports = front_view;
});