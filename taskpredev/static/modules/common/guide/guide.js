define('common/guide/guide', function(require, exports, module){ 'use strict';

/*
 *  引导提示
*/

/**
 * @require modules/common/guide/guide.scss
**/



var $ = require('components/jquery/jquery.js');
var cookie = require('common/cookie/index');


module.exports = 
{
    render: function (dom) {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选
        // 择            
        
        var self = this;   

        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<div class=\"guide-page w960\">\n\n    <div class=\"setp-1 \"  step_operation=\"1\"  id=\"setp-1\">\n        <div class=\"img\"></div>\n    </div>\n    \n\n\n    <div class=\"setp-2  fn-hide\" step_operation=\"2\" >\n\n        <div class=\"title font_wryh\">查看订单详情</div>\n\n        <ul class=\"list clearfix f14 mt15\">\n            <li>1. 为了确保订单的真实性，我们会严格审核每一个发布的需求。</li>\n            <li>2. 为了确保客户需求得到及时响应，每个订单的有效时间为24小时。请务必第一时间处理所收到 <br />\n的订单来提高成交可能性。(开始时间为邮件发送时间) </li>\n\n\n        </ul>  \n        <div class=\"box2\">\n            <div class=\"img\"></div>\n        \n        </div>\n\n    </div>\n\n\n\n\n    <div class=\"setp-3  fn-hide\" step_operation=\"3\" >\n\n        <div class=\"title font_wryh\">报价须知</div>\n\n        <div class=\"box1 f14\">如何迅速提交报价，继而获得订单？</div>\n\n        <ul class=\"list clearfix f14\">\n            <li>1. 3条报价来自认证商家，5条来自普通商家 (按报价时间排序， 先报先推， 满5条即不可再报价) <br />请务必在收到订单的第一时间选择是否报价来提高成功率。</li>\n            <li>2. 制定合理价格，列明所有包含的服务内容，提高报价竞争力，促进交易达成。报价的同时别忘记<br />\n告诉客户，你区别于别人的优势噢。这将大大增加客户对你的青睐。</li>\n        </ul>   \n\n\n        <div class=\"box2\">\n            <div class=\"img\"></div>\n        </div> \n\n    </div>\n    \n\n\n    <div class=\"setp-4 fn-hide\" step_operation=\"4\">\n        <div class=\"title\">获得生意卡</div>\n        <div class=\"box1 f14\">生意卡获取方式:</div>\n        <ul class=\"list clearfix f14\">\n            <li>1. 直接充值购买生意卡 。</li>\n            <li>2. 通过对应积分兑换生意卡，生意卡只会在你选择提交报价的情况下被扣除， 查看客户订单不需要使用生意卡 。</li>\n            <li>3. 若你发送的报价在48小时内未被客户点击查看， 对应扣除的生意卡将退还到你的账号上。</li>\n\n        </ul>  \n        <div class=\"box2\">\n            <div class=\"img\"></div>\n        \n        </div>\n    </div>\n\n\n    <div class=\"setp-5 fn-hide\" step_operation=\"5\">\n        <div class=\"title\">跟进落实需求</div>\n        <ul class=\"list clearfix\">\n            <li>实时更新订单情况，订单的每个状态都一目了然。若顾客确认了你的报价， 雇佣了你， 我们将会短信通知你。</li>\n            <li>蓝色是顾客已查看报价， 绿色是已雇佣， 橙色是已支付定金， 灰色是已失效。</li>\n            <li>你可以主动联系客户来提高订单成功率。</li>\n        </ul>        \n        <div class=\"box2\">\n            <div class=\"img\">\n                \n            </div>\n        </div>\n    </div>\n\n\n\n    <div class=\"foot-box clearfix font_wryh fn-hide\"  id=\"foot-box\">\n        <div class=\"back fldi\" ><a href=\"javascript:void(0)\" id=\"back\">返回</a></div>\n        <div class=\"next frdi\" id=\"next\">继续</div>\n    </div>    \n\n    \n\n\n</div>";
  });  


        dom.innerHTML = template(
            {

            }
        );          
        

        
    },

    // 初始化                  
    init : function()
    {
        var self = this;
        self.operation() ;
    },

    all_hide : function (){
        $('[step_operation]').addClass('fn-hide');
    },

    show : function(i){
        $('[step_operation="'+i+'"]').removeClass('fn-hide');
    },

    //回到顶部
    go_top : function() 
    {
        var self = this;
        $("html,body").animate({scrollTop:0},300); 
    },

    operation : function () {
        var self = this;   

        var i = 1 ;

        var congfig = true ;

        // 判断显示
        if (cookie.get('guide')) 
        {
            $('#guide').addClass('fn-hide');
            $('#list-page').removeClass('fn-hide');
        }


        $("#guide").click(function() {

            if (congfig) 
            {
                if ( $('#setp-1').is(":visible") == true )
                {

                    $('#foot-box').removeClass('fn-hide');
                    $('#setp-1').hide();
                    self.show(2);

                    i = i + 1 ;
                }
            }
            
            congfig = false ;


        });



        //  向前
        $('#next').click(function() {

            //回到顶部
            self.go_top() ;

            self.all_hide();
    
            i++;
            console.log(i);
            if ( i >= 6 ) 
            {
                $('#foot-box').addClass('fn-hide');
                
                $('#guide').addClass('fn-hide');
                $('#list-page').removeClass('fn-hide') ;
                cookie.set('guide',1,{
                    expires : 7
                })
                return false ;
            }

            console.log(i);
            self.show(i);

            
        });



        //  向后
        $('#back').click(function() {

            //回到顶部
            self.go_top() ; 

            self.all_hide();           

            i--;
            if ( i <= 1 ) 
            {
                $('#foot-box').addClass('fn-hide');
                $('#setp-1').show();
                congfig = true ;
                return false ;
            }

           console.log(i);
           self.show(i);
        
        });



    }   

};


 
});