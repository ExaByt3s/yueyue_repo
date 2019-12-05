
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var collection = require('./collection');
    var search_view = require('./view');

    page_control.add_page([function(){
        return{
           title:'搜索',
            route:{
                'search' : 'search'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : 'slide',
            page_init : function(){

                this.search_view = new search_view({
                    collection : new collection,
                    parentNode: this.$el
                }).render();

            },
            page_before_show : function()
            {

            },
            page_show : function()
            {

            },
            page_before_hide : function()
            {

            },
            page_hide : function(){
                //离开页面时删除搜索记录弹窗；
                this.search_view.del_search_history_dom();
            }

        }
    }]);
})