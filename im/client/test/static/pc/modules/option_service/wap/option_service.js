define('option_service/wap/option_service', function(require, exports, module){ /**
 * Created by nolest on 15/11/11.
 **/
/**
 * @require modules/option_service/wap/service_panel.scss
 *
 */
// ========= 模块引入 =========
var $ = require('components/jquery/jquery.js');

var service_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<div class=\"options_service_panel fn-hide\" data-role=\"options_service_panel\">\r\n    <div class=\"ds-box pack-center service_part_empty\">----未选择发送的消费者----</div>\r\n</div>";
  });

var service_item = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n    <div class=\"ds-box orient-h inner_service_item\" data-role=\"part_service_item\" data_goods_id=\"";
  if (helper = helpers.goods_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.goods_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\r\n        <div class=\"ds-box info-main flex-1\">\r\n            <div class=\"ds-box img_con align-center\">\r\n                <img src=\"";
  if (helper = helpers.images) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.images); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" style=\"width: 40px;height: 40px;border-radius: 2px;\"/>\r\n            </div>\r\n            <div class=\"ds-box orient-v info-con flex-1 pack-center\">\r\n                <div class=\"title\">";
  if (helper = helpers.titles) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.titles); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n                <div class=\"ds-box orient-v p-g-con\">\r\n                    <div class=\"prices\">￥";
  if (helper = helpers.prices) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.prices); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n                    <div class=\"goods_id\">商品ID:";
  if (helper = helpers.goods_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.goods_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n        <div class=\"ds-box service_btn orient-v pack-center\">\r\n            <div class=\"ds-box align-center ways pack-center ways\" data-role=\"service_send\">发送</div>\r\n            <div class=\"ds-box align-center ways pack-center ways fn-hide\" data-role=\"service_goto\">查看</div>\r\n        </div>\r\n    </div>\r\n";
  return buffer;
  }

  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { return stack1; }
  else { return ''; }
  });

var service_empty = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<div class=\"ds-box pack-center service_part_empty\">----该商家没有服务----</div>";
  });

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
});