define('common/widget/uploader/index', function(require, exports, module){ /**
 * Created by hudingwen on 15/6/4.
 * 上传组件
 */


/**
 * @require modules/common/widget/uploader/main.scss
 *
 */

var $ = require('components/zepto/zepto.js');
var App = require('common/I_APP/I_APP');
var utility = require('common/utility/index');
var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n        <img src=\"";
  if (helper = helpers.img_url) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.img_url); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"\n             style=\"max-height: 100%;max-width: 100%;\">\n        <div class=\"close-out \" data-role=\"del-pic\" >\n            <i class=\"icon close-btn\"></i>\n        </div>\n    ";
  return buffer;
  }

function program3(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n        <img src=\"";
  if (helper = helpers.default_img_url) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.default_img_url); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-role=\"upload-flag\"\n             style=\"max-height: 100%;max-width: 100%;\">\n\n    ";
  return buffer;
  }

  buffer += "<div class=\"ui-uploader ";
  if (helper = helpers.is_active) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.is_active); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" style=\"width:";
  if (helper = helpers.width) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.width); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + ";height:";
  if (helper = helpers.height) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.height); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" >\n    ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.upload), {hash:{},inverse:self.program(3, program3, data),fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n</div>";
  return buffer;
  });

module.exports =
{
    render: function (dom,options)
    {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选
        // 择

        //console.log(css);

        var self = this;

        options = options || {};

        options.upload = options.upload || false;

        if(!options.upload)
        {
            options.default_img_url = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALQAAAC0CAYAAAA9zQYyAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA3tpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpENDZEQ0I5RjQwMDdFNDExODJEREMzQzk1NTAyQzA5MiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBNjI0RUFDQUE2OUYxMUU0QUQ3RUUwNzA4OUI5NzhCNSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBNjI0RUFDOUE2OUYxMUU0QUQ3RUUwNzA4OUI5NzhCNSIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgKFdpbmRvd3MpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MTg2NDhhMDAtMmQ2OS0yMjQzLWJiZDMtNmQzYWQ0OTg2MTZkIiBzdFJlZjpkb2N1bWVudElEPSJhZG9iZTpkb2NpZDpwaG90b3Nob3A6ZWYyMzJhMDktMTdiOC0xMWU0LThlYmMtZDk2MDViOWUyM2M5Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+gWRlewAABFBJREFUeNrs2t9qXFUUB+DJJEaLfQcxIGrjnxeoN6UXkfoWBlKxIBXBC6+9EKRVqJBifYFcW5qL0pvaB6i2VW8iPoMtxZE0ri3r6CEIZorZM2fP98FiJmdmh3b3Nzsrp2tpZ2dndMipqM2ot6NejHo2ry8det/B6Ghqr5vVWuvqrPs9ai9qN+pa1IP+m8a956tRV6J+iLoY9UovzDAvSiZfzYyWrG5HneheXOmF+XrU2d7CB/kJuBH1yxE+QU97Ah73ulmtte7/Xfdc1FrURnYQp/JA3op6KTuKSXdCX+6FeRJ1Ier1vP5THvMwSyWDP2YmSzbPRz3O185EXepajvVMeRfmc1FfRT2xh8ypks2rUe9kZkcZ8PVxHt/LebH0JTftFwNxK+rDfF4yvDnOnqS4n6mHIdnO7BYb42y0R/kL4L79YWD2M7vF2jjvcBS79oaB6rK72r8PvWdfGKi/s1vuQy/ZDwZu0uV4bC9oiUAj0DCvSg/dTTHppRmyAyc0Wg4QaBBoEGgEGgQaBBoEGqZg2o5WmLZDywECDTV7aNN2tMC0HVoOEGgQaBBoBBoEGgQaBBqmYdqOVpi2Q8sBAg01e2jTdrTAtB1aDhBoEGgQaAQaBBoEGgQaBJqFZXyUVhgfRcsBAg01e2jjo7TA+ChaDp7Op1G/5SMCPXgfR52M+shWCHQLlvNx1VYINAg0Ag0CDQINAg3/zfgorTA+ipYDBBpq9tDGR2mB8VG0HCDQINAg0Ag0CDQINAg0TMO0Ha0wbYeWAwQaavbQpu1ogWk7tBwg0HPkdv5oql2HfzTWrtsC3abTC3pwnRboNn23oIG+syh/0ZUF+4d9a5a/gSd3k5zQINAINAy/h9bT0QLTdmg5QKChZg9t2o4WmLZDywECDQINAo1Ag0CDQINAwzRM29EK03ZoOUCgoWYPbdqOFpi2Q8sBAg0CDQKNQFPDfj5ObIVAt+CzqEdRn9uK47ViC6r4JAsnNEx3QvsfQlpg2g4tBwg01OyhTdvRAtN2aDlAoEGgQaARaBBoEGgQaJiGaTtaYdoOLQcINNTsoU3b0QLTdmg5QKBBoEGgEWgQaBBoEGiYhmk7WmHaDi0HCDTU7KFN29EC03ZoOUCgQaBBoBFoEGgQaBBomIZpO1ph2g4tBwg01Az0Qdaq7WCgTnY57p/Qa/aFgXqhf0JP8vmGfWGgzubjwxLovfzi3ahle8PAlMxu5vNfS6B384vXorbsDwOzldktbpZAfxO1nxcu945vmHdnMrOjzPC1Euh7UVfzYrnTcT3q/ZFbesyvcZ7M347+uTtXMnyvC+3Fclz3Qn0l6vuoD6JejnrGHjJjz0etZybvRm1HncjXbmWG/5rlKMqdjnNRl6Ley09AWfxF7xsenvk4OOIfpPa6Wa217njXPfyX9zyJ+jrDPBkdaivKhQtRb0R9GfVz1B8OBubMo6j7mdE3o85HPe5e/FOAAQCjHND+oYIYygAAAABJRU5ErkJggg==';
            options.width = '90px';
            options.height = '90px';

        }
        else
        {
            options.is_active = 'active';
        }

        dom.innerHTML = template(options);

        self.$el = $(dom);

        self._setup_event();

        return self;

    },
    _setup_event : function()
    {
        var self = this;

        self.$el.find('[data-role="del-pic"]').on('click',function()
        {
            self.reset();
        });

        self.$el.find('[data-role="upload-flag"]').on('click',function()
        {
            self.$el.trigger('click:upload_flag');
        });
    },
    show : function()
    {
        var self = this;
    },
    hide : function()
    {
        var self = this;
    },
    reset : function()
    {
        var self = this;

        self.render(self.$el[0],{});

        self.pic_list = [];
    },
    get : function()
    {
        var self = this;

        return self.pic_list || [];
    },
    upload_action : function(options)
    {
        var self = this;
        if(App.isPaiApp)
        {

            App.upload_img
            ('multi_img',{
                is_async_upload : 0,
                max_selection : 1,
                is_zip : 1

            },function(data)
            {
                var pic_list=[];

                self.pic_list = [];

                if(data.imgs.length>0)
                {
                    for(var i = 0;i<data.imgs.length;i++)
                    {

                        var img = utility.matching_img_size(data.imgs[i].url,165);

                        pic_list[0] = img;
                    }


                }

                self.pic_list = pic_list;

                options.callback && options.callback.call(this,pic_list);
            });


        }
        else
        {
            alert('上传图片按钮异常');

            window.location.href = window.location.href;

            return;

            self.pic_list = [];

            var pic_list =
                [
                    'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_640.jpg'
                ];

            self.pic_list = pic_list;

            options.callback && options.callback.call(this,pic_list);

        }

    }


}; 
});