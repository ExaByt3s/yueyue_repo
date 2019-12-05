/**
 *  个人空间Popup
 */
define(function(require, exports, module) {
    var Popup = require('../popup/index');
    var page_control = require('../../frame/page_control');

    var mainTpl = require('./mine-popup.handlebars');

    var mine_Popup = Popup.extend({
        setup: function() {
            var self = this;

            var items = self.get('items') ||
            {
                edit : true,
                report : false,//,
                share : false
            };
            var events_fork = self.get('events_fork');
            console.log(events_fork);
            self.setContent(mainTpl({
                    uid:self.get('uid'),
                    items : items

                })).$el.addClass('mine-popup')
                .on('tap', '[data-role=navigate]', function(event) {
                    var $target = $(event.currentTarget);

                    var appTarget = $target.attr('data-target');
                    self.hide();
                    switch (appTarget) {
                        case 'edit':
                            console.log(self.model);
                            page_control.navigate_to_page('mine/profile');
                            //page_control.navigate_to_page('model_date/model_card/edit_condition', {model_info:self.model});
                            break;
                        case 'setting':
                            //page_control.navigate_to_page('model_date/model_card/edit_condition');
                            break;
                        case 'about':
                            //page_control.navigate_to_page('model_date/model_card/edit_condition');
                            break;
                        case 'report':
                            page_control.navigate_to_page('report/'+self.get('report_model_id'));
                            break;
                        case 'share_act':
                            //活动分享页面
                            if(events_fork)events_fork.share_function();
                            break;

                    }
            });

            Popup.prototype.setup.call(self);
        },

        set_config: function(options) {
            var self = this;

            return self;
        }
    });

    module.exports = mine_Popup;
});