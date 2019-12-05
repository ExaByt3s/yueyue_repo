var tpl = __inline('./list.tmpl');


var $ = require('zepto');
var scroll = require('../common/scroll/index');
var utility = require('../common/utility/index');
var yue_ui = require('../yue_ui/frozen');
var menu = require('../menu/index');
var abnormal = require('../common/widget/abnormal/index');
var LZ = require('../common/lazyload/lazyload');
$(function()
{

    var _self = $({});



    /*********** ����ˢ�¼����б����� ************/

    var list_class = function()
    {
        var self = this;

        self.init();

    };

    list_class.prototype =
    {
        /*********** ˢ�� ************/
        refresh : function()
        {
            var self = this;

            _self.page = 1;
            self.action(_self.page);

        },
        /*********** ���� ************/
        load_more : function()
        {
            var self = this;

            console.log(_self.has_next_page);

            if(_self.has_next_page)
            {
                _self.page++;
                self.action(_self.page);
            }
            else
            {
                $.tips
                ({
                    content:'�Ѿ�����ͷ��',
                    stayTime:3000,
                    type:'warn'
                });

                _self.scroll_view_obj && _self.scroll_view_obj.dragger.reset();
            }
 
        },
        /*********** ��ʼ�� ************/
        init : function()
        {
            var self = this;

            // ��ʼ������

            _self.$loading = {};
            _self.page = 1 ;
            _self.$scroll_wrapper = $('[data-role="wrapper"]');
            _self.scroll_view_obj = scroll(_self.$scroll_wrapper);

            _self.scroll_view_obj.on('success:drag_down_load',function(e,dragger)
            {
                self.refresh();
            });

            _self.scroll_view_obj.on('success:drag_up_load',function(e,dragger)
            {
                self.load_more();
            });

            // ��װ�¼�
            self._setup_event();

            self.refresh();
        },

        action : function(page,load_more)
        {

            var self = this;

            self._sending = false;

            if(self._sending)
            {
                return;
            }
            _self.ajax_obj = utility.ajax_request
            ({
                url       : window.$__config.ajax_url.get_topic_list,
                data      :
                {
                    page  : page
                },
                cache     : true,
                beforeSend: function ()
                {
                    self._sending = true;

                    _self.$loading = $.loading
                    ({
                        content:'������...'
                    });
                },
                success   : function (data)
                {

                    /* ���غ�*/
                    self._sending = false;

                    _self.$loading.loading("hide");

                    var res = data.result_data;

                    var list = res.list;

                    console.log(list); 

                    _self.has_next_page = res.has_next_page;

                    var html_str = tpl
                    ({
                        arr: list
                    });

                    var insert = $('[data-role="insert"]');

                    if(_self.page == 1)
                    {
                        insert.html(html_str);
                    }else
                    {
                        insert.append(html_str);
                    }

                    _self.trigger('success:get_list',res);

                    $('[data-role="nav-to-info"]').on('click',function()
                    {

                        var _id = $(this).attr('data-role-id');

                        window.location.href= "./index.php?topic_id="+_id;

                    });


                },
                error : function(res)
                {
                    self._sending = false;

                    _self.$loading.loading("hide");

                    _self.trigger('error:get_list',res);

                    $.tips
                    ({
                        content:'�����쳣',
                        stayTime:3000,
                        type:'warn'
                    });
                }

            });
        },
        /**
         * ��װ�¼�
         * @private
         */
        _setup_event : function()
        {
            var self = this;

            _self.on('success:get_list',function(res)
            {

                //new ���� �½����ö���
                if(!self.lazyloading)
                {
                    self.lazyloading = new LZ(_self.$scroll_wrapper,
                        {
                            size : window.innerWidth -20
                        });
                }
                else
                {
                    self.lazyloading.refresh();
                }


                _self.scroll_view_obj && _self.scroll_view_obj.dragger.reset();


            })
                .on('error:get_list',function(e,res)
                {
                    _self.scroll_view_obj && _self.scroll_view_obj.dragger.reset();
                });
        }
    };
    _self.list_obj = new list_class();
});