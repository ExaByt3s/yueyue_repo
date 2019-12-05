'use strict';

/*
 * 引用头部
*/
var header = require('../common/widget/header/main');

/*
 * 首次登陆提示 引入
*/
var guide = require('../common/guide/guide');


var $ = require('jquery');




exports.init = function(options)
{

    var user_role = options && options.seller_id;
    $(function() {
        

        // 首次登陆提示
        var $guide = $('#guide');
        guide.render($guide[0]);  //渲染模板
        guide.init();   //初始化


        // 渲染头部
        var $header_container = $('#yue-topbar-userinfo-container');
        header.render($header_container[0],user_role);


        // 点击跳转
        $('[go_url_lead_id]').click(function(ev) 
        {
            var action = $(ev.target).attr('action');
            if (action) 
            {
                return ;
            }
            else
            {
                var lead_id = $(this).attr('go_url_lead_id');
                var url = './lead_detail.php?lead_id=' + lead_id ;
                window.location.href= url; 
            }

        })



        $('[lead_id]').click(function() 
        {
            var lead_id = $(this).attr('lead_id');
            var data = 
            {
                lead_id : lead_id
            }
            // console.log(lead_id);

            if (confirm("确认删除么？"))
            {
                $.ajax({
                    url: window.$__config.ajax_url + 'del_lead_list.php',
                    data: data,
                    type: 'POST',
                    cache: false,
                    beforeSend: function() 
                    {
                        
                    },
                    success: function(data) 
                    {
                        if (parseInt(data) == 1 ) 
                        {
                            $(['data-list-'+lead_id]).remove();
                            window.location.reload();
                        }
                        
                    },    
                    error: function() 
                    {
                        
                    },    
                    complete: function() 
                    {
                        
                    } 
                });
            }
           
            
        });
    });
}