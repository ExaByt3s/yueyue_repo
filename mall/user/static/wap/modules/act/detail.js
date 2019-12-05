define('act/detail', function(require, exports, module){ var $ = require('components/zepto/zepto.js');
//����ģ��
var LZ = require('../../../../modules/common/lazyload/lazyload');
var utility = require('../../../../modules/common/utility/index');
var App =  require('../../../../modules/common/I_APP/I_APP.js');
var menu = require('../../../../modules/menu/index');
var header = require('../../../../modules/common/widget/header/main');

$(function()
{

    //new ���� �½����ö���
    var lazyloading = new LZ($('body'),{
        size : window.innerWidth -20
    });
    /*$('[data-role="act-img"]').each(function(i,obj)
     {
     load_img($(obj));
     });

     function load_img(img)
     {
     var $img = $(img);

     var url = $img.attr('data-src');

     var newWidth = window.innerWidth - 20;
     var oldWidth = $img.attr('data-width');
     var oldHeight = $img.attr('data-height');

     var newHeight = (newWidth * oldHeight) / oldWidth;


     $img.height(newHeight).css('background-image','url("'+url+'")');
     }*/

    // ��Ⱦͷ��
    header.init({
        ele : $("#global-header"), //ͷ����Ⱦ�Ľڵ�
        title:"�����",
        header_show : true , //�Ƿ���ʾͷ��
        right_icon_show : false, //�Ƿ���ʾ�ұߵİ�ť

        share_icon :
        {
            show :false,  //�Ƿ���ʾ����ťicon
            content:""
        },
        omit_icon :
        {
            show :false,  //�Ƿ���ʾ����Բ��icon
            content:""
        },
        show_txt :
        {
            show :true,  //�Ƿ���ʾ����
            content:""  //��ʾ��������
        }
    });


    $('[data-role="act-img"]').on('click',function(ev)
    {
        var self = this;

        var $cur_btn = $(ev.currentTarget);

        var $total_alumn_img = $('[data-role="act-img"]');

        var total_alumn_img_arr = [];

        $total_alumn_img.each(function(i,obj)
        {
            total_alumn_img_arr.push
            ({
                url : $(obj).attr('data-l-src'),
                text : ''
            });
        });

        // ��ǰͼƬ����
        var index = $total_alumn_img.index($cur_btn);

        var data =
            {
                img_arr : total_alumn_img_arr,
                index : index
            };

        if(App.isPaiApp)
        {
            App.show_alumn_imgs(data);
        }
        else
        {
            //todo �Ժ�΢�Ż�wap�汾����
        }
    });

    //���Ͻǲ˵�������

    var share_text = {share_text};
    console.log(share_text);
    var menu_data =
            [
                {
                    index:0,
                    content:'����',
                    click_event:function()
                    {
                        var self = this;
                        App.share_card(share_text.result_data,
                            function(data)
                            {

                            }
                        )
                    }
                },
                {
                    index:1,
                    content:'��ҳ',
                    click_event:function()
                    {
                        App.isPaiApp && App.switchtopage({page:'hot'});
                    }
                },
                {
                    index:2,
                    content:'ˢ��',
                    click_event:function()
                    {
                        window.location.href = window.location.href;
                    }
                }

            ];
    menu.render($('body'),menu_data);

    var __showTopBarMenuCount = 0;

    utility.$_AppCallJSObj.on('__showTopBarMenu',function(event,data)
    {

        __showTopBarMenuCount++;

        if(__showTopBarMenuCount%2!=0)
        {
            menu.show()
        }
        else
        {
            menu.hide()
        }
    });


    //ͷ����תê��
    $('#plan').click(function(){
        var $par3 = $('#par3');
        var $vi = $('html,body');
        var $t3 = $par3.offset().top;
        $vi.scrollTop($t3);
        $(this).addClass('cur');
        $(this).siblings().removeClass('cur');
    });
    $('#info').click(function(){
        var $par2 = $('#par2');
        var $vi = $('html,body');
        var $t2 = $par2.offset().top;
        $vi.scrollTop($t2);
        $(this).addClass('cur');
        $(this).siblings().removeClass('cur');
    });
    $('#introduce').click(function(){
        var $par1 = $('#par1');
        var $vi = $('html,body');
        var $t1 = $par1.offset().top;
        $vi.scrollTop($t1);
        $(this).addClass('cur');
        $(this).siblings().removeClass('cur');
    });

}); 
});