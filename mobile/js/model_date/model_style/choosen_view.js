/**
 * Created by nolestLam on 2014/8/22.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var choosen_btn = require('../model_style/tpl/choosen.handlebars');

    var choosen_view = View.extend
    ({
        attrs:
        {
            template: choosen_btn
        },
        events :
        {
            
        },
        _parseElement : function()
        {
            var self = this;

            var template_model = self.get('templateModel');

            console.log(template_model)

            self.set('templateModel', template_model);

            View.prototype._parseElement.apply(self);
        },
        _setup_events : function()
        {

        },
        setup : function()
        {
            var self = this;

            self._setup_events();


        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }
    });
    module.exports = choosen_view;
});