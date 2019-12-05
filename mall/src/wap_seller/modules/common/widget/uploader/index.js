/**
 * Created by hudingwen on 15/6/4.
 * �ϴ����
 */


/**
 * @require ./main.scss
 *
 */

var $ = require('zepto');
var App = require('../../I_APP/I_APP');
var utility = require('../../utility/index');
var template  = __inline('./main.tmpl');

module.exports =
{
    render: function (dom,options)
    {
        // tpl��׺���ļ�Ҳ��������ģ��Ƕ�룬���handlebars
        // tpl�ļ�������ģ��������ܣ�Ƕ���ֻ����Ϊ�ַ���ʹ
        // �ã�tpl�ļ�Ƕ��֮ǰ���Ա����ѹ���������С��
        // handlebars����ȱ����Ӧ��ѹ����������ʱ������Ԥ
        // ����׶���ѹ����ѡ��tpl����handlebars��������ѡ
        // ��

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
            alert('�ϴ�ͼƬ��ť�쳣');

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