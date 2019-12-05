/**
 * Created by nolest on 15/11/11.
 **/
/**
 * @require ./service_panel.scss
 *
 */
// ========= 模块引入 =========
var $ = require('jquery');

var service_tpl = __inline('./service_panel.tmpl');

var service_item = __inline('./service_item.tmpl');

var service_empty = __inline('./service_empty.tmpl');

var option_service =
{
    init: function(options)
    {
        var self = this;

        self.contain = options.bar;

        self.render_lib = options.data.result_data;

        self.click_send = options.click_send || function(){};

        self.click_link = options.click_link || function(){};

        self.render();
    },
    render : function()
    {
        var self = this;

        var frame_str = service_tpl();

        self.contain.append(frame_str);

        self.panel_obj = $('[data-role="options_service_panel"]')
    },
    load : function(id)
    {
        var self = this;

        if(!id)return

        var id_lib;

        var str = '';

        $.each(self.render_lib,function(i,obj)
        {
            if(obj.seller_user_id == id)
            {
                id_lib = obj.good_list
            }
        })

        if(id_lib && id_lib.length == 0)
        {
            str = service_empty();
        }
        else
        {
            str = service_item({data:id_lib});
        }

        self.panel_obj.html(str);

        self._bind_events(id_lib);
    },
    show : function()
    {
        var self = this

        self.panel_obj.removeClass('fn-hide');
    },
    hide : function()
    {
        var self = this

        self.panel_obj.addClass('fn-hide');
    },
    _bind_events : function(id_lib)
    {
        var self = this;


        self.panel_obj.find('[data-role="service_send"]').on('click',function()
        {
            var con = $(this);

            var passer ;
            $.each(id_lib, function (i, obj)
            {
                if(obj.goods_id == con.parents('[data_goods_id]').attr('data_goods_id'))
                {
                    passer = obj
                }
            });
            if (typeof (self.click_send) == 'function')self.click_send.call(this,passer);
        });

        self.panel_obj.find('[data-role="service_goto"]').on('click',function()
        {
            if (typeof (self.click_link) == 'function')self.click_link.call(this,this);
        })
    }
}

return option_service;