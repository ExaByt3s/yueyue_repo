define('yue_ui/selectbox/popup', function(require, exports, module){ /*!
 * PopupJS
 * Date: 2014-11-09
 * https://github.com/aui/popupjs
 * (c) 2009-2014 TangBin, http://www.planeArt.cn
 *
 * This is licensed under the GNU LGPL, version 2.1 or later.
 * For details, see: http://www.gnu.org/licenses/lgpl-2.1.html
 */



var $ = require('components/jquery/jquery.js');

var _count = 0;
var _isIE6 = !('minWidth' in $('html')[0].style);
var _isFixed = !_isIE6;


function Popup () {

    this.destroyed = false;


    this.__popup = $('<div />')
    /*ʹ�� <dialog /> Ԫ�ؿ��ܵ��� z-index ��Զ�ö�������(chrome)*/
    .css({
        display: 'none',
        position: 'absolute',
        /*
        left: 0,
        top: 0,
        bottom: 'auto',
        right: 'auto',
        margin: 0,
        padding: 0,
        border: '0 none',
        background: 'transparent'
        */
        outline: 0
    })
    .attr('tabindex', '-1')
    .html(this.innerHTML)
    .appendTo('body');


    this.__backdrop = this.__mask = $('<div />')
    .css({
        opacity: .7,
        background: '#000'
    });


    // ʹ�� HTMLElement ��Ϊ�ⲿ�ӿ�ʹ�ã������� jquery ����
    // ͳһ�Ľӿ�����δ�� Popup ��ֲ������ DOM ����
    this.node = this.__popup[0];
    this.backdrop = this.__backdrop[0];

    _count ++;
}


$.extend(Popup.prototype, {
    
    /**
     * ��ʼ������¼����� show()��showModal() ִ��
     * @name Popup.prototype.onshow
     * @event
     */

    /**
     * �ر��¼����� close() ִ��
     * @name Popup.prototype.onclose
     * @event
     */

    /**
     * ����ǰ�¼����� remove() ǰִ��
     * @name Popup.prototype.onbeforeremove
     * @event
     */

    /**
     * �����¼����� remove() ִ��
     * @name Popup.prototype.onremove
     * @event
     */

    /**
     * �����¼����� reset() ִ��
     * @name Popup.prototype.onreset
     * @event
     */

    /**
     * �����¼����� foucs() ִ��
     * @name Popup.prototype.onfocus
     * @event
     */

    /**
     * ʧ���¼����� blur() ִ��
     * @name Popup.prototype.onblur
     * @event
     */

    /** ���� DOM �ؽڵ�[*] */
    node: null,

    /** ���� DOM �ڵ�[*] */
    backdrop: null,

    /** �Ƿ����̶���λ[*] */
    fixed: false,

    /** �ж϶Ի����Ƿ�ɾ��[*] */
    destroyed: true,

    /** �ж϶Ի����Ƿ���ʾ */
    open: false,

    /** close ����ֵ */
    returnValue: '',

    /** �Ƿ��Զ��۽� */
    autofocus: true,

    /** ���뷽ʽ[*] */
    align: 'bottom left',

    /** �ڲ��� HTML �ַ��� */
    innerHTML: '',

    /** CSS ���� */
    className: 'ui-popup',

    /**
     * ��ʾ����
     * @param   {HTMLElement, Event}  ָ��λ�ã���ѡ��
     */
    show: function (anchor) {

        if (this.destroyed) {
            return this;
        }

        var that = this;
        var popup = this.__popup;
        var backdrop = this.__backdrop;

        this.__activeElement = this.__getActive();

        this.open = true;
        this.follow = anchor || this.follow;


        // ��ʼ�� show ����
        if (!this.__ready) {

            popup
            .addClass(this.className)
            .attr('role', this.modal ? 'alertdialog' : 'dialog')
            .css('position', this.fixed ? 'fixed' : 'absolute');

            if (!_isIE6) {
                $(window).on('resize', $.proxy(this.reset, this));
            }

            // ģ̬���������
            if (this.modal) {
                var backdropCss = {
                    position: 'fixed',
                    left: 0,
                    top: 0,
                    width: '100%',
                    height: '100%',
                    overflow: 'hidden',
                    userSelect: 'none',
                    zIndex: this.zIndex || Popup.zIndex
                };


                popup.addClass(this.className + '-modal');


                if (!_isFixed) {
                    $.extend(backdropCss, {
                        position: 'absolute',
                        width: $(window).width() + 'px',
                        height: $(document).height() + 'px'
                    });
                }


                backdrop
                .css(backdropCss)
                .attr({tabindex: '0'})
                .on('focus', $.proxy(this.focus, this));

                // ���� tab �Ľ������
                this.__mask = backdrop
                .clone(true)
                .attr('style', '')
                .insertAfter(popup);

                backdrop
                .addClass(this.className + '-backdrop')
                .insertBefore(popup);

                this.__ready = true;
            }


            if (!popup.html()) {
                popup.html(this.innerHTML);
            }
        }


        popup
        .addClass(this.className + '-show')
        .show();

        backdrop.show();


        this.reset().focus();
        this.__dispatchEvent('show');

        return this;
    },


    /** ��ʾģ̬���㡣�����μ� show() */
    showModal: function () {
        this.modal = true;
        return this.show.apply(this, arguments);
    },
    
    
    /** �رո��� */
    close: function (result) {
        
        if (!this.destroyed && this.open) {
            
            if (result !== undefined) {
                this.returnValue = result;
            }
            
            this.__popup.hide().removeClass(this.className + '-show');
            this.__backdrop.hide();
            this.open = false;
            this.blur();// �ָ����㣬�չ˼��̲������û�
            this.__dispatchEvent('close');
        }
    
        return this;
    },


    /** ���ٸ��� */
    remove: function () {

        if (this.destroyed) {
            return this;
        }

        this.__dispatchEvent('beforeremove');
        
        if (Popup.current === this) {
            Popup.current = null;
        }


        // �� DOM ���Ƴ��ڵ�
        this.__popup.remove();
        this.__backdrop.remove();
        this.__mask.remove();


        if (!_isIE6) {
            $(window).off('resize', this.reset);
        }


        this.__dispatchEvent('remove');

        for (var i in this) {
            delete this[i];
        }

        return this;
    },


    /** ����λ�� */
    reset: function () {

        var elem = this.follow;

        if (elem) {
            this.__follow(elem);
        } else {
            this.__center();
        }

        this.__dispatchEvent('reset');

        return this;
    },


    /** �ø����ȡ���� */
    focus: function () {

        var node = this.node;
        var popup = this.__popup;
        var current = Popup.current;
        var index = this.zIndex = Popup.zIndex ++;

        if (current && current !== this) {
            current.blur(false);
        }

        // ��齹���Ƿ��ڸ�������
        if (!$.contains(node, this.__getActive())) {
            var autofocus = popup.find('[autofocus]')[0];

            if (!this._autofocus && autofocus) {
                this._autofocus = true;
            } else {
                autofocus = node;
            }

            this.__focus(autofocus);
        }

        // ���õ��Ӹ߶�
        popup.css('zIndex', index);
        //this.__backdrop.css('zIndex', index);

        Popup.current = this;
        popup.addClass(this.className + '-focus');

        this.__dispatchEvent('focus');

        return this;
    },


    /** �ø���ʧȥ���㡣�������˻���֮ǰ��Ԫ�أ��չ������ϰ��û� */
    blur: function () {

        var activeElement = this.__activeElement;
        var isBlur = arguments[0];


        if (isBlur !== false) {
            this.__focus(activeElement);
        }

        this._autofocus = false;
        this.__popup.removeClass(this.className + '-focus');
        this.__dispatchEvent('blur');

        return this;
    },


    /**
     * ����¼�
     * @param   {String}    �¼�����
     * @param   {Function}  ��������
     */
    addEventListener: function (type, callback) {
        this.__getEventListener(type).push(callback);
        return this;
    },


    /**
     * ɾ���¼�
     * @param   {String}    �¼�����
     * @param   {Function}  ��������
     */
    removeEventListener: function (type, callback) {
        var listeners = this.__getEventListener(type);
        for (var i = 0; i < listeners.length; i ++) {
            if (callback === listeners[i]) {
                listeners.splice(i--, 1);
            }
        }
        return this;
    },


    // ��ȡ�¼�����
    __getEventListener: function (type) {
        var listener = this.__listener;
        if (!listener) {
            listener = this.__listener = {};
        }
        if (!listener[type]) {
            listener[type] = [];
        }
        return listener[type];
    },


    // �ɷ��¼�
    __dispatchEvent: function (type) {
        var listeners = this.__getEventListener(type);

        if (this['on' + type]) {
            this['on' + type]();
        }

        for (var i = 0; i < listeners.length; i ++) {
            listeners[i].call(this);
        }
    },


    // ��Ԫ�ذ�ȫ�۽�
    __focus: function (elem) {
        // ��ֹ iframe ������Ȩ�ޱ���
        // ��ֹ IE ���ɼ�Ԫ�ر���
        // try {
        //     // ie11 bug: iframe ҳ��������������
        //     if (this.autofocus && !/^iframe$/i.test(elem.nodeName)) {
        //         elem.focus();
        //     }
        // } catch (e) {}
    },


    // ��ȡ��ǰ�����Ԫ��
    __getActive: function () {
        try {// try: ie8~9, iframe #26
            var activeElement = document.activeElement;
            var contentDocument = activeElement.contentDocument;
            var elem = contentDocument && contentDocument.activeElement || activeElement;
            return elem;
        } catch (e) {}
    },


    // ���и���
    __center: function () {
    
        var popup = this.__popup;
        var $window = $(window);
        var $document = $(document);
        var fixed = this.fixed;
        var dl = fixed ? 0 : $document.scrollLeft();
        var dt = fixed ? 0 : $document.scrollTop();
        var ww = $window.width();
        var wh = $window.height();
        var ow = popup.width();
        var oh = popup.height();
        var left = (ww - ow) / 2 + dl;
        var top = (wh - oh) * 382 / 1000 + dt;// �ƽ����
        var style = popup[0].style;

        
        style.left = Math.max(parseInt(left), dl) + 'px';
        style.top = Math.max(parseInt(top), dt) + 'px';
    },
    
    
    // ָ��λ�� @param    {HTMLElement, Event}  anchor
    __follow: function (anchor) {
        
        var $elem = anchor.parentNode && $(anchor);
        var popup = this.__popup;
        

        if (this.__followSkin) {
            popup.removeClass(this.__followSkin);
        }


        // ����Ԫ�ز�����
        if ($elem) {
            var o = $elem.offset();
            if (o.left * o.top < 0) {
                return this.__center();
            }
        }
        
        var that = this;
        var fixed = this.fixed;

        var $window = $(window);
        var $document = $(document);
        var winWidth = $window.width();
        var winHeight = $window.height();
        var docLeft =  $document.scrollLeft();
        var docTop = $document.scrollTop();


        var popupWidth = popup.width();
        var popupHeight = popup.height();
        var width = $elem ? $elem.outerWidth() : 0;
        var height = $elem ? $elem.outerHeight() : 0;
        var offset = this.__offset(anchor);
        var x = offset.left;
        var y = offset.top;
        var left =  fixed ? x - docLeft : x;
        var top = fixed ? y - docTop : y;


        var minLeft = fixed ? 0 : docLeft;
        var minTop = fixed ? 0 : docTop;
        var maxLeft = minLeft + winWidth - popupWidth;
        var maxTop = minTop + winHeight - popupHeight;


        var css = {};
        var align = this.align.split(' ');
        var className = this.className + '-';
        var reverse = {top: 'bottom', bottom: 'top', left: 'right', right: 'left'};
        var name = {top: 'top', bottom: 'top', left: 'left', right: 'left'};


        var temp = [{
            top: top - popupHeight,
            bottom: top + height,
            left: left - popupWidth,
            right: left + width
        }, {
            top: top,
            bottom: top - popupHeight + height,
            left: left,
            right: left - popupWidth + width
        }];


        var center = {
            left: left + width / 2 - popupWidth / 2,
            top: top + height / 2 - popupHeight / 2
        };

        
        var range = {
            left: [minLeft, maxLeft],
            top: [minTop, maxTop]
        };


        // ������������������Ӧλ��
        $.each(align, function (i, val) {

            // �����һ��±߽磺ʹ��������ϱ߶���
            if (temp[i][val] > range[name[val]][1]) {
                val = align[i] = reverse[val];
            }

            // ��������ұ߽磺ʹ���һ����±߶���
            if (temp[i][val] < range[name[val]][0]) {
                align[i] = reverse[val];
            }

        });


        // һ�����������
        if (!align[1]) {
            name[align[1]] = name[align[0]] === 'left' ? 'top' : 'left';
            temp[1][align[1]] = center[name[align[1]]];
        }


        //���follow��css, Ϊ�˸�cssʹ��
        className += align.join('-') + ' '+ this.className+ '-follow';
        
        that.__followSkin = className;


        if ($elem) {
            popup.addClass(className);
        }

        
        css[name[align[0]]] = parseInt(temp[0][align[0]]);
        css[name[align[1]]] = parseInt(temp[1][align[1]]);
        popup.css(css);

    },


    // ��ȡԪ�������ҳ���λ�ã�����iframe�ڵ�Ԫ�أ�
    // ��ʱ��֧���������ϵ� iframe ��Ƕ
    __offset: function (anchor) {

        var isNode = anchor.parentNode;
        var offset = isNode ? $(anchor).offset() : {
            left: anchor.pageX,
            top: anchor.pageY
        };


        anchor = isNode ? anchor : anchor.target;
        var ownerDocument = anchor.ownerDocument;
        var defaultView = ownerDocument.defaultView || ownerDocument.parentWindow;
        
        if (defaultView == window) {// IE <= 8 ֻ��ʹ���������ں�
            return offset;
        }

        // {Element: Ifarme}
        var frameElement = defaultView.frameElement;
        var $ownerDocument = $(ownerDocument);
        var docLeft =  $ownerDocument.scrollLeft();
        var docTop = $ownerDocument.scrollTop();
        var frameOffset = $(frameElement).offset();
        var frameLeft = frameOffset.left;
        var frameTop = frameOffset.top;
        
        return {
            left: offset.left + frameLeft - docLeft,
            top: offset.top + frameTop - docTop
        };
    }
    
});


/** ��ǰ���Ӹ߶� */
Popup.zIndex = 1024;


/** ���㸡���ʵ�� */
Popup.current = null;


return Popup;

 
});