define('seller/seller_list', function(require, exports, module){ /**
 * Created by huanggc on 15/10/9.
 */

// ========= ģ������ ========= 
var $ = require('components/zepto/zepto.js');
var utility = require('common/utility/index');
var yue_ui = require('yue_ui/frozen');
var abnormal = require('common/widget/abnormal/index');
var LZ = require('common/lazyload/lazyload');
var _self = $({});
// ���캯��
/**
 *                //������
                handlebarsģ��ʹ�����ǽӿ������÷� style="width:{{percent score "5"}}"
                percent : 
                score ��������
                "5" : ���������ֵ
 * @param options
 * @param matter
 * @param event
 */
 function list_item_class(options)
{
    var self = this;

    var options = options || {} ;
    //��ȾĿ��
    self.$render_ele = options.ele || {} ;
    //���������ַ
    self.send_url = options.url || "";
    //���ݲ���
    self.ajax_params = options.params ;
    //ģ��
    self.template = options.template || {} ;

    self.init(options);
}
list_item_class.prototype =
{
    /*********** ˢ�� ************/
    refresh   : function()
    {
        var self = this;

        self.page = 1;
        self.action(self.page);
    },

    load_more : function()
    {
        var self = this;

        if(_self.has_next_page)
        {
            self.page++;
            self.action(self.page);
        }
        else
        {
            self.$load_more.html('���ײ���');
        }
    },
    /*********** ��ʼ�� ************/
    init : function(options)
    {
        var self = this;

        _self.$loading = {};
        _self.page = 1 ;

        self.$render_ele.parent().next().append('<div class="load_more fn-hide" data-role="load_more">���ڼ���...</div>');
        self.$load_more = $('[data-role="load_more"]');

        self.refresh();
        self.window_scroll_more();
    },

    action : function (page)
    {
        var self = this ;

        if(self._sending)
        {
            return;
        }

        self.ajax_params = $.extend(self.ajax_params,{page:page});

        _self.ajax_obj = utility.ajax_request 
        ({
            url : self.send_url,
            data : self.ajax_params,
            beforeSend : function()
            {
                self._sending = true;
                _self.$loading = $.loading
                ({
                    content:'������...'
                });

            },
            success : function(data)
            {
                self._sending = false;

                _self.$loading.loading("hide");

                var list_data = data.result_data.list.data.list;

                _self.has_next_page = data.result_data.has_next_page;

                console.log(data);
                console.log(_self.has_next_page);

                // �����ݴ��� 
                if(!list_data.length && page == 1)
                {
                    abnormal.render(self$render_ele[0],{});

                    self.$load_more.addClass('fn-hide');

                    return;
                }
                else
                {
                    self.$load_more.removeClass('fn-hide');
                }

                //����ҳ��
                var html_str = self.template
                ({
                    list : list_data
                });

                self.$render_ele.append(html_str);

                self.setup_event();
            },
            error : function()
            {
                self._sending = false;
                _self.$loading = $.loading
                ({
                    content : "�����쳣"
                });
            }
        })

    },

    window_scroll_more : function()
    {
        var self = this;
        $(window).scroll(function()
        {
            if($(window).scrollTop() + $(window).height() > $(document).height() - 50)
            {
                self.load_more();
            }
        });
    },
    setup_event : function()
    {
        var self = this;
        //new ���� �½����ö���
        if(!self.lazyloading)
        {
            var lz_options_obj =
                {
                    size : window.innerWidth
                };

            if(!self.is_open_lz_opts)
            {
                lz_options_obj = {};
            }

            self.lazyloading = new LZ($('body'),lz_options_obj);
        }
        else
        {
            self.lazyloading.refresh();
        }

        //�ж��б�С����Ļ�߶�
        if(self.$render_ele.height()<window.innerHeight)
        {
            self.$load_more.addClass('fn-hide');
        }

    }

}
return list_item_class; 
});