
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var collection = require('./collection');
    var search_view = require('./view');

    page_control.add_page([function(){
        return{
           title:'搜索结果',
            route:{
                'search_result' : 'search_result'
            },
            dom_not_cache : true,
            ignore_exist : true,
            transition_type : null,
            page_init : function(apps_page_view, apps_params,search_info){

                this.search_view = new search_view({
                    templateModel :
                    {
                        id : search_info.id,
                        tag : search_info.tag
                    },
                    collection : new collection,
                    parentNode: this.$el
                }).set_search_info(search_info).render();

            },
            page_before_show : function()
            {

            },
            page_show : function()
            {

            },
            page_before_hide : function()
            {

            }

        }
    }]);
})