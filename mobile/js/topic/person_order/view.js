/**
 * Created by hudw on 15-1-1.
 */
define(function(require,exports,module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var view = require('../../common/view');
    var scroll = require('../../common/scroll');
    var utility = require('../../common/utility');
    var m_alert = require('../../ui/m_alert/view');
    var templateHelpers = require('../../common/template-helpers');
    var tpl = require('./tpl/main.handlebars');
    var global_config = require('../../common/global_config');
    var m_select = require('../../ui/m_select/view');

    var form_view = view.extend
    ({
        attrs:
        {
            template:tpl
        },
        templateHelpers :
        {
            if_equal : templateHelpers.if_equal
        },
        forms :
        {
            select :{}
        },
        /**
         * 事件
         */
        events:
        {
            'tap [data-role="page-back"]' : '_back',
            'tap [data-role="cellphone"]' : function(ev)
            {
                var self = this;

                if(self.is_duplicate)
                {
                    return
                }

                var $cur_btn = $(ev.currentTarget);

                var $input = $cur_btn.find('input');

                var text = $input.val();

                var type = 'text';

                page_control.navigate_to_page('edit_page/'+type,
                    {
                        title:'手机号码',
                        input_type : 'tel',
                        text : text,
                        edit_obj:$input

                    });
            },
            'tap [data-role="style"]' : function(ev)
            {
                var self = this;

                if(self.is_duplicate)
                {
                    return
                }

                var select_style = self.forms['select']['style'];

                if(select_style)
                {
                    select_style.show();
                }
            },
            'tap [data-role="address"]' : function(ev)
            {
                var self = this;

                if(self.is_duplicate)
                {
                    return
                }

                var select_address = self.forms['select']['province'];

                if(select_address)
                {
                    select_address.show();
                }
            },
            'tap [data-role="time"]' : function()
            {
                var self = this;

                if(self.is_duplicate)
                {
                    return
                }

                var select_time = self.forms['select']['time'];

                if(select_time)
                {
                    select_time.show();
                }
            },
            'tap [data-role="hour"]' : function()
            {
                var self = this;

                if(self.is_duplicate)
                {
                    return
                }

                var select_hour = self.forms['select']['hour'];

                if(select_hour)
                {
                    select_hour.show();
                }
            },
            'tap [data-role="model-num"]' : function(ev)
            {
                var self = this;

                if(self.is_duplicate)
                {
                    return
                }

                var select_model_num = self.forms['select']['model_num'];

                if(select_model_num)
                {
                    select_model_num.show();
                }
            },
            'tap [data-role="money"]' : function(ev)
            {
                var self = this;

                if(self.is_duplicate)
                {
                    return
                }

                var $cur_btn = $(ev.currentTarget);

                var $input = $cur_btn.find('input');

                var text = $input.val();

                var type = 'text';

                page_control.navigate_to_page('edit_page/'+type,
                    {
                        title:'报酬',
                        input_type : 'tel',
                        text : text,
                        edit_obj:$input
                    });
            },
            'tap [data-role="face"]' : function(ev)
            {
                var self = this;

                if(self.is_duplicate)
                {
                    return
                }

                var $cur_btn = $(ev.currentTarget);

                var $input = $cur_btn.find('textarea');

                var text = $input.val();

                var type = 'textarea';

                page_control.navigate_to_page('edit_page/'+type,
                    {
                        title:'外貌要求',
                        text : text,
                        is_empty : true,
                        edit_obj:$input,
                        word_limit : 140
                    });
            },
            'tap [data-role="submit"]' : function()
            {
                var self = this;

                if(self.is_duplicate)
                {
                    return
                }

                self._submit();
            }
        },

        _back: function()
        {
            page_control.back();
        },

        setup: function()
        {
            var self = this;

            self.$cellphone = self.$('[data-role="cellphone"]').find('input');
            self.$address = self.$('[data-role="address"]').find('input');
            self.$style = self.$('[data-role="style"]').find('input');
            self.$time = self.$('[data-role="time"]').find('input');
            self.$hour = self.$('[data-role="hour"]').find('input');
            self.$model_num = self.$('[data-role="model-num"]').find('input');
            self.$money = self.$('[data-role="money"]').find('input');
            self.$face = self.$('[data-role="face"]').find('textarea');

            if(utility.auth.is_login() && utility.user.get('cellphone'))
            {
                self.$cellphone.val(utility.user.get('cellphone'));
            }

            self._init_order_data();
        },

        /**
         * 渲染模板
         */
        render: function()
        {
            var self = this;

            view.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        /**
         * 安装下拉模特数量
         * @private
         */
        _setup_select_model_num : function(data)
        {
            var self = this;

            self.forms['select']['model_num'] = new m_select
            ({
                templateModel :
                {
                    options : [data]

                },
                parentNode: self.$el
            }).render();

            // 开始时间确认
            self.forms['select']['model_num'].on('confirm:success',function(arr)
            {
                self.$model_num.val(arr[0].value);
            });
        },
        /**
         * 安装下拉时长
         * @private
         */
        _setup_select_hour : function(data)
        {
            var self = this;

            self.forms['select']['hour'] = new m_select
            ({
                templateModel :
                {
                    options : [data]

                },
                parentNode: self.$el
            }).render();

            // 开始时间确认
            self.forms['select']['hour'].on('confirm:success',function(arr)
            {
                self.$hour.val(arr[0].text);
            });
        },
        /**
         * 安装下拉选择时间
         * @private
         */
        _setup_select_time : function()
        {
            var self = this;

            var date_arr = utility.select_time.mix_date().date_arr;
            var hour_arr = utility.select_time.mix_date().hour_arr;
            var min_arr = utility.select_time.mix_date().min_arr;


            self.forms['select']['time'] = new m_select
            ({
                templateModel :
                {
                    options : [date_arr,hour_arr,min_arr]

                },
                parentNode: self.$el
            }).render();


            // 开始时间确认
            self.forms['select']['time'].on('confirm:success',function(arr)
            {
                var cur_year = utility.select_time.today_is().year;

                self.$time.val(arr[0].value+" "+arr[1].value+":"+arr[2].value);
            });
        },
        /**
         * 安装地区下拉
         * @private
         */
        _setup_select_location : function(location_data)
        {
            var self = this;

            var province = location_data.two_lv_data.province;
            var city = location_data.two_lv_data.city;

            // 设置默认市
            var city_arr = city[province[0].id];

            // 设置省第一项默认选中
            province[0].selected = true;
            city_arr[0].selected = true;

            // 级联查询必须有“不限”的数据组
            self.forms['select']['province'] = new m_select
            ({
                templateModel :
                {
                    options :
                        [
                            province,
                            city_arr
                        ]
                },
                parentNode: self.$el
            }).render();


            self.forms['select']['province'].on('change:options',function(arr,cur_scroll_obj)
            {

                if(cur_scroll_obj.index == 0)
                {
                    var key = arr[0].id;

                    var options = city[key];

                    console.log(options)

                    if(options)
                    {
                        options[0].selected = true;

                        self.forms['select']['province'].set_options([options],1);
                    }


                }

            });

            // 地址确认
            self.forms['select']['province'].on('confirm:success',function(arr)
            {
                if(utility.is_empty(arr[0].value))
                {
                    m_alert.show('请选择省份','right');

                    return;
                }

                self.$address.attr('data-city-id',arr[1].id).val(arr[0].value+"-"+arr[1].value);

            });
        },
        /**
         * 安装风格下拉
         * @private
         */
        _setup_select_style : function(data)
        {
            var self = this;

            data[0].selected = true;

            self.forms['select']['style'] = new m_select
            ({
                templateModel :
                {
                    options : [data]
                },
                parentNode: self.$el
            }).render();

            // 地址确认
            self.forms['select']['style'].on('confirm:success',function(arr)
            {
                if(utility.is_empty(arr[0].text))
                {
                    m_alert.show('请选择风格','right');

                    return;
                }

                self.$style.val(arr[0].text);

            });
        },
        /**
         * 安装私人定制表单内容模块
         * @private
         */
        _init_order_data : function()
        {

            var self = this;

            // 本地已经有地区就不再请求
            self.client_has_location = 0;

            if(utility.storage.get('location_data_v2'))
            {
                self._setup_select_location(utility.storage.get('location_data_v2'));

                self.client_has_location = 1;
            }

            utility.ajax_request
            ({
                url : global_config.ajax_url.person_order_info,
                cache : true,
                data :
                {
                    phone : utility.user.get('phone'),
                    client_has_location : self.client_has_location
                },
                beforeSend : function()
                {
                    //m_alert.show('加载中...','loading',{delay:-1});
                },
                success : function(ret)
                {
                    m_alert.hide();

                    var data = ret.result_data;

                    // 填充地区数据
                    if(data.two_lv_data)
                    {
                        var location_data_v2 =
                        {
                            two_lv_data :
                            {
                                province:JSON.parse(data.two_lv_data.province),
                                city : eval("(" + data.two_lv_data.city + ")")
                            }
                        };

                        utility.storage.set('location_data_v2',location_data_v2);

                        self._setup_select_location(location_data_v2);
                    }

                    // 填充风格
                    if(data.model_style)
                    {
                        self._setup_select_style(data.model_style);
                    }

                    // 填充时长
                    if(data.model_hour)
                    {
                        self._setup_select_hour(data.model_hour);
                    }

                    // 填充模特数
                    if(data.model_hour)
                    {
                        self._setup_select_model_num(data.model_num);
                    }

                    // 填充时间
                    self._setup_select_time();

                    console.log(data);

                    if(data.is_duplicate)
                    {
                        self.is_duplicate = data.is_duplicate;

                        self.$('[data-role="submit"]').addClass("fn-hide");
                        self.$('[data-role="submit_after_txt"]').removeClass('fn-hide');

                        self._insert_data(data.data)
                    }
                },
                error : function()
                {
                    m_alert.show('网络异常','error');

                    setTimeout(function()
                    {
                        page_control.back();
                    },2400);
                }
            })
        },
        _insert_data : function(data)
        {
            var self = this;

            if(data.style){self.$style.val(data.style);}
            if(data.city_name){self.$address.val(data.city_name)}
            if(data.date_time){self.$time.val(data.date_time)}
            if(data.hour){self.$hour.val(data.hour)}
            if(data.model_num){self.$model_num.val(data.model_num)}
            if(data.budget){self.$money.val(data.budget)}
            if(data.looks_require){self.$face.val(data.looks_require)}


        },
        /**
         * 提交私人订制
         * @private
         */
        _submit : function()
        {
            var self = this;

            if(self.submiting)
            {
                return;
            }

            var data =
            {
                cellphone : self.$cellphone.val(),
                location_id : self.$address.attr('data-city-id'),
                style : self.$style.val(),
                date_time : self.$time.val(),
                hour : self.$hour.val(),
                model_num : self.$model_num.val(),
                budget : self.$money.val(),
                looks_require : self.$face.val()
            };


            var error_msg = '';

            if(utility.is_empty(data['location_id']))
            {
                error_msg = '地址不能为空';
            }
            else if(utility.is_empty(data['cellphone']))
            {
                error_msg = '手机号码不能为空';
            }
            else if(utility.is_empty(data['style']))
            {
                error_msg = '主题风格不能为空';
            }
            else if(utility.is_empty(data['date_time']))
            {
                error_msg = '拍摄时间不能为空';
            }
            else if(utility.is_empty(data['hour']))
            {
                error_msg = '拍摄时长不能为空';
            }
            else if(utility.is_empty(data['model_num']))
            {
                error_msg = '模特数量不能为空';
            }
            else if(utility.is_empty(data['budget']))
            {
                error_msg = '报酬不能为空';
            }

            if(error_msg)
            {
                m_alert.show(error_msg,'error');

                return;
            }

            utility.ajax_request
            ({
                url : global_config.ajax_url.send_person_order,
                cache : false,
                data : data,
                type : 'POST',
                beforeSend : function()
                {
                    self.submiting = true;

                    m_alert.show('加载中...','loading',{delay:-1});
                },
                success : function(res)
                {
                    self.submiting = false;

                    m_alert.hide();

                    var res = res.result_data;

                    if(res.code >0)
                    {
                        m_alert.show(res.msg,'right');

                        setTimeout(function()
                        {
                            page_control.back();
                        },2400);

                    }
                    else
                    {
                        m_alert.show(res.msg,'error');
                    }

                    console.log(res);
                },
                error : function()
                {
                    self.submiting = false;

                    m_alert.show('网络异常','error');
                }
            });
        }

    });

    module.exports = form_view;
});