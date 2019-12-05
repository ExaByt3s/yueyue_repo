

var temp_data;

exports.setData = function(data)
{
    if(!data || data.length == 0)
    {
        console.error("data not enough");

        return
    }

    temp_data = data;

};

exports.show = function()
{
    var picker_tmpl = __inline('./picker.tmpl');

    var picker_html = picker_tmpl({data: temp_data});

    var picker_height = document.body.clientHeight*0.4;

    var picker_width = document.body.clientWidth*0.7;

    var picker_btn_height = picker_height*0.2;

    var picker_title_height = picker_height*0.15;

    var choose_height = Math.floor(picker_height * 0.15);

    $('body').append(picker_html);

    $('[data-role="ui-picker-container"]').css("height",picker_height).css("width",picker_width);

    $('[data-role="choose_child_title"]').css("height",picker_title_height);

    $('[data-role="btn_area"]').css("height",picker_btn_height);

    $('[data-child="child"]').css("height",choose_height);

    $('[data-role="picker_line"]').css("height",choose_height-2);

    $('[data-role="picker_line"]').css("top",Math.floor(picker_title_height));

    $('[data-role="scroll"]')
        .css("paddingBottom",($('[data-role="scroll"]').height()));

    var timer;

    var scrollObj;
    var scroll_top;
    var refresh = function () {
        //停止滚动
        var rest = ((scrollObj.scrollTop()/choose_height).toFixed(1))%1;

        var _int = Math.floor(scrollObj.scrollTop()/choose_height);

        if(rest < 0.4)
        {
            scrollObj.scrollTop(_int*choose_height)
        }
        if(rest > 0.6)
        {
            scrollObj.scrollTop((_int+1)*choose_height);
        }
    }

    $('[data-role="scroll"]')
        .on('scroll',function()
        {
            scrollObj = $(this)
            clearTimeout(timer);
            timer = setTimeout( refresh , 150 );
        })
    return $('[data-role="ui-picker"]');
};




