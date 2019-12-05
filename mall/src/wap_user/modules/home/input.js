/**
 * Created by hudingwen on 15/7/30.
 */
var $ = require('zepto');
var header = require('../common/widget/header/main');
var utility = require('../common/utility/index');
var App =  require('../common/I_APP/I_APP.js');
var yue_ui = require('../yue_ui/frozen');
var item_tpl = __inline('./input_page.tmpl');

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

