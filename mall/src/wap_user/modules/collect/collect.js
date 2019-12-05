/**
 * �ղز���
 * ��Բ
 */
 /**
  * @require ./collect.scss
  **/

"use strict";
var utility = require('../common/utility/index');
var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
// var App =  require('../common/I_APP/I_APP');
var items_tpl = __inline('./collect.tmpl');


if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}


var _self = $({});

function collect(options) 
{
    var self = this;
    self.render_ele = options.ele ;
    self.target_id = options.target_id;
    self.target_type = options.target_type;
    self.value = options.value ;

    self.config = true ;
    self.$loading = {};

    //�ж���ʾ�ղأ��������ղ�
    if (parseInt(self.value) == 0) 
    {
        self.data_txt = 
        {
            txt : '�ղ�',
            class_name : ''
        };

        self.operate  = 'follow';

    }
    else
    {
        self.data_txt = 
        {
            txt : '���ղ�',
            class_name : 'has-collect'
        }

        self.operate  = 'unfollow';

    }

    self.init()

}

collect.prototype =
{
    refresh : function()
    {
        var self = this;
        var html_str = items_tpl(self.data_txt);
        var view = self.render_ele.html(html_str);
        self.click_ele = view.find('[data-role="collect"]');
        self.click_ele.unbind('click').on('click', function(event) {

            if (self.config) 
            {

                self.add_collect();
            }
                
        });
    },

    init : function()
    {

        var self = this;
        self.refresh();

    },

    add_collect : function() 
    {
        var self = this;
        var data = 
        {
            target_id : self.target_id ,
            target_type : self.target_type  ,
            operate : self.operate
        };
        $.ajax({
            url: '../ajax/collect.php',
            data: data,
            dataType: 'json',
            type: 'POST',
            cache: false,
            beforeSend: function() 
            {
                self.config = false ;
                self.$loading = $.loading
                ({
                    content:'������...'
                });
            },
            success: function(data) 
            {
                // debugger;
                self.$loading.loading("hide");
                self.config = true ;
                var ret = data.result_data.data ;

                if (ret.result == 1) 
                {
                    
                    $.tips({
                        content: ret.message,
                        stayTime:3000,
                        type:'success'
                    });

                    if (parseInt(self.value) == 0) 
                    {
                        self.click_ele.find('[data-role="btn-collect"]').addClass('has-collect');
                        self.click_ele.find('[data-role="txt-collect"]').html('���ղ�');
                        self.operate  = 'unfollow';
                        self.value = 1 ;
                    }
                    else
                    {
                        self.click_ele.find('[data-role="btn-collect"]').removeClass('has-collect');
                        self.click_ele.find('[data-role="txt-collect"]').html('�ղ�');
                        self.operate  = 'follow';
                        self.value = 0 ;
                    }
                }
                else
                {
                    $.tips({
                        content: ret.message,
                        stayTime:3000,
                        type:'warn'
                    });
                }


            },    
            error: function() 
            {
                self.$loading.loading("hide");
                self.config = true ;
                $.tips({
                    content:'�����쳣',
                    stayTime:3000,
                    type:'warn'
                });
                
            },    
            complete: function() 
            {
                self.$loading.loading("hide");
                self.config = true ;

            } 
        });
        
    }

    
};

return  collect;



