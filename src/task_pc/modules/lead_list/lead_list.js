'use strict';

/*
 * ����ͷ��
*/
var header = require('../common/widget/header/main');

/*
 * �״ε�½��ʾ ����
*/
var guide = require('../common/guide/guide');


var $ = require('jquery');




exports.init = function(options)
{

    var user_role = options && options.seller_id;
    $(function() {
        

        // �״ε�½��ʾ
        var $guide = $('#guide');
        guide.render($guide[0]);  //��Ⱦģ��
        guide.init();   //��ʼ��


        // ��Ⱦͷ��
        var $header_container = $('#yue-topbar-userinfo-container');
        header.render($header_container[0],user_role);


        // �����ת
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

            if (confirm("ȷ��ɾ��ô��"))
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