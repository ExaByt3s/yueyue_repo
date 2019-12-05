/**
 * 首页 - 城市选择 基础页面框架
 * 汤圆 2014.8.18
 * @param  {[type]} require 
 * @param  {[type]} exports 
 * @param  {[type]} module  
 * @return {[type]}         
 */
define(function(require, exports, module) 
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var location_view = require('../location/view');
    var location_collection = require('../location/collection');
    var I_App = require('../common/I_APP');
    var utility = require('../common/utility');

	page_control.add_page([function()
    {
        return{
            title : '城市选择',
            route :
            {
                'location(/:from)' : 'location'
            },
            dom_not_cache : false,
            transition_type : 'slide',
            initialize : function()
            {



            },
            page_init : function(page_view,route_params_arr,route_params_obj)
            {
                var self = this;

                self.collection = new location_collection();

                self.location_view_obj = new location_view
                ({
                    from : route_params_arr[0],
                    collection : self.collection,
                    parentNode : self.$el
                }).render();
                console.log("2")
            },
            page_before_show : function()
            {
                var self = this;

                if(I_App.isPaiApp)
                {
                    //I_App.remove_front();

                    I_App.get_gps({},function(data)
                    {
                        self.collection.get_location_by_gps({long:data.long,lat:data.lat})

                        //alert("1" + data.long,data.lat)
                    })
                }

                self.collection
                    .on('success:get_location_fetch',function(response,options)
                    {
                        var city = response.result_data.data.city;

                        var location_id = response.result_data.data.location_id;

                        self.location_view_obj.$('[data-role="now-city"]')
                            .attr('data-location-id',location_id)
                            .attr('data-location-name',city)

                        self.location_view_obj.$('[data-role="now-city-text"]').html(city)

                    })
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

