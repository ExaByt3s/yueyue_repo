define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var global_config = require('../../common/global_config');
    var view = require('../../common/view');
    var app = require('../../common/I_APP');
    var scroll = require('../../common/scroll');
    var utility = require('../../common/utility');
    var App = require('../../common/I_APP');
    var m_alert = require('../../ui/m_alert/view');
    var pay_items =require('../../widget/pay_item/view');


    var main_tpl = require('./tpl/main.handlebars');
    var item_tpl = require('./tpl/item.handlebars');
    var photo_success_tpl = require('./tpl/photo_success.handlebars');
    var money_success_tpl = require('./tpl/money_success.handlebars');
    var submit_success_tpl = require('./tpl/submit_success.handlebars');
    var ua = require('../../frame/ua')

    var authentication_list_view = view.extend({
        attrs:{
            template:main_tpl
        },
        events:{
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role=take-photo]' : function (ev)
            {
                var self = this;
                self.$photo = self.$('[data-role=photo]');

                if(app.isPaiApp){
                    app.upload_img
                    ('multi_img',{
                        is_async_upload : 0,
                        max_selection : 1,
                        is_zip : 1

                    },function(data)
                    {
                        console.log(data.imgs);
                        var img = data.imgs[0].url;
                        $('[data-role="reverse"]').children('[data-role="btn-con"]').remove();
                        var photo_success_html = photo_success_tpl({
                            img : img,
                            img_width : self._get_img_w_h().width,
                            is_v2 : self.options.is_v2,
                            from_date : self.options.from_date
                        });

                        self.$photo.html(photo_success_html);
                        $('[data-role="btn-con"]').appendTo('[data-role="reverse"]').removeClass('fn-hide');
                        //self.model.add_authentication_pic({img : img})
                        self.view_scroll_obj.resetLazyLoad();
                        self.view_scroll_obj.refresh();
                        self.view_scroll_obj.reset_top();
                        self.options.upload = 0;

                    });
                }else{
                    $('[data-role="reverse"]').children('[data-role="btn-con"]').remove();
                    var photo_success_html = photo_success_tpl({
                     img_width : self._get_img_w_h().width,
                     is_v2 : self.options.is_v2,
                     img:'http://image16-c.poco.cn/mypoco/myphoto/20141122/11/4198122820141122112239087_640.jpg?1024x646_120',
                     from_date:self.options.from_date
                     });
                     self.$photo.html(photo_success_html);
                    $('[data-role="btn-con"]').appendTo('[data-role="reverse"]').removeClass('fn-hide');
                     self.view_scroll_obj.resetLazyLoad();
                     self.view_scroll_obj.refresh();
                     self.view_scroll_obj.reset_top();
                }

            },
            /**
             * 充值支付宝事件
             * @param ev
             */
            'tap [data-role=recharge]' : function (ev)
            {
                var self = this;

                //v3认证，未完成v2认证时的提示
                if(!self.options.is_v2 && self.options.upload){
                    m_alert.show('亲，请先完成【V2：实名认证】，再充值哦~','error',{delay:1000});
                    return;
                }

                if(self.options.yp_url)
                {
                    var url = decodeURIComponent(self.options.yp_url);

                    var idx = url.indexOf('#');

                    var url_hash = url.substr(idx+1,url.length-1);
                }
                else
                {
                    var url_hash = 'mine';
                }

                var data =
                {
                    type:'recharge',
                    amount : 300,
                    third_code : self.pay_item_obj.model.get('pay_type'),
                    redirect : url_hash,
                    is_refresh : true
                };


                utility.user.send_recharge(data);
            },
            /**
             * 返回约拍页面
             */
            'tap [data-role="back-to-date"]' : function()
            {
                var self = this;

                self.options.yp_url && (window.location.href = decodeURIComponent(self.options.yp_url));


            },
            /**
             * 聊天触发事件
             * @param ev
             */
            'tap [data-role=chat]' : function (ev)
            {
                var params = utility.get_url_params();

                if(App.isPaiApp)
                {
                    utility.ajax_request
                    ({
                        url : global_config.ajax_url.model_card,
                        data :{
                            user_id : params && params.model_id
                        },
                        beforeSend : function()
                        {
                            m_alert.show('发送中...','loading',{delay:-1});
                        },
                        success : function(res)
                        {
                            m_alert.hide();

                            var obj = res.result_data.data;

                            var data =
                                {
                                    senderid : utility.login_id,
                                    receiverid : params.model_id,
                                    sendername : utility.user.get('nickname'),
                                    receivername : obj.user_name,
                                    sendericon : obj.user_icon,
                                    receivericon : obj.user_icon
                                };

                            console.log(data);

                            if(!App.isPaiApp)
                            {
                                console.warn('no App');

                                return;
                            }

                            App.chat(data);
                        },
                        error : function()
                        {
                            m_alert.show('网络异常','error');
                        }
                    });


                }

            },
            'tap [data-role=have-a-see]' : function (ev)
            {
                if(App.isPaiApp)
                {
                    App.switchtopage({page:'hot'});
                }
                else
                {
                    page_control.navigate_to_page('hot');
                }
            },
            'tap [data-role="submit"]' : function()
            {
                var self = this;

                self.upload_img = self.$('[data-role="images"]').attr("src");

                console.log("提交审核");

                if(window.confirm("确认提交？"))
                {
                    if(self.$('[data-role="id"]').val().trim() == "" || self.$('[data-role="name"]').val().trim() == "")
                    {
                        m_alert.show('请正确填写个人信息','error');
                    }
                    else
                    {
                        self.model.add_authentication_pic({img : self.upload_img,id_code:self.$('[data-role="id"]').val(),name:self.$('[data-role="name"]').val()})
                    }
                }
            },
            'tap [data-role="re_take_photo"]' : function()
            {
                var self = this;

                page_control.back();
            }

        },

        _setup_events:function(){
            var self = this;

            // 模型事件
            // --------------------
            self.model
                .on('before:authentication_detail:fetch', self._authentication_detail_before, self)
                .on('success:authentication_detail:fetch', self._authentication_detail_success, self)
                .on('error:authentication_detail:fetch', self._authentication_detail_error, self)
                .on('complete:authentication_detail:fetch', self._authentication_detail_complete, self)

                .on('before:authentication_pic:fetch',function(response,options)
                {

                })
                .on('success:authentication_pic:fetch',function(response,options)
                {

                    m_alert.show(response.result_data.msg,'error');

                    $('[data-role="reverse"]').children('[data-role="btn-con"]').remove();
                    var submit_success_str = submit_success_tpl({
                        is_v2 : self.inner_authentications.is_v2,
                        authentications: self.inner_authentications
                    });
                    self.$photo.html(submit_success_str);
                    $('[data-role="btn-con"]').appendTo('[data-role="reverse"]').removeClass('fn-hide');
                    //page_control.navigate_to_page("mine/level_2/status");
                })
                .on('error:authentication_pic:fetch',function(response,options)
                {
                    m_alert.show('请求失败，请重试','error');
                })
                .on('complete:authentication_pic:fetch',function(response,options)
                {

                    //self._drop_reset();
                })




            self.on('render',function(){
                self.model.get_level_detail();
            });

            if(utility.user)
            {
                utility.user.off('before:send_rechare:fetch');
                utility.user.off('success:send_rechare:fetch');
                utility.user.off('error:send_rechare:fetch');
            }

            utility.user.on('before:send_rechare:fetch',function()
            {
                m_alert.show('加载中...','loading');
            })
            .on('success:send_rechare:fetch',function(response,options)
            {
                //m_alert.hide();

                var response = response.result_data;
                var code = response.code;
                var msg = response.msg;
                var data = response.data;
                var channel_return = response.channel_return;
                var third_code = response.third_code;
                var payment_no = response && response.payment_no;


                if( code == 1)
                {
                    m_alert.show('第三方支付跳转','right',{delay:1000});

                    if(third_code == 'alipay_purse')
                    {
                        // 支付宝
                        app.alipay
                        ({
                            alipayparams : data,
                            payment_no : payment_no
                        },function(res)
                        {
                            var result = utility.int(res.result);

                            self.after_pay_text(result);

                            if(result == 1 || result == -1 || result == -2)
                            {
                                window.location.href = channel_return;
                            }
                        });
                    }
                    else if(third_code == 'tenpay_wxapp')
                    {
                        // 微信支付

                        app.wxpay(JSON.parse(data),function(res)
                        {
                            var result = utility.int(res.result);

                            self.after_pay_text(result);

                            if(result == 1 || result == -1 || result == -2)
                            {
                                window.location.href = channel_return;
                            }
                        });
                    }


                }
                else
                {
                    m_alert.show(msg,'error',{delay:1000});

                }


            })
            .on('error:send_rechare:fetch',function()
            {
                m_alert.hide();
            })

        },

        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = scroll(self.$container,
                {
                    is_hide_dropdown : true,
                    lazyLoad : true
                });

            self.view_scroll_obj = view_scroll_obj;

            //self.view_scroll_obj.refresh();
        },
        /**
         * 安装第三方支付选择模块
         * hudw 2014.12.9
         * @private
         */
        _setup_pay_items : function()
        {
            var self = this;
            // 支付选项参数模型
            var pay_items_model =
            {
                can_use_balance : false,
                total_price : 300,
                show_price_info : false,
                is_support_outtime : true,
                is_support_now_out : true
            };

            self.$pay_items_container = self.$('[data-role="pay-item-container"]');

            self.pay_item_obj = new pay_items
            ({
                templateModel : pay_items_model,
                parentNode : self.$pay_items_container
            }).render();

            self.pay_item_obj.show_other_pay_items();

            self.pay_item_obj._select_pay_type('alipay_purse');

            self.$('.pay-details-payment-title').addClass('fn-hide');
        },
        setup : function()
        {

            var self = this;

            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$scoll_wrapper = self.$('[data-role=scoll-wrapper]');


            // 安装事件
            self._setup_events();

            console.log(ua);
            console.log(ua.ios_version == '7.0');



            //self.view_scroll_obj.refresh();


        },

        render : function()
        {
            var self = this;

            self._visible = true;

            view.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        refresh : function()
        {
            var self = this;

            self.$scoll_wrapper.html('');

            self.model.get_level_detail();

            return self;

        },

        _render_item: function(authentications) {
            var self = this;

            var params = utility.get_url_params();

            $.extend(authentications,{from_chat : params && params.from_chat_v2});

            authentications.is_v2 = self.options.is_v2;
            authentications.from_date = self.options.from_date;
            authentications.from_date_success = self.options.from_date_success;
            self.options.upload = authentications.upload;
            authentications.img_width = self._get_img_w_h().width;
            self.inner_authentications = authentications;

            if(authentications.name)
            {
                //名字不为空
                var _a = authentications.name;
                var _b = a.toString();
                var _head;
                var _tail;
                if(_b.length> 2)
                {
                    head = b.substring(0,1);
                    tail = b.substring(b.length-1, b.length);
                    var _starts = "";
                    for(var k=0;k+2 <b.length;k++)
                    {
                        starts = starts + "*"
                    }
                    authentications.name = head + starts + tail;
                }
                else
                {
                    _head = b.substring(0,1);
                    authentications.name = _head + "*";
                }

            }
            if(authentications.id_code)
            {
                //身份证号不为空
                var a = authentications.id_code;
                var b = a.toString();
                var head = b.substring(0,2);
                var tail = b.substring(b.length-4, b.length);
                var starts = "";
                for(var i=0;i+6 <b.length;i++)
                {
                    starts = starts + "*"
                }
                authentications.id_code = head + starts + tail;
            }
            var html_str = item_tpl({
                is_v2 : self.options.is_v2,
                authentications: authentications
            });

            self.$scoll_wrapper.html(html_str);

            if(!self.pay_item_obj)
            {
                // 安装第三方支付
                self._setup_pay_items();
            }


            if(!self.view_scroll_obj)
            {
                self._setup_scroll();
            }

            $('[data-role="btn-con"]').appendTo('[data-role="reverse"]').removeClass('fn-hide');
            self.view_scroll_obj.refresh();

            /*self.view_scroll_obj.reset_top();
            self.view_scroll_obj.refresh();*/
        },

        _get_img_w_h : function(){
            var self = this;
            var w_h ={};
            w_h.width = utility.get_view_port_width() - 30;
            w_h.height = 580 / 400 * w_h.width;
            return w_h;
        },

        set_options : function(options){
            var self = this;
            self.options = options;
            return self;
        },


        _authentication_detail_before:function(){
            m_alert.show('加载中...','loading',{
             delay:-1
             });
        },

        _authentication_detail_success:function(response, xhrOptions){
            var self = this;

            var authentications = response.result_data.list;

            console.log(response.result_data);
            self._render_item(authentications);

        },

        _authentication_detail_error:function(){

        },

        _authentication_detail_complete:function(){
            m_alert.hide();
        },
        /**
         * 显示支付结束后的文案
         * @param code
         */
        after_pay_text : function(code)
        {
            switch (utility.int(code))
            {
                case 1:
                case -2:
                case -1:
                    m_alert.show('支付成功','right');
                    break;
                case 0:
                    m_alert.show('其它错误','error');
                    break;
                case -3:
                    m_alert.show('支付失败','error');
                    break;
                case -4:
                    m_alert.show('支付取消','error');
                    break;
            }
        }

    })

    module.exports = authentication_list_view;
});