/**
 * 模特卡列表
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');

    var utility = require('../../common/utility');
    var templateHelpers = require('../../common/template-helpers');
    var item_tpl = require('./tpl/item.handlebars');

    var view_width = utility.get_view_port_width();

    module.exports = View.extend
    ({
        attrs :
        {
            template : item_tpl
        },
        events :
        {

        },
        _parseElement : function()
        {
            var self = this;

            var template_model = self.get('templateModel');

            var custom_model = [];
            var img_list = [];
            var tpl_type = template_model.tpl_type;

            switch(tpl_type)
            {
                /**
                 * 单独出图
                 */
                case 'one':
                    for(var i= 0,len=template_model.tpl_data.length;i<len;i++)
                    {
                        var temp = template_model.tpl_data[i];

                        var size = self._get_size_by_type(temp.type);

                        temp.width = size.width;
                        temp.height = size.height;
                        temp.no_use_lazyload = self.get('no_use_lazyload') || 0

                        if((i-1)%3 ==0)
                        {
                            temp.mid_class = 'mid';
                        }

                        img_list.push(temp);

                    }

                    custom_model.push
                    ({
                        img_list : img_list
                    });

                    break;
                /**
                 *  分组出图 （标题+组图）
                 */
                case 'group':
                    for(var j=0;j<template_model.tpl_data.length;j++)
                    {
                        var temp_img_list = template_model.tpl_data[j].list;

                        var temp_title = template_model.tpl_data[j].title;

                        for(var i= 0,len=temp_img_list.length;i<len;i++)
                        {
                            var temp = temp_img_list[i];

                            var size = self._get_size_by_type(temp.type);

                            temp.width = size.width;
                            temp.height = size.height;
                            temp.no_use_lazyload = self.get('no_use_lazyload') || 0

                            if((i-1)%3 == 0)
                            {
                                temp.mid_class = 'mid';
                            }

                            img_list.push(temp);

                        }

                        custom_model.push
                        ({
                            img_list : temp_img_list,
                            title : temp_title
                        });

                    }
                    break;
                case '1l2r':
                    for(var i= 0,len=template_model.tpl_data.length;i<len;i++)
                    {
                        var temp = template_model.tpl_data[i];

                        var size = self._get_size_by_type(temp.type);

                        temp.width = size.width;
                        temp.height = size.height;
                        temp.no_use_lazyload = self.get('no_use_lazyload') || 0

                        if((i+3)%3==0)
                        {
                            temp.mid_class = 'right';
                        }
                        if((i-1)%3==0)
                        {
                            temp.mid_class = 'bottom';
                        }


                        img_list.push(temp);

                    }

                    custom_model.push
                    ({
                        img_list : img_list
                    });

                    break;
                case '1l1m1r':
                    for(var i= 0,len=template_model.tpl_data.length;i<len;i++)
                    {
                        var temp = template_model.tpl_data[i];

                        var size = self._get_size_by_type(temp.type);

                        temp.width = size.width;
                        temp.height = size.height;
                        temp.no_use_lazyload = self.get('no_use_lazyload') || 0

                        if((i-1)%3==0)
                        {
                            temp.mid_class = 'mid-l-r-b';
                        }
                        else
                        {
                            temp.mid_class = 'bottom';
                        }


                        img_list.push(temp);

                    }

                    custom_model.push
                    ({
                        img_list : img_list
                    });

                    break;
                case '1double_others_one':
                    for(var i= 0,len=template_model.tpl_data.length;i<len;i++)
                    {
                        var temp = template_model.tpl_data[i];

                        var size = self._get_size_by_type(temp.type);

                        temp.width = size.width;
                        temp.height = size.height;
                        temp.no_use_lazyload = self.get('no_use_lazyload') || 0

                        if(len == 3)
                        {

                            if((i+3)%3==0)
                            {
                                temp.mid_class = 'right';
                            }
                            if((i-1)%3==0)
                            {
                                temp.mid_class = 'bottom';
                            }
                        }
                        else
                        {
                            if((i-1)%3==0)
                            {
                                temp.mid_class = 'mid';
                            }
                            else
                            {
                                temp.mid_class = 'bottom';
                            }

                        }

                        img_list.push(temp);

                    }


                    custom_model.push
                    ({
                        img_list : img_list
                    });

                    break;
                case 'all_ori_img':
                    for(var i= 0,len=template_model.tpl_data.length;i<len;i++)
                    {
                        var temp = template_model.tpl_data[i];

                        var size = self._get_size_by_type(temp.type);

                        temp.no_grid_size = true;

                        temp.width = '100%';
                        temp.height = '100%';
                        temp.no_use_lazyload = self.get('no_use_lazyload') || 0



                        img_list.push(temp);

                    }

                    console.log(img_list)


                    custom_model.push
                    ({
                        img_list : img_list
                    });

                    break;
            }

            self.set('templateModel', {custom_model:custom_model});

            View.prototype._parseElement.apply(self);


        },
        _setup_events : function()
        {

        },
        _get_size_by_type : function(type)
        {
            return utility.get_grid_size(type)

        },
        setup : function()
        {

        },
        list : function()
        {
            var self = this;

            return self.$el;
        }
    });
});