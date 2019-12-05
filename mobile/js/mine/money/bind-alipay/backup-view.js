/**
 * 绑定支付宝
 * 汤圆 2014.11.20       
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var bind_alipay = require('../bind-alipay/tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var templateHelpers = require('../../../common/template-helpers');
    var Scroll = require('../../../common/scroll');
    var global_config = require('../../../common/global_config');
    var number_btn_view = require('../../../widget/number_btn/view');
    var m_alert = require('../../../ui/m_alert/view');
    var no_bind = require('../bind-alipay/tpl/no-bind.handlebars');
    var has_bind = require('../bind-alipay/tpl/has-bind.handlebars');

    var bind_alipay_view = View.extend
    ({

        attrs:
        {
            template: bind_alipay
        },
        events :
        {
            'swiperight' : function ()
            {
                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role="btn"]' : function (ev)
            {

                var self = this;
                self.alipay_account_val = self.$('[data-role="alipay-account"]').val().trim();
                self.alipay_name_val = self.$('[data-role="alipay-name"]').val().trim();
                self.id_car_val = self.$('[data-role="id-car"]').val().trim();


                if( self.alipay_name_val == '' || !self.alipay_name_val )
                {
                    m_alert.show('请输入姓名！','error',{delay:1000});
                    return ;
                }

                if( self.alipay_account_val == '' || !self.alipay_account_val )
                {
                    m_alert.show('请输入支付宝账号！','error',{delay:1000});
                    return ;
                }
                
                if( !self.id_car_val )
                {
                    m_alert.show('请输入正确的身份证号码！','error',{delay:1000});
                    return ;
                }

                // 身份证验证
                self.validation_id(self.id_car_val);

                //收据收集
                var data = 
                {
                    third_account : self.alipay_account_val,
                    real_name : self.alipay_name_val,
                    id_car : self.id_car_val ,
                    type : 'bind_act'
                }
                console.log(data);
                // self.model.send_bind(data);
            }
        },

        //安装滚动条
        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,{
                is_hide_dropdown : true
            });
            self.view_scroll_obj = view_scroll_obj;
        },

        setup : function()
        {
            var self = this;
            self.$container = self.$('[data-role="container"]'); // 滚动容器
            self.$alipay_account = self.$('[data-role="alipay-account"]'); 
            self.$alipay_name = self.$('[data-role="alipay-name"]');
            self.$main_option = self.$('[data-role="main-option"]');
            self.setup_event();
            self._setup_scroll()

            //检查是否绑定
            self.model.check_bind({
                type : 'check_bind'
            });

            //刷新滚动条
            self.on('update_list',function(response,xhr)
            {

                // 区分当前对象
                var self = this;
                if(!self.view_scroll_obj)
                {
                    self._setup_scroll();
                }
                self.view_scroll_obj.refresh();
            })

        },

        //安装事件
        setup_event : function()
        {
            var self = this;

            //检查是否绑定
            self.model
                .on('before:check_bind:fetch',function(response,options)
                {
                    m_alert.show('加载中...','loading',{delay:1000});
                })
                .on('success:check_bind:fetch',function(response,options)
                {
                    var data = response.result_data;
                    self.render_html(data);
                })
                .on('error:check_bind:fetch',function()
                {
                    m_alert.show('网络不给力,请返回重试！','error')
                })
                .on('complete:check_bind:fetch',function(response,options)
                {
                    //m_alert.hide();
                })

            //发送绑定
            self.model
                .on('before:send_bind:fetch',function(response,options)
                {
                    m_alert.show('加载中...','loading',{delay:1000});
                })
                .on('success:send_bind:fetch',function(response,options)
                {
                    var data = response.result_data;
                    self.render_has_bind(data);
                })
                .on('error:send_bind:fetch',function()
                {
                    m_alert.show('网络不给力,请返回重试！','error')
                })
                .on('complete:send_bind:fetch',function(response,options)
                {
                    //m_alert.hide();
                })

        },
        render_html : function (data)
        {
            var self = this;
            var sate = data.code;
                switch (sate)
                {
                    case 0: 
                        self.$main_option.html(no_bind);
                        break;
                    case 1: 
                        //匹配支付宝星星转换
                        var str = data.data.third_account;
                        self.alipay_account_val = self.reg_alipay_account(str);
                        self.render_has_bind(data);
                        break;
            }
            //self.trigger('update_list');
        },
        render_has_bind : function(data)
        {
            var self = this;
            //支付宝星星匹配
            var ret = self.reg_alipay_account(self.alipay_account_val);
            var has_bind_html = has_bind({
                data : data,
                alipay_account : ret
            })
            self.$main_option.html(has_bind_html);;
        },

        //支付宝星星匹配
        reg_alipay_account : function (str) 
        {
            var self = this;
            var str = str.toString();
            var email = new RegExp(/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/);
            var email_test = email.test(str);
            if (email_test)
            {
                var ret = str.replace(/[\d\w]{1,4}@{1}/g, "****@");
                return ret;
            }
            else
            {
                var ret = str.replace(/[\d]{4}([\d]{4})$/g, "****$1");
                return ret;
            }
        },


        // 身份证验证 15 - 18 位
        validation_id : function(idCard) 
        {
            var self = this;
    
            var Wi = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1 ];    // 加权因子   
            var ValideCode = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ];            // 身份证验证位值.10代表X   

            // 执行
            IdCardValidate(idCard);

            function IdCardValidate(idCard) 
            { 
                idCard = trimId(idCard.replace(/ /g, ""));               //去掉字符串头尾空格                     
                if (idCard.length == 15) 
                {   

                    return isValidityBrithBy15IdCard(idCard);       //进行15位身份证的验证
                } 
                else if (idCard.length == 18) 
                {   
                    var a_idCard = idCard.split("");                // 得到身份证数组   
                    if( isValidityBrithBy18IdCard(idCard)&&isTrueValidateCodeBy18IdCard(a_idCard) )
                    {   
                    
                        //进行18位身份证的基本验证和第18位的验证
                        return true;  
                    }
                    else 
                    {   
                        m_alert.show('请输入正确的身份证号码！','error',{delay:1000});
                        return false;   
                    }   
                } 
                else 
                {   
                    m_alert.show('请输入正确的身份证号码！','error',{delay:1000});
                    return false;   
                }   
            }   
            /**  
             * 判断身份证号码为18位时最后的验证位是否正确  
             * @param a_idCard 身份证号码数组  
             * @return  
             */  
            function isTrueValidateCodeBy18IdCard(a_idCard) {   
                var sum = 0;                             // 声明加权求和变量   
                if (a_idCard[17].toLowerCase() == 'x') {   
                    a_idCard[17] = 10;                    // 将最后位为x的验证码替换为10方便后续操作   
                }   
                for ( var i = 0; i < 17; i++) {   
                    sum += Wi[i] * a_idCard[i];            // 加权求和   
                }   
                valCodePosition = sum % 11;                // 得到验证码所位置   
                if (a_idCard[17] == ValideCode[valCodePosition]) {   
                    return true;   
                } else {   
                    return false;   
                }   
            }   
            /**  
              * 验证18位数身份证号码中的生日是否是有效生日  
              * @param idCard 18位书身份证字符串  
              * @return  
              */  
            function isValidityBrithBy18IdCard(idCard18){   
                var year =  idCard18.substring(6,10);   
                var month = idCard18.substring(10,12);   
                var day = idCard18.substring(12,14);   
                var temp_date = new Date(year,parseFloat(month)-1,parseFloat(day));   
                // 这里用getFullYear()获取年份，避免千年虫问题   
                if(temp_date.getFullYear()!=parseFloat(year)   
                      ||temp_date.getMonth()!=parseFloat(month)-1   
                      ||temp_date.getDate()!=parseFloat(day)){   
                        return false;   
                }else{   
                    return true;   
                }   
            }   
              /**  
               * 验证15位数身份证号码中的生日是否是有效生日  
               * @param idCard15 15位书身份证字符串  
               * @return  
               */  
              function isValidityBrithBy15IdCard(idCard15){   
                  var year =  idCard15.substring(6,8);   
                  var month = idCard15.substring(8,10);   
                  var day = idCard15.substring(10,12);   
                  var temp_date = new Date(year,parseFloat(month)-1,parseFloat(day));   
                  // 对于老身份证中的你年龄则不需考虑千年虫问题而使用getYear()方法   
                  if(temp_date.getYear()!=parseFloat(year)   
                          ||temp_date.getMonth()!=parseFloat(month)-1   
                          ||temp_date.getDate()!=parseFloat(day)){   
                            return false;   
                    }else{   
                        return true;   
                    }   
              }   

            //去掉字符串头尾空格   
            function trimId(str) {   
                return str.replace(/(^\s*)|(\s*$)/g, "");   
            }
        },


        render : function()
        {
            var self = this;
            View.prototype.render.apply(self);
            self.trigger('render');
            return self;
        }

    });

    module.exports = bind_alipay_view;
});