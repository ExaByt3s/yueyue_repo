define('recommend/g', function(require, exports, module){ 'use strict';

/*
 * ����ͷ��
*/
var header = require('common/widget/header/main');

var $ = require('components/jquery/jquery.js');


//����ѡ�����
var SelectLocal = require('common/widget/selectlocal/selectlocal');


$(function() {
	

	// ��Ⱦͷ��
	var $header_container = $('#yue-topbar-userinfo-container');
	header.render($header_container[0]);


    // �۵�����
    $('.item-title ').click(function() {
            if ($(this).next().hasClass('list')) 
            {
                $(this).next().toggleClass('dn');
                $(this).toggleClass('item-title-cur');
            }

    });


    // ��ѡ����ѡ����
    $('[question_titleid]').each(function(i) {
        var type = $(this).attr('type');

        switch (type)
        {

            // ��ѡ 
            case '1': 

                var $that = $(this) ;

                // ��ʾ������ ����
                if ($(this).find('[data-link]').length > 0 ) 
                {
                    
                    $(this).find('input[type=checkbox]').click(function() {
           
                        $that.find('input[type=radio]').each(function(index) {
                            $(this).attr("checked",false);
                        });
                        
                    });

                    $(this).find('input[type=radio]').click(function() {
                        $that.find('input[type=checkbox]').each(function(index) {
                            $(this).attr("checked",false);
                        });
                    });


                }
                else
                {

                    // û����������������
                    var $that = $(this);
                    $(this).find('[btn-radio-txt]').click(function() {
                        $that.find("input[type=radio]").prop("checked", false);
                        $(this).find("input[type=radio]").prop("checked", true);
                    });

                    
                }
            break;


            // ��ѡ ��������
            case '2': 
                $('[checkbox-txt]').click(function() {
                    if ($(this).find("input[type=checkbox]").is(':checked')) 
                    {
                        $(this).find("input[type=checkbox]").prop("checked",false);
                    }
                    else
                    {
                        $(this).find("input[type=checkbox]").prop("checked",true);
                    }
                    
                });
            break;


        }     
    });


    // �������
    $('[selectlocal_area]').each(function(i) {
        
        var id = $(this).attr('selectlocal_area');
        new SelectLocal('#province-'+id, '#city-'+id)
    });


   

    // �ռ�����
    var question_id = $('#question_id').val();
    var service_id = $('#service_id').val();
    var version = $('#version').val();


    $('#submit').click(function() {


        // ������ʾ����
        var congfig = true ;

        function error_tips(ele,str) 
        {
            alert(str) ;
            $("html,body").animate({scrollTop:ele.offset().top},300);
            congfig = false ;
            return false;
        }


        var arr = [] ; //�ռ���Ŀ��������



        $('[question_titleid]').each(function(i) {

            var question_titleid = $(this).attr('question_titleid'); //����������
            var type = $(this).attr('type'); //��Ŀ���� //1��ѡ 2��ѡ 4�ʴ� 6���� 7ʱ������

            switch (type)
            {

                // 1Ϊ��ѡ
                case '1': 
            
                    var data = [] ;
                    var $that = $(this) ;

                    // ��ʾ����������
                    if ($(this).find('[data-link]').length > 0 ) 
                    {

                        $(this).find('.item-title').each(function(index) {
                
                            if ($(this).next().hasClass('list'))
                            {
                           
                                var father_anwserid = $(this).attr('data-link');
                                var son_data = [];

                                if ($(this).attr('data-type') == 1 ) 
                                {
                                    
                                    var sel_val =  $(this).next().find("input[type=radio]:checked").val();
                                    if (sel_val) 
                                    {

                                        son_data.push({
                                            anwserid : sel_val,
                                            message : 1 
                                        })

                                        data.push({
                                            anwserid : father_anwserid,
                                            data : son_data
                                        })

                                    }

                                    
                                }

                                if ($(this).attr('data-type') == 2 )
                                {

                                    var can_check ;
                                    $(this).next().find('input[type=checkbox]:checked').each(function(i) {

                                        can_check = 1 ;

                                        son_data.push({
                                            anwserid : $(this).val(),
                                            message : 1 
                                        })

                                    });


                                    if (can_check == 1) 
                                    {
                                        data.push({
                                            anwserid : father_anwserid,
                                            data : son_data
                                        })
                                    }
                                    
                                }

                                //  û��ѡ������
                                // if (data.length < 1) 
                                // {
                                //     error_tips($that,$that.find('.title').html() );
                                //     return false;
                                // }

                      


                            }

                            else
                            {
                                if ($(this).find('input[type=radio]').is(':checked') ) 
                                {
                                    data.push({
                                        anwserid : $(this).find('input[type=radio]:checked').val(),
                                        message : 1 
                                    })
                                }



                                
                            }

                        });         
                   
                      
                        if (data.length < 1) 
                        {
             
                            error_tips($that,$that.find('.title').html() );
                            return false ;
                        }            

                       
                    }
                    else
                    {

                        // var anwserid = $(this).find("input[type=radio]:checked").val();
                        var radio_check =  $(this).find("input[type=radio]:checked") ;

                        //  ��ѡû��ѡ�� ������ʾ
                        if (radio_check.length < 1) 
                        {
                      
                            error_tips($that,$that.find('.title').html() );
                            return false ;
                        }

                        if (radio_check.attr('is_input') == 2 ) 
                        {
                            data.push({
                                anwserid : radio_check.val(),
                                message : radio_check.next().val()
                            })
                        }
                        else
                        {
                            data.push({
                                anwserid : radio_check.val(),
                                message : 1
                            })


                        }

                    }



                break;



                // ��ѡ
                case '2': 
                    var data = [] ;
                    // �������������
                    if ($(this).find('[data-link]').length > 0 ) 
                    {
                        
                        
                        $(this).find('.item-title').each(function(index) {
                 
                 
                            if ($(this).next().hasClass('list'))
                            {

                                if ($(this).attr('data-type') == 1) 
                                {
                                    var father_anwserid = $(this).attr('data-link');
                                    var son_data = [];

                                    var sel_val =  $(this).next().find("input[type=radio]:checked").val();

                                    if (sel_val) 
                                    {
                                        son_data.push({
                                            anwserid : sel_val,
                                            message : 1 
                                        })


                                        data.push({
                                            anwserid : father_anwserid,
                                            data : son_data
                                        })
                                    }

                                   
                                }

                                if ($(this).attr('data-type') == 2) 
                                {

                                    var father_anwserid = $(this).attr('data-link');
                                    var son_data = [];
                                    $(this).next().find('input[type=checkbox]:checked').each(function(n) {
                                        son_data.push({
                                            anwserid : $(this).val(),
                                            message : 1 
                                        })
                                    });

                                    
                                    data.push({
                                        anwserid : father_anwserid,
                                        data : son_data
                                    })
                                }
                           
                               
                            }
                            else
                            {
                                if ($(this).find('input[type=checkbox]').is(':checked') ) 
                                {
                                    data.push({
                                        anwserid : $(this).find('input[type=checkbox]:checked').val(),
                                        message : 1 
                                    })
                                }
                                
                            }

                        });


                        if (data.length < 1) 
                        {
                        
                            error_tips($that,$that.find('.title').html() );
                            return false ;
                        }     
                                                
                    }
                    else
                    {
                        
                        // û��ѡ��ѡ�����
                        if ($(this).find('input[type=checkbox]:checked').length < 1) 
                        {
                            error_tips($(this),$(this).find('.title').html() );
                            return false;
                        }

                        $(this).find('input[type=checkbox]:checked').each(function(k) {

                            if ($(this).attr('is_input') == 0) 
                            {
                                data.push({
                                     anwserid : $(this).val(),
                                    'message' : 1
                                })
                            }

                            if ($(this).attr('is_input') == 1) 
                            {
                                data.push({
                                     anwserid : $(this).val(),
                                    'message' : $(this).next().val()
                                })
                            }

                            
                        });


                    }
                    
                    
                break;


                // �ʴ�
                case '4' :
                    var data = [] ;
                    var son_data = [];
                    var anwserid = $(this).find('.textarea-class').attr('question_titleid');

                    var father_anwserid = $(this).find('.main-title').attr('id');

                    son_data.push({
                        anwserid : anwserid,
                        message : $(this).find('textarea').val().toString()
                    })
   
                    data.push({
                        anwserid : father_anwserid,
                        data : son_data
                    })

                break ;


                // ����
                case '6' :
                    var data = [] ;

                    if ($(this).find('[province-anwserid]').length > 0 ) 
                    {

                        data.push({
                            anwserid : $(this).find('.province').attr('province-anwserid'),
                            message :  $(this).find('.town').val()
                        })
                    }
 

                    //  û��ѡ�����������ʾ
                    if (!$(this).find('.town').val()) 
                    {
                        error_tips($(this), $(this).find('.title').html());
                        return false;
                    }
                    


                    if ($(this).find('[other-anwserid]').length > 0 ) 
                    {

                        data.push({
                            anwserid : $(this).find('.input-text').attr('other-anwserid'),
                            message : $(this).find('.input-text').val()
                        })
                    }

                break ;


                //ʱ������   //7����8ʱ��9ʱ��
                case '7' :
                    var data = [] ;
                    var str = '';
                    var $that = $(this) ;
                    

                    if ($(this).find('[data-style-data]').length > 0) 
                    {
                        var time_val = $(this).find('[data-style-data]').val();

                        if (!time_val) 
                        {
                            // alert('��ѡ�����ڣ�') ;
                            // $("html,body").animate({scrollTop:$(this).offset().top},300);
                            // return false;

                            error_tips($(this),'��ѡ�����ڣ�');
                            return false;

                        }

                        // �Ƚ�ʱ��
                        var dateStr= function(s)
                        {
                            return new Date(s.replace(/-/g, "/"))
                        }
    
                        // ��ǰʱ��
                        var d = new Date();
                        var d_str = d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate();


                        if(dateStr(time_val).getTime() <= dateStr(d_str).getTime())
                        {
                            // alert('ѡ�����ڴ�����ѡ�����֮���ʱ�䣡') ;
                            // $("html,body").animate({scrollTop:$(this).offset().top},300);
                            // return false;
                            // 
                            error_tips($(this),'��ѡ�����֮���ʱ�䣡');

                            return false;



                        }



                        data.push({
                            message : time_val,
                            anwserid : $(this).find('[data-style-data]').attr('data-style-data')
                        })

                    }


                    if ($(this).find('[data-style-time]').length > 0) 
                    {

     
                        // if ($(this).find('[data-style-time]').find('#data-style-time-1').val() == 0 || $(this).find('[data-style-time]').find('#data-style-time-2').val() == 0 ) 
                        // {
                        //     error_tips($that,$that.find('.title').html() );

                        //     return false;
                        // }

                        data.push({
                            message : $(this).find('[data-style-time]').find('#data-style-time-1').val() +':'+ $(this).find('[data-style-time]').find('#data-style-time-2').val(),
                            anwserid : $(this).find('[data-style-time]').attr('data-style-time')
                        })


                    }


                    if ($(this).find('[data-style-long]').length > 0) 
                    {
                        data.push({
                            message : $(this).find('[data-style-long]').val() ,
                            anwserid : $(this).find('[data-style-long]').attr('data-style-long')
                        })
                    }

                break ;

            }     
            
            arr.push({
                question_titleid : question_titleid,
                data : data
            })


        });

        console.log(arr);

        // return ;

        if (congfig) 
        {
            $.ajax({
                url: window.$__config.ajax_url+'pc_sub_request.php',
                data: {
                    question_id  : question_id,
                    service_id  : service_id,
                    version : version ,
                    data : {
                        question_detail_list : arr
                    }
            
                },
                dataType: 'json',
                type: 'POST',
                cache: false,
                beforeSend: function() 
                {
                    
                },
                success: function(data) 
                {
                    var data = data.result_data ;

                    var num = data.result;

                    if (num == 1) 
                    {
                        $('#fade').show();
                        $('#pop_recommend_success').show();

                    }
                    else
                    {
                        alert(data.message);
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
});