'use strict';

/*
 *  引导提示
*/

/**
 * @require ./guide.scss
**/



var $ = require('jquery');
var cookie = require('../cookie/index');


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

        var template  = __inline('./guide.tmpl');  


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


