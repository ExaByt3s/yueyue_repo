/**
 * Created by nolest on 2014/9/1.
 *
 * 活动介绍模型
 */
define(function(require, exports, module)
{
    var global_config = require('../../common/global_config');
    var Backbone = require('backbone');



    module.exports = Backbone.Model.extend
    ({
        url : global_config.ajax_url.act_info,
        defaults :
        {
            act_intro:'',
            act_info:'',
            act_arrange:'',
            pub_user_id:'',
            event_id:'',
            is_follow: false
        },
        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

        },
        parse : function(response)
        {

            if(response.result_data.data)
            {
                return response.result_data.data

            }

            return response;
        },
        initialize : function()
        {
            var self = this;

            self._setup_events();
        },
        get_list : function(is_show_table_num)
        {
            var self = this;

            self.fetch
            ({
                url :self.url,
                data:
                {
                    event_id : self.get('event_id'),
                    is_show_table_num : is_show_table_num || false
                },
                cache : false,
                reset : true,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:fetch',xhr,options);
                },
                success : function(collection, response,options)
                {
                    self.trigger('success:fetch',response,options);
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:fetch',xhr,status);
                }
            });

            return self
        },
        submit_obj : function(data)
        {
            var self = this;

            self.fetch
            ({

                url : global_config.ajax_url.add_act,
                data : data,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:submit',xhr,options);
                },
                success : function(collection, response, options)
                {
                    self.trigger('success:submit',response,options);
                },
                error : function(collection, response, options)
                {
                    self.trigger('error:submit',response,options)
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:submit',xhr,status);
                }
            });

            return self

        },

        relation: function(relationAct) {
            var self = this;

            console.log(relationAct)
            self.save({
                is_follow: relationAct
            }, {
                wait: true,
                url: global_config.ajax_url.follow_act,
                data:'event_id=' + self.get('event_id') + '&add_follow=' + relationAct,
                beforeSend: function(xhr, options) {
                    self.trigger('before:relation', xhr, options);
                },
                success: function(model, response, options) {
                    self.trigger('success:relation', model, response, options);
                },
                error: function() {
                    //self.trigger('error:relation');
                },
                complete:function(){
                    //self.trigger('complete:relation');
                }
            });
        },
        get_enroll_info : function(params)
        {
            var self = this;

            self.fetch
            ({
                url :global_config.ajax_url.get_enroll_detail_info,
                data:params,
                reset : true,
                cache : false,
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:get_enroll_info:fetch',xhr,options);
                },
                success : function(collection, response,options)
                {
                    self.trigger('success:get_enroll_info:fetch',response,options);
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:get_enroll_info:fetch',xhr,status);
                }
            });

            return self
        },
        /**
         * 支付活动
         * @param data
         * @returns {exports}
         */
        join_again_act : function(data)
        {
            var self = this;

            var location = window.location;

            //2014.12.31 hudw
            //支付活动后跳转的链接修改成到活动最终页
            //var redirect_url = location.origin+"/mobile/"+window._page_mode+"#act/signin/"+data.event_id;

            var redirect_url = location.origin+"/m/"+window._page_mode+location.search+"#act/pay_success/"+data.event_id;

            data = $.extend(data,{redirect_url : redirect_url});

            self.fetch
            ({
                url :global_config.ajax_url.join_again_act,
                data: data,
                cache : false,
                type : 'POST',
                beforeSend : function(xhr,options)
                {
                    self.trigger('before:pay:fetch',xhr,options);
                },
                success : function(collection, response,options)
                {
                    self.trigger('success:pay:fetch',response,options);
                },
                complete : function(xhr,status)
                {
                    self.trigger('complete:pay:fetch',xhr,status);
                }
            });

            return self
        }
    });
});
