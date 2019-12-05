define('home/input', function(require, exports, module){ /**
 * Created by hudingwen on 15/7/30.
 */
var $ = require('components/zepto/zepto.js');
var header = require('common/widget/header/main');
var utility = require('common/utility/index');
var App =  require('common/I_APP/I_APP');
var yue_ui = require('yue_ui/frozen');
var item_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, options, functionType="function", escapeExpression=this.escapeExpression, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n            <!--**个人简介**-->\n            <div class=\"textarea-con pt15\" data-role=\"intro-page\">\n                <textarea id=\"introduce\" class=\"ui-textarea-info input-content\" placeholder=\"";
  if (helper = helpers.input_title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.input_title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-role=\"text\">";
  if (helper = helpers.input_content) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.input_content); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</textarea>\n                <div class=\"num-word\">\n                    <span id=\"setNums\">0</span>\n                    <span>/&nbsp;";
  if (helper = helpers.limit) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.limit); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span>\n                </div>\n                <!--<span class=\"red tips\" style=\"display: none\">请填写内容</span>-->\n            </div>\n        ";
  return buffer;
  }

function program3(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n            <!--**输入昵称**-->\n            <div class=\"ui-input-info-mod mb10 pt15 \" data-role=\"nickname-page\">\n                <div class=\"item border-radius0\">\n                    <input id=\"nickname\" value=\"";
  if (helper = helpers.input_content) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.input_content); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" type=\"text\" maxlength=\"16\" class=\"ui-input-info input-content\" placeholder=\"";
  if (helper = helpers.input_title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.input_title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-role=\"text\">\n                    <i class=\"icon-delete\" style=\"display: none\"></i>\n                </div>\n                <span class=\"red tips\" style=\"display: none\">请填写内容</span>\n            </div>\n        ";
  return buffer;
  }

  buffer += "<!-- input层 -->\n<div data-role=\"global-header\" ></div>\n<div class=\"page-view input-page\" data-role=\"page-container\">\n    <div class=\"input-information\">\n        ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.type), "textarea", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.type), "textarea", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n        ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data},helper ? helper.call(depth0, (depth0 && depth0.type), "text", options) : helperMissing.call(depth0, "if_equal", (depth0 && depth0.type), "text", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n \n\n    </div>\n</div>";
  return buffer;
  });

module.exports =
{
    render : function($el,params)
    {
        var self = this;

        self.params = params;

        $el.html(item_tpl(params));

        self.init();

        self.$el = $el;

        self.id = 0;

        return $el;
    },
    setup_header : function(options)
    {
        var self = this;

        var header_obj = {};

        header_obj = header.init({
            ele        : options.$el.find('[data-role="global-header"]'), //头部渲染的节点
            title      : options.title || '编辑',
            style  : "position: absolute", 
            use_page_back : false,
            header_show: true, //是否显示头部
            right_icon_show: true, //是否显示右边的按钮
            share_icon : {
                show   : false,  //是否显示分享按钮icon
                content: ""
            },
            omit_icon  : {
                show   : false,  //是否显示三个圆点icon
                content: ""
            },
            show_txt   : {
                show   : true,  //是否显示文字
                content: "确定"  //显示文字内容
            }
        });

        self.id++;

        console.log(self.id)

        return header_obj;
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
    },
    init : function()
    {
        var self = this;

        //input层
        var limit = self.params.limit;

        String.prototype.len = function()
        {
            return this.replace(/[^\x00-\xff]/g, "xx").length;
        };

        function numWord(num){
            var nowLength = Math.ceil($(num).val().len()/ 2);
            //字数超出限制后变红
            if(nowLength>limit){
                $('#setNums').addClass('red');
            }
            else{
                $('#setNums').removeClass('red');
            }
            $('#setNums').html(nowLength);

            var flag = '';

            //空文提示

            var nickname = $('#nickname').val();
            var introduce = $('#introduce').val();
            console.log(nickname);
            if(!nickname==""){
                self.$el.find('.tips').hide();
                self.$el.find('.icon-delete').show();

                //flag = false;
            }else{

                self.$el.find('.tips').show();
                self.$el.find('.icon-delete').hide();
                //flag = true;
            }
            //return flag;

        }
        //昵称输入框，内容清空符号
        $('.icon-delete').on('click',function(){
            $('#nickname').val("").focus();
        });
        $('[data-role="text"]').on('focus',function(){
            numWord(this);
        });
        $('[data-role="text"]').on('input',function(){
            numWord(this);
        });
        $('[data-role="introduce"]').on('click',function()
        {
            var introduce_input = $('#introduce');
            numWord(introduce_input);
        })

    }
};

 
});