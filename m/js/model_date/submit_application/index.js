/**
 * 提交约拍申请 - index
 * nolestLam 2014.8.25
 * @param  {[type]} require
 * @param  {[type]} exports
 * @param  {[type]} module
 * @return {[type]}
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var submit_application_view = require('../submit_application/view');
    var model = require('../submit_application/model');
    var agreement_view = require('../agreement/view');
    var utility = require('../../common/utility');
    var model_card = require('../model_card/model');
    var m_alert = require('../../ui/m_alert/view');

    page_control.add_page([ function() {
        return {
            title: "提交约拍申请",
            route: {
                "model_date/submit_application(/:custom_params)": "model_date/submit_application"
            },
            dom_not_cache: true,
            ignore_exist: true,
            transition_type: "slide",
            initialize: function() {},
            page_init: function(page_view, route_params_arr, route_params_obj) {
                var self = this;
                var model_obj = null;
                var model_card_obj = {};
                var custom_params = decodeURIComponent(route_params_arr[0]);
                // 人体私拍协议
                var agreement_view_obj = new agreement_view({
                    parentNode: self.$el
                }).render();
                agreement_view_obj.hide();
                if (route_params_arr[0]) {
                    // 从其他页面直接跳转到表单页，通过路径传参数这样形成一个数据
                    // hudw 2014.12.8
                    var obj = eval("(" + custom_params + ")");


                    /*var cache  =
                    {
                        combo_text: "1元(200小时)",
                        continue_price: "2000",
                        continue_text: "加钟续拍每小时200元",
                        hour: "1",
                        price: "10",
                        style: "性感 清新1111",
                        address : "广东省-东莞",
                        location_id : 101029011,
                        detail_address : '肥龙广场9413号',
                        user_id : 100261,
                        time : "2015-1-13 12:30",
                        limit_num : 10,
                        person_num : 9,
                        user_name : 'Jane girl',
                        is_from_custom_data : true,
                        disable_time : true,
                        disable_address : true,
                        disable_detail_address : true,
                        disable_person_num : true
                    };

                    obj = cache;*/

                    console.log(obj);

                    model_obj = new model({});
                    if (!utility.is_empty(obj)) {
                        model_card_obj = new model_card({
                            user_id: obj.user_id
                        });
                        model_card_obj.get_info();
                    }
                    // 加载模特数据
                    model_card_obj.on("before:fetch", function() {
                        m_alert.show("加载中...", "loading", {
                            delay: -1
                        });
                    }).on("success:fetch", function(response) {
                        m_alert.hide();
                        model_obj = model_card_obj;
                        model_obj.set("model_selected_info", obj);
                        utility.storage.set("submit_cache_" + utility.user.get("user_id"), model_obj);
                        console.log(model_obj.toJSON());
                        self.submit_application_view_obj = new submit_application_view({
                            model: model_obj,
                            parentNode: self.$el,
                            templateModel: model_obj.toJSON(),
                            is_from_custom_params: 1,
                            model_card_obj: model_card_obj,
                            agreement_view_obj: agreement_view_obj
                        }).render();
                    }).on("error:fetch", function() {
                        m_alert.show("网络异常", "error");
                    });
                } else {
                    if (route_params_obj && route_params_obj.cid) {
                        //从约拍风格到达此页面
                        model_obj = route_params_obj;
                        console.log(model_obj);
                    } else {
                        //在约拍风格跳转其他流程，再到达此页面，建立新的model,从缓存中读取model属性 2014-12-4 nolset
                        // 做容错机制交互
                        model_obj = new model({});
                        model_obj.attributes = utility.storage.get("submit_cache_" + utility.user.get("user_id"));
                    }
                    self.submit_application_view_obj = new submit_application_view({
                        model: model_obj,
                        parentNode: self.$el,
                        templateModel: model_obj.toJSON(),
                        is_from_custom_params: 0,
                        model_card_obj: model_card_obj,
                        agreement_view_obj: agreement_view_obj
                    }).render();
                }
            },
            page_before_show: function() {},
            page_show: function() {},
            page_before_hide: function() {},
            window_change: function(page_view) {}
        };
    } ]);

})

