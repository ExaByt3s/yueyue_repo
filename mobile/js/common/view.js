define(function(require, exports, module)
{
	var $ = require('$');
	var Backbone = require('backbone');

    var Handlebars = require('handlebars');

    // 实例缓存
    var cachedInstances = {};

    // 编译过的模版缓存
    var compiledTemplates = {};

    /**
     * 默认属性
     * @type {{id: null, className: null, template: null, templateModel: null, parentNode: HTMLElement}}
     */
    var defaultAttr =
    {
        // 基本属性
        id: null,
        className: null,

        // 默认模板
        template: null,

        // 默认模版数据模型
        templateModel: null,

        // 组件的默认父节点
        parentNode: document.body
    };

    var View = Backbone.View.extend
    ({
        /**
         * Handlebars 的 helpers
         */
        templateHelpers: null,

        /**
         * Handlebars 的 partials
         */
        templatePartials: null,

        /**
         * 渲染Handlebars模版
         * @param template
         * @param templateModel
         * @returns {*}
         * @private
         */
        _renderElement: function(template, templateModel)
        {
            var self = this;

            template || (template = self.get('template'));

            // 模版套入的数据
            templateModel || (templateModel = self.get('templateModel')) || (templateModel = {});
            if (templateModel.toJSON) {
                templateModel = templateModel.toJSON();

                console.log(templateModel)
            }

            // handlebars runtime
            if (isFunction(template)) {
                return template(templateModel, {
                    helpers: self.templateHelpers,
                    partials: compilePartial(self.templatePartials)
                });
            }

            var helpers = self.templateHelpers;
            var partials = self.templatePartials;

            // 注册 helpers
            if (helpers) {
                var helper;
                for (helper in helpers) {
                    if (helpers.hasOwnProperty(helper)) {
                        Handlebars.registerHelper(helper, helpers[helper]);
                    }
                }
            }
            // 注册 partials
            if (partials) {
                var partial;
                for (partial in partials) {
                    if (partials.hasOwnProperty(partial)) {
                        Handlebars.registerPartial(partial, partials[partial]);
                    }
                }
            }

            var compiledTemplate = compiledTemplates[template];
            if (!compiledTemplate) {
                compiledTemplate = compiledTemplates[template] = Handlebars.compile(template);
            }

            // 生成 html
            var html = compiledTemplate(templateModel);

            // 卸载 helpers
            if (helpers) {
                var helper;
                for (helper in helpers) {
                    if (helpers.hasOwnProperty(helper)) {
                        delete Handlebars.helpers[helper];
                    }
                }
            }
            // 卸载 partials
            if (partials) {
                var partial;
                for (partial in partials) {
                    if (partials.hasOwnProperty(partial)) {
                        delete Handlebars.partials[partial];
                    }
                }
            }

            return html;
        },

        /**
         * 从模版上制造element
         * @private
         */
        _parseElementFromTemplate: function()
        {
            var self = this;

            // template 支持 id 选择器
            var $template, template = self.get('template');
            if (/^#/.test(template) &&
                ($template = $(template))) {

                template = $template.html();
                self.set('template', template);
            }

            self.el = self._renderElement(template);
        },

        /**
         * 覆盖方法(Backbone.View._ensureElement)
         * @private
         */
        _ensureElement: function() {},

        /**
         * 构建element
         * @private
         */
        _parseElement: function()
        {
            var self = this;

            var isTemplate = !self.el && self.get('template');

            // 未传入 el 时，从 template 构建
            if (isTemplate) {
                self._parseElementFromTemplate();
                self.setElement(self.el, false);

                // 是否由 template 初始化
                self._isTemplate = true;
            } else {
                Backbone.View.prototype._ensureElement.apply(self);
            }

            // 如果对应的 DOM 元素不存在，则报错
            if (!self.$el || !self.$el[0]) {
                throw new Error('element is invalid');
            }
        },

        /**
         * 让 element 与 View 实例建立关联
         * @private
         */
        _stamp: function()
        {
            var self = this;
            var cid = self.cid;

            cachedInstances[cid] = self;
        },

        _initAttrs: function(options)
        {
            var self = this;

            // 组件属性与默认属性融合
            self.attrs = $.extend(true, {}, defaultAttr, (self.attrs || {}), options);

            //console.log({},defaultAttr,self.attrs,(self.attrs || {}),options,self.attrs);

        },

        /**
         * 初始化
         * @param options
         * @returns {View}
         */
        initialize: function(options)
        {
            options || (options = {});

            var self = this;

            // 初始化属性
            self._initAttrs(options);

            // 兼容 backbone.view
            var elementId = self.get('id');
            if (elementId) {
                self.id = elementId;
            }

            var elementClassName = self.get('className');
            if (elementClassName) {
                self.className = elementClassName;
            }

            // 构建 element
            self._parseElement();

            // 子类自定义的初始化
            self.setup();

            // 保存实例信息
            self._stamp();

            return self;
        },

        get: function(name)
        {
            return this.attrs[name] || null;
        },

        set: function(name, value)
        {
            var self = this;

            self.attrs[name] = value;

            return self;
        },

        /**
         * 提供给子类覆盖的初始化方法
         */
        setup: function()
        {
        },

        /**
         * 渲染
         * @returns {View}
         */
        render: function(type)
        {
            var self = this;

            if (!self.rendered) {
                self.rendered = true;
            }

            var parentNode = self.get('parentNode');
            if (parentNode && !isInDocument(self.el)) {
                if(type && type == 'prepend'){
                    parentNode.prepend(self.$el);
                }else{
                    self.$el.appendTo(parentNode);
                }
            }

            return self;
        },

        /**
         * 移除
         * @returns {View}
         */
        remove: function()
        {
            var self = this;

            delete cachedInstances[self.cid];

            // 注销所有事件,Zepto的remove只是单纯移除DOM
            self.undelegateEvents().$el.off();

            Backbone.View.prototype.remove.apply(self);

            return self;
        },

        /**
         * 销毁实例
         * @returns {View}
         */
        destroy: function()
        {
            var self = this;

            for (var p in self) {
                if (self.hasOwnProperty(p)) {
                    delete self[p];
                }
            }

            // 此方法只能运行一次
            self.destroy = function() {};

            return self;
        }
    });

    module.exports = View;

    // For memory leak
    $(window).unload(function() {
        var cid, instance;
        for (cid in cachedInstances) {
            instance = cachedInstances[cid];
            instance && instance.destroy();
        }
    });

    // Helps
    // ------------------------------

    function isFunction(o)
    {
        return typeof o === 'function';
    }

    function isInDocument(element)
    {
        return $.contains(document.documentElement, element);
    }

    function compilePartial(partials)
    {
        if (!partials) return {};

        var result = {},
            name, partial;
        for (name in partials) {
            partial = partials[name];
            result[name] = isFunction(partial) ? partial : Handlebars.compile(partial);
        }
        return result;
    }

	module.exports = View;
});