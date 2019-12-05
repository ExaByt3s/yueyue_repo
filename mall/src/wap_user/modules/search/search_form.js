var $ = require('zepto');
var utility = require('../common/utility/index');
var search_tpl = __inline("./search_form.tmpl");
console.log('1');
//search渲染容器
var search_form_class = function(options)
{
    var self = this;
    self.options = options ||{};
    console.log('2');

    self.init(options);
}
search_form_class.prototype =
{
    init : function(options)
    {
        var self = this;

        self.options = options;
        self.$el_form = options.ele || {} ;
        console.log('hhhhh');
        self.render();
        //var search_obj = require('./search.js');
        search_obj_class();
        self.setup_event();
    },
    render : function()
    {
        var self = this;
        var html_str = search_tpl();
        self.$el_form.html(html_str);
    },
    setup_event : function()
    {
        var self = this;
        //返回按钮
        $('[data-role="page-back"]').on('click',function()
        {
            if(document.referrer)
            {
                window.history.back();
            }
            else
            {
                window.location.href = "../index.php" ;
            }
        });
        document.body.addEventListener('touchstart',function(){});


        // 服务的热门标签数组
        self.service_tags =  window.__service_tags || [];
        self.service_btn_list = [];
        // 商家的热门标签数组
        self.seller_tags =  window.__seller_tags || [];
        self.seller_btn_list = [];
        //type_id参数
        self.type_id = window.__type_id;


        //======== 循环重新整合标签格式 ========
        if(self.service_tags.length>0)
        {
            for(var i= 0 ;i<self.service_tags.length;i++)
            {
                var list_obj = {};
                list_obj.name = self.service_tags[i];
                self.service_btn_list.push(list_obj);
            }
        }

        if(self.seller_tags.length>0)
        {
            for(var i= 0 ;i<self.seller_tags.length;i++)
            {
                var list_obj = {};
                list_obj.name = self.seller_tags[i];
                self.seller_btn_list.push(list_obj);
            }
        }
        //======== 循环重新整合标签格式 ========

        //搜索服务类型切换

        self.type = self.$search_type_init.val() || "goods";

        init_logic_data();

        // hudw hack android
        if(self.$search_type_txt.val() == 'goods')
        {
            self.$search_type_txt.val('服务');
        }

        self.$type_btn.on('click',function()
        {
            var type_str  = '服务';

            self.type = $(this).attr('data-type');

            self.$type_select.addClass('fn-hide');

            if(self.type == 'seller')
            {
                type_str = '商家';

                var lst_key = "seller-history";

                // 显示商家的热门、历史标签
                self.$hot_seller_content.removeClass('fn-hide');
                self.$history_seller_content.removeClass('fn-hide');
                self.$hot_service_content.addClass('fn-hide');
                self.$history_service_content.addClass('fn-hide');

            }
            else if(self.type == 'goods')
            {
                type_str = '服务';

                var lst_key = "service-history";

                // 显示服务的热门、历史标签
                self.$hot_service_content.removeClass('fn-hide');
                self.$history_service_content.removeClass('fn-hide');
                self.$hot_seller_content.addClass('fn-hide');
                self.$history_seller_content.addClass('fn-hide');

                var history_btn = utility.storage.get(self.type_id+"_"+lst_key) || [];

            }
            else
            {
                // todo 全部
            }

            init_logic_data();

            self.$search_type_txt.val(type_str);




        });

        //搜索添加历史

        self.$search_btn.on('click',function()
        {
            var searchText = self.$search_text.val();
            if(!$.trim(searchText))
            {
                alert("请输入内容");
                console.log('请输入内容');
                return;
            }
            goto_search(searchText,self.type);
            self.$search_text.val("");
        });

        //服务类型切换显隐
        $('[data-role="search-type"]').on('click',function(ev)
        {
            ev.stopPropagation();
            if(self.$type_select.hasClass('fn-hide'))
            {
                self.$type_select.removeClass('fn-hide');
            }else
            {
                self.$type_select.addClass('fn-hide');
            }
        });
        $(document).on('click',function(ev)
        {
            self.$type_select.addClass('fn-hide');
        });


        /**
         * 初始化页面
         */
        function init_logic_data()
        {
            var hot_key = null;
            if(self.type == 'goods')
            {
                var lst_key = "service-history";
                var hot_key = self.service_tags;


                self.$history_service_content.removeClass('fn-hide');
                self.$history_seller_content.addClass('fn-hide');
            }
            else if(self.type == 'seller')
            {
                var lst_key = "seller-history";
                var hot_key = self.seller_tags;

                self.$history_seller_content.removeClass('fn-hide');
                self.$history_service_content.addClass('fn-hide');
            }

            var history_btn = utility.storage.get(self.type_id+"_"+lst_key) || [];

            if(history_btn.length > 0)
            {
                self.$empty.removeClass('fn-hide');
                self.$history.removeClass('fn-hide');
            }
            else
            {
                self.$empty.addClass('fn-hide');
                self.$history.addClass('fn-hide');
            }
            //

            if(hot_key.length > 0)
            {
                self.$hot.removeClass('fn-hide');
            }
            else
            {
                self.$hot.addClass('fn-hide');
            }
        }

        function goto_search(name,type)
        {
            if(type == 'goods')
            {
                var lst_key = "service-history";
            }
            else if(type == 'seller')
            {
                var lst_key = "seller-history";
            }

            var history_btn = utility.storage.get(self.type_id+"_"+lst_key) || [];

            var repeat = false;
            for(var n = 0;n<history_btn.length;n++){
                if(history_btn[n].name==name){
                    repeat=true;
                }
            }
            if(!repeat)
            {
                if(history_btn.length>=10)
                {
                    history_btn.pop();
                }
                history_btn.unshift({name: name});



                utility.storage.set(self.type_id+"_"+lst_key, history_btn);
            }

            self.$search_type_init.val(self.type);

            var project_url = window.__index_url_link;
            project_url = project_url.replace('index.php','');

            window.location.href = project_url+"search/?"+"search_type="+encodeURIComponent(type)+"&keywords="+name+"&type_id="+self.type_id;
        }
    }
};
exports.init = function(options)
{
    console.log('4');
    return new search_form_class(options);
};
//search方法
function search_obj_class()
{
    var self = this;
    //搜索事件
    self.$search_merchant = $('[data-role="merchant"]');
    self.$search_service = $('[data-role="service"]');
    self.$hot_service_content = $('[data-role="hot-service-content"]');
    self.$hot_seller_content = $('[data-role="hot-seller-content"]');
    self.$history = $('[data-role="history"]');
    self.$history_service_content = $('[data-role="history-service-content"]');
    self.$history_seller_content = $('[data-role="history-seller-content"]');
    self.$empty = $('[data-role="empty"]');
    self.$type_btn = $('[data-role="type-btn"]');
    self.$search_btn = $('[data-role="right-btn"]');
    self.$search_text = $('[data-role="search-text"]');
    self.$search_type_txt = $('.search-type-btn');
    self.$type_select = $('#type-select');
    self.$hot = $('[data-role="hot"]');
    self.$search_type_init = $('#search-type-init');
    /**
     * 搜索子项类
     */
    var search_items_child_class = function(options)
    {
        var self = this;

        options = options || {};

        self.options = options;

        self.init();
    };
    search_items_child_class.prototype =
    {
        init : function()
        {
            var self = this;

            var data = self.options.data || [];
            var key = self.options.key || '';


            self.$el = self.options.$el || {};

            if(self.type == 'goods')
            {
                self.$el.lst_key = "service-history";
            }
            else if(self.type == 'seller')
            {
                self.$el.lst_key = "seller-history";
            }

            var history_btn = utility.storage.get(self.type_id+"_"+self.$el.lst_key) || [];

            var btn_list_str = self.render_items(data);



            // 插入内容
            self.$el.html(btn_list_str);

            self.$btn = self.$el.find('[data-role="btn"]');
            self.$btn.on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);
                var name = $cur_btn.attr('data-code-name');

                goto_search(name,self.type);
            });

            //清除搜索历史事件
            self.$empty.on('click',function()
            {
                if(self.type == 'goods')
                {
                    var lst_key = "service-history";
                    self.$history.addClass('fn-hide');

                }
                else if(self.type == 'seller')
                {
                    var lst_key = "seller-history";
                    self.$history.addClass('fn-hide');
                }

                utility.storage.remove(self.type_id+"_"+lst_key);

                self.setup_empty();
            });



        },
        render_items : function(data)
        {
            var self = this;

            var html_str = search_tpl(data);

            // 模板渲染，返回数据 html字符串

            return html_str;
        },

        setup_search_click : function()
        {

        },

        setup_empty : function()
        {
            self.$empty.addClass("fn-hide");

        }
    };

    // 服务热门子项
    self.search_hot_service_items_child_obj = new search_items_child_class
    ({
        key : 'hot_service',
        data :
        {
            data :
            {
                btn_list : self.service_btn_list
            }
        },
        title : '',
        $el : self.$hot_service_content
    });

    // 服务历史子项
    self.search_history_service_items_child_obj = new search_items_child_class
    ({
        key : 'history_service',
        data :
        {
            data :
            {
                btn_list:utility.storage.get(self.type_id+"_"+"service-history") || []
            }
        },
        title : '',
        $el : self.$history_service_content
    });

    // 商家热门子项
    self.search_hot_seller_items_child_obj = new search_items_child_class
    ({
        key : 'hot_seller',
        data :
        {
            data :
            {
                btn_list : self.seller_btn_list
            }
        },
        title : '',
        $el : self.$hot_seller_content
    });

    // 商家历史子项
    self.search_history_seller_items_child_obj = new search_items_child_class
    ({
        key : 'history_seller',
        data :
        {
            data :
            {
                btn_list:utility.storage.get(self.type_id+"_"+"seller-history") || []
            }
        },
        title : '',
        $el : self.$history_seller_content
    });

}

