define('common/guide/guide', function(require, exports, module){ 'use strict';

/*
 *  ������ʾ
*/

/**
 * @require modules/common/guide/guide.scss
**/



var $ = require('components/jquery/jquery.js');
var cookie = require('common/cookie/index');


module.exports = 
{
    render: function (dom) {
        // tpl��׺���ļ�Ҳ��������ģ��Ƕ�룬���handlebars
        // tpl�ļ�������ģ��������ܣ�Ƕ���ֻ����Ϊ�ַ���ʹ
        // �ã�tpl�ļ�Ƕ��֮ǰ���Ա����ѹ���������С��
        // handlebars����ȱ����Ӧ��ѹ����������ʱ������Ԥ
        // ����׶���ѹ����ѡ��tpl����handlebars��������ѡ
        // ��            
        
        var self = this;   

        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<div class=\"guide-page w960\">\n\n    <div class=\"setp-1 \"  step_operation=\"1\"  id=\"setp-1\">\n        <div class=\"img\"></div>\n    </div>\n    \n\n\n    <div class=\"setp-2  fn-hide\" step_operation=\"2\" >\n\n        <div class=\"title font_wryh\">�鿴��������</div>\n\n        <ul class=\"list clearfix f14 mt15\">\n            <li>1. Ϊ��ȷ����������ʵ�ԣ����ǻ��ϸ����ÿһ������������</li>\n            <li>2. Ϊ��ȷ���ͻ�����õ���ʱ��Ӧ��ÿ����������Чʱ��Ϊ24Сʱ������ص�һʱ�䴦�����յ� <br />\n�Ķ�������߳ɽ������ԡ�(��ʼʱ��Ϊ�ʼ�����ʱ��) </li>\n\n\n        </ul>  \n        <div class=\"box2\">\n            <div class=\"img\"></div>\n        \n        </div>\n\n    </div>\n\n\n\n\n    <div class=\"setp-3  fn-hide\" step_operation=\"3\" >\n\n        <div class=\"title font_wryh\">������֪</div>\n\n        <div class=\"box1 f14\">���Ѹ���ύ���ۣ��̶���ö�����</div>\n\n        <ul class=\"list clearfix f14\">\n            <li>1. 3������������֤�̼ң�5��������ͨ�̼� (������ʱ������ �ȱ����ƣ� ��5���������ٱ���) <br />��������յ������ĵ�һʱ��ѡ���Ƿ񱨼�����߳ɹ��ʡ�</li>\n            <li>2. �ƶ�����۸��������а����ķ������ݣ���߱��۾��������ٽ����״�ɡ����۵�ͬʱ������<br />\n���߿ͻ����������ڱ��˵������ޡ��⽫������ӿͻ������������</li>\n        </ul>   \n\n\n        <div class=\"box2\">\n            <div class=\"img\"></div>\n        </div> \n\n    </div>\n    \n\n\n    <div class=\"setp-4 fn-hide\" step_operation=\"4\">\n        <div class=\"title\">������⿨</div>\n        <div class=\"box1 f14\">���⿨��ȡ��ʽ:</div>\n        <ul class=\"list clearfix f14\">\n            <li>1. ֱ�ӳ�ֵ�������⿨ ��</li>\n            <li>2. ͨ����Ӧ���ֶһ����⿨�����⿨ֻ������ѡ���ύ���۵�����±��۳��� �鿴�ͻ���������Ҫʹ�����⿨ ��</li>\n            <li>3. ���㷢�͵ı�����48Сʱ��δ���ͻ�����鿴�� ��Ӧ�۳������⿨���˻�������˺��ϡ�</li>\n\n        </ul>  \n        <div class=\"box2\">\n            <div class=\"img\"></div>\n        \n        </div>\n    </div>\n\n\n    <div class=\"setp-5 fn-hide\" step_operation=\"5\">\n        <div class=\"title\">������ʵ����</div>\n        <ul class=\"list clearfix\">\n            <li>ʵʱ���¶��������������ÿ��״̬��һĿ��Ȼ�����˿�ȷ������ı��ۣ� ��Ӷ���㣬 ���ǽ������֪ͨ�㡣</li>\n            <li>��ɫ�ǹ˿��Ѳ鿴���ۣ� ��ɫ���ѹ�Ӷ�� ��ɫ����֧������ ��ɫ����ʧЧ��</li>\n            <li>�����������ϵ�ͻ�����߶����ɹ��ʡ�</li>\n        </ul>        \n        <div class=\"box2\">\n            <div class=\"img\">\n                \n            </div>\n        </div>\n    </div>\n\n\n\n    <div class=\"foot-box clearfix font_wryh fn-hide\"  id=\"foot-box\">\n        <div class=\"back fldi\" ><a href=\"javascript:void(0)\" id=\"back\">����</a></div>\n        <div class=\"next frdi\" id=\"next\">����</div>\n    </div>    \n\n    \n\n\n</div>";
  });  


        dom.innerHTML = template(
            {

            }
        );          
        

        
    },

    // ��ʼ��                  
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

    //�ص�����
    go_top : function() 
    {
        var self = this;
        $("html,body").animate({scrollTop:0},300); 
    },

    operation : function () {
        var self = this;   

        var i = 1 ;

        var congfig = true ;

        // �ж���ʾ
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



        //  ��ǰ
        $('#next').click(function() {

            //�ص�����
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



        //  ���
        $('#back').click(function() {

            //�ص�����
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