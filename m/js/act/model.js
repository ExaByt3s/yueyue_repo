/**
 * Created by nolest on 2014/8/30.
 *
 * 活动列表模型
 *
 */
define(function(require, exports, module)
{
    var Backbone = require('backbone');
    var global_config = require('../common/global_config');

    module.exports = Backbone.Model.extend
    ({
        default :
        {
                _seq_id_ : '',
                active_time : '',
                address : '',
                budget : '',
                category : '',
                club : '',
                content: '',
                cover_image : '',
                end_time: '',
                event_id: '',
                event_review : '',
                hit_count : '',
                is_authority : '',
                is_recommend : '',
                is_top : '',
                join_count : '',
                last_update_time : '',
                limit_num : '',
                location_id : '',
                review_time : '',
                score : '',
                start_time : '',
                status : '',
                title : '',
                type_icon : '',
                user_id : '',
                username : ''
        },
        _setup_events : function ()
        {

        },
        initialize : function()
        {
            var self = this;

            self._setup_events();
        },
        join_act : function(data) {
            var self = this;

            //var redirect_url = location.origin+"/mobile/"+window._page_mode+"#act/apply/"+data.event_id;

            var redirect_url = location.origin+"/mobile/"+window._page_mode+"#act/pay_success/"+data.event_id;

            data = $.extend(data,
            {
                redirect_url : redirect_url
            });

            self.fetch({
                url: global_config.ajax_url.join_act,
                cache: false,
                data:data,
                beforeSend: function(xhr, options) {
                    self.trigger('before:join_act:fetch', xhr, options);
                },
                success: function(model, response, options) {
                    self.trigger('success:join_act:fetch', response, options);
                },
                error: function(model, xhr, options) {
                    self.trigger('error:join_act:fetch', model, xhr, options);
                },
                complete: function(xhr, status) {
                    self.trigger('complete:join_act:fetch', xhr, status);
                }
            });

            return self;
        },
    /**
     * 获得广告图数据
     * @param data
     */
    get_ad_pic : function(data){
        var self = this;

        self.fetch({
            url : global_config.ajax_url.ad_pic,
            data : data,
            cache : false,
            beforeSend: function() {
                self.trigger('before:ad_pic:fetch');
            },
            success: function(model, response, options) {
                self.trigger('success:ad_pic:fetch', response, options);
            },
            error: function(model, response, options) {
                self.trigger('error:ad_pic:fetch', response, options);
            },
            complete: function(xhr, status) {
                self.trigger('complete:ad_pic:fetch', xhr, status);
            }
        });
    }
    });
});