
/**
 * nolestLam 2014/9/16.
 */
/**
 * 首次进入app引导
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var guide_tpl = require('./tpl/guide.handlebars');
    var page_control = require('../../frame/page_control');
    var App = require('../../common/I_APP');
    var Scroll = require('../../common/new_iscroll');

    module.exports = View.extend
    ({
        attrs :
        {
            template : guide_tpl
        },
        events :
        {

        },
        _setup_scroll : function()
        {
            var self = this;

            console.log(Scroll)

            var view_scroll_obj = Scroll(self.$container,
                {
                    bounce: false,
                    snap: true,
                    snapThreshold : self.threshold,
                    momentum: false,
                    hScroll : true,
                    vScroll : false,
                    hScrollbar: false,
                    vScrollbar: false,
                    checkDOMChanges: true
                });

            view_scroll_obj.on('scrollEnd',function()
            {
                var _self = this;


                if(self.page_num == _self.currPageX+1)
                {
                    setTimeout(function()
                    {
                        self.trigger('guide_hide');
                    },100);
                }
            });

            self.$container.find('[data-role="fail_last"]').on('tap',function()
            {

                self.trigger('guide_hide');
            });


            /*view_scroll_obj.on('scrollMoveAfter',function()
            {
                var _self = this;

                if(self.current_page == self.page_num && _self.maxScrollX-_self.x > self.threshold && !self.is_run)
                {
                    self.is_run = true;

                    //self.view_scroll_obj.scrollTo(_self.maxScrollX - utility.get_view_port_width());

                    self.is_over_end = true;

                }

            });*/


            self.view_scroll_obj = view_scroll_obj;

            self.view_scroll_obj.refresh();

        },
        _setup_event : function()
        {
            var self = this;
        },
        current_page : function()
        {
            var self = this;

            return self.current_page
        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="guide-body"]');

            self.$container.height(self.reset_viewport_height());

            self.$guide_container = self.$('[data-role="guide-page-warp-box"]');

            self.current_page = 1 ;

            self.is_run = false;

            self.threshold = utility.get_view_port_width()/5;

            self._setup_pages();

            self._setup_event();

            self._setup_scroll();

        },
        _setup_pages : function()
        {
            var self = this;

            var pages = self.get("page_tpl");

            var prepare_String = pages
            ({
                width : utility.get_view_port_width,
                height : utility.get_view_port_height
            });

            var prepare_obj = $(prepare_String);

            self.page_width = utility.get_view_port_width();
            //最后一页透明
            self.page_num = prepare_obj.find('.page').length;

            self.$guide_container.width(self.page_width * self.page_num);

            self.$('[data-role="guide-page-warp-box"]').html(prepare_String);
        },
        reset_viewport_width : function()
        {
            return utility.get_view_port_width
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height;

        },
        show : function()
        {
            var self = this;

            self.$el.removeClass('fn-hide');
        },
        hide : function()
        {
            var self = this;

            self.$el.addClass('fn-hide');
        }
    });
});