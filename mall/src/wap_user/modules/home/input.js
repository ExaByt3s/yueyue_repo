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
            ele        : options.$el.find('[data-role="global-header"]'), //ͷ����Ⱦ�Ľڵ�
            title      : options.title || '�༭',
            style  : "position: absolute", 
            use_page_back : false,
            header_show: true, //�Ƿ���ʾͷ��
            right_icon_show: true, //�Ƿ���ʾ�ұߵİ�ť
            share_icon : {
                show   : false,  //�Ƿ���ʾ����ťicon
                content: ""
            },
            omit_icon  : {
                show   : false,  //�Ƿ���ʾ����Բ��icon
                content: ""
            },
            show_txt   : {
                show   : true,  //�Ƿ���ʾ����
                content: "ȷ��"  //��ʾ��������
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

        //input��
        var limit = self.params.limit;

        String.prototype.len = function()
        {
            return this.replace(/[^\x00-\xff]/g, "xx").length;
        };

        function numWord(num){
            var nowLength = Math.ceil($(num).val().len()/ 2);
            //�����������ƺ���
            if(nowLength>limit){
                $('#setNums').addClass('red');
            }
            else{
                $('#setNums').removeClass('red');
            }
            $('#setNums').html(nowLength);

            var flag = '';

            //������ʾ

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
        //�ǳ������������շ���
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

