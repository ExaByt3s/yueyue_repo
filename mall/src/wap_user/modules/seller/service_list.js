/**
 * Created by hudingwen on 15/8/25.
 */

// ========= 模块引入 ========= 
var $ = require('zepto');
var header = require('../common/widget/header/main');
var utility = require('../common/utility/index');
var yue_ui = require('../yue_ui/frozen');
var abnormal = require('../common/widget/abnormal/index');
var LZ = require('../common/lazyload/lazyload');
var _self = $({});



// 渲染头部
_self.header_obj = header.init
({
    ele : $("#global-header"), //头部渲染的节点
    title:"服务列表",
    header_show : true , //是否显示头部
    right_icon_show : false, //是否显示右边的按钮
    share_icon : 
    {
        show :false,  //是否显示分享按钮icon
        content:""
    },
    omit_icon :
    {
        show :false,  //是否显示三个圆点icon
        content:""
    },
    show_txt :
    {
        show :true,  //是否显示文字
        content:"编辑"  //显示文字内容
    }
});
var SELLER_AJAX_URL = window.$__ajax_domain+'get_sell_services_list.php';
var page_params = window.__page_params;
var template  = __inline('../../templates/default/wap/seller/service_list_item.tmpl');
var list_item_class = require('../../modules/list/list.js'); 

var list_obj = new list_item_class(
    {
        //渲染目标
        ele : $('#render_ele'),
        //请求地址
        url : SELLER_AJAX_URL,
        //传递参数
        params : page_params,
        //模板
        template : template,
        //lz是否开启参数
        is_open_lz_opts : false  
    });

