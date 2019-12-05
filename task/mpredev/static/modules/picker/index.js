define('picker/index', function(require, exports, module){ 

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
    var picker_tmpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\r\n                    <div class=\"choose_child\">\r\n                        <div class=\"title\" data-role=\"choose_child_title\">\r\n                            <p>";
  if (helper = helpers.bit) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.bit); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p>\r\n                        </div>\r\n                        <div class=\"scroll\" data-role=\"scroll\">\r\n\r\n                            ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n                        </div>\r\n                    </div>\r\n                ";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\r\n                                <div data-child=\"child\" data-role=\""
    + escapeExpression(((stack1 = (data == null || data === false ? data : data.index)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" ><p>"
    + escapeExpression((typeof depth0 === functionType ? depth0.apply(depth0) : depth0))
    + "</p></div>\r\n                            ";
  return buffer;
  }

  buffer += "<div class=\"ui-picker\" data-role=\"ui-picker\">\r\n    <div class=\"ui-picker-fade\">\r\n        <div class=\"ui-picker-container\" data-role=\"ui-picker-container\">\r\n            <div class=\"choose_area\">\r\n                <div class=\"picker_line\" data-role=\"picker_line\"></div>\r\n                ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n            </div>\r\n            <div class=\"btn_area\" data-role=\"btn_area\">\r\n                <div class=\"btn cancel\"><span>取消</span></div>\r\n                <div class=\"btn confirm\"><span>确认</span></div>\r\n            </div>\r\n\r\n        </div>\r\n    </div>\r\n</div>";
  return buffer;
  });

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




 
});