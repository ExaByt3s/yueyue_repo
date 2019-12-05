/**
 * Created by hudingwen on 15/6/4.
 * 上传组件
 */


/**
 * @require ./main.scss
 *
 */

var $ = require('zepto');
var App = require('../../I_APP/I_APP');
var utility = require('../../utility/index');
var frozen = require('../../../yue_ui/frozen');
var template  = __inline('./main.tmpl');

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
            options.default_img_url = __inline('../../../../image/pai/add.png');
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

		self.$el.on('success:web_upload_img',function(args,binary_result)
			{

				var oMyForm = new FormData();

				oMyForm.append("opus", self.submit_file);
				oMyForm.append("poco_id", utility.login_id);


				//模拟formData对象
				var oMyForm = new FormData();
				
				oMyForm.append("opus", self.submit_file);
				oMyForm.append("poco_id", utility.login_id);

				var oReq = new XMLHttpRequest();

				oReq.open("POST", 'http://sendmedia-w.yueus.com:8079/upload.cgi', true);

				oReq.onreadystatechange = ShowResult;

				oReq.send(oMyForm); 

				window.$loading = $.loading
				({
					content:'发送中...'
				});

				function ShowResult()
				{
					if(oReq.readyState==4)
					{
					   window.$loading.loading("hide");	 

					   if(oReq.status==200)
					   {
						   var response_data = JSON.parse(oReq.response);

						   console.log(response_data)	
							   
						   self.pic_list = response_data.url;

				           self.$el.trigger('upload:success',self.pic_list);

					   }

					}
				}

				
			})
		
		var upb = document.getElementById("uploadBtn");
		upb && upb.addEventListener("change", function()
		{
			var file = this.files[0];

			self.submit_file = file;

			var reader = new FileReader();

			reader.onload = function()
			{
				console.log(this);

				var binary_reader = new FileReader();

				binary_reader.readAsArrayBuffer(file);

				binary_reader.onload = function()
				{
					binary_result = this.result

					self.$el.trigger('success:web_upload_img',binary_result);

					console.log(binary_result);

				}

			};
			reader.readAsDataURL(file);//base64
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
     

    }


};