'use strict';

/*
 *  ������ʾ
*/

/**
 * @require ./guide.scss
**/



var $ = require('jquery');
var cookie = require('../cookie/index');


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

        var template  = __inline('./guide.tmpl');  


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


