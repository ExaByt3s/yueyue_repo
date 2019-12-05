define('yue_ui/selectbox/selectbox', function(require, exports, module){ /*!
 * selectbox
 * Date: 2014-01-10
 * https://github.com/aui/popupjs
 * (c) 2009-2013 TangBin, http://www.planeArt.cn
 *
 * This is licensed under the GNU LGPL, version 2.1 or later.
 * For details, see: http://www.gnu.org/licenses/lgpl-2.1.html
 */

 /**
  * @require modules/yue_ui/selectbox/ui-selectbox.scss
  */

var $ = require('components/jquery/jquery.js');
var Popup = require('yue_ui/selectbox/popup');
// var css = './ui-selectbox.css';

// css loader: RequireJS & SeaJS
// if (css) {
//     var fn = require[require.toUrl ? 'toUrl' : 'resolve'];
//     if (fn) {
//         css = fn(css);
//         css = '<link rel="stylesheet" href="' + css + '" />';
//         if ($('base')[0]) {
//             $('base').before(css);
//         } else {
//             $('head').append(css);
//         } 
//     }
// }


function Selectbox (select, options) {

    select = this.select = $(select);

    $.extend(this, options || {});

    var that = this;
    var isIE6 = !('minWidth' in select[0].style);
    //var selectHeight = select.outerHeight() + 'px';

    

    if (select.is('[multiple]')) {
        return;
    }


    if (select.data('selectbox')) {
        // ɾ����һ�ε� selectbox �����¸���
        select.data('selectbox').remove();
    }


    var selectboxHtml = this._tpl(this.selectboxHtml, $.extend({
        textContent: that._getOption().html() || ''
    }, select.data()));


    this._selectbox = $(selectboxHtml);
    this._value = this._selectbox.find('[data-value]');


    // selectbox ���¼���
    if (this.isShowDropdown && !select.attr('disabled')) {
        this._globalKeydown = $.proxy(this._globalKeydown, this);

        this._selectbox
        .on(this._clickType + ' focus blur', function (event) {
            that[that._clickType === event.type ? 'click' : event.type]();
        });
    }


    this._selectbox
    .css({
        width: select.outerWidth() + 'px'
    });
    

    // ��¡ԭ�� select �߶�
    // this._value.css({
    //     minHeight: selectHeight,
    //     height: isIE6 ? selectHeight : '',
    //     lineHeight: selectHeight
    // });


    // ��¡ԭ�� select �Ļ��� UI �¼�
    select
    .on('focus blur', function (event) {
        that[event.type]();
        event.preventDefault();
    })
    .on('change', function () {
        var text = that._getOption().html();
        that._value.html(text);
    });


    // ����ԭ�� select
    // ä����Ȼ����ͨ�� tab �����ʵ�ԭ���ؼ�
    // iPad �� iPhone ���豸�����Ȼ�ܹ�ʹ�ù������� select
    select.css({
        opacity: 0,
        position: 'absolute',
        left: isIE6 ? '-9999px' : 'auto',
        right: 'auto',
        top: 'auto',
        bottom: 'auto',
        zIndex: this.isShowDropdown ? -1 : 1
    }).data('selectbox', this);

    // ����ԭ�� select
    select.after(this._selectbox);
}

var popup = function () {};
popup.prototype = Popup.prototype;
Selectbox.prototype = new popup();

$.extend(Selectbox.prototype, {

    selectboxHtml:
      '<div class="ui-selectbox" hidefocus="true" style="user-select:none" onselectstart="return false" tabindex="-1" aria-hidden>'
    +     '<div class="ui-selectbox-inner" data-value="">{{textContent}}</div>'
    +     '<i class="ui-selectbox-icon"></i>'
    + '</div>',
    
    dropdownHtml:  '<dl class="ui-selectbox-dropdown">{{options}}</dl>',
    optgroupHtml:  '<dt class="ui-selectbox-optgroup">{{label}}</dt>',
    optionHtml:    '<dd class="ui-selectbox-option {{className}}" data-option="{{index}}" tabindex="-1">{{textContent}}</dd>',
    selectedClass: 'ui-selectbox-selected',
    disabledClass: 'ui-selectbox-disabled',
    focusClass:    'ui-selectbox-focus',
    openClass:     'ui-selectbox-open',

    // �ƶ��˲�ʹ��ģ��������
    isShowDropdown:  !('createTouch' in document),

    selectedIndex: 0,
    value: '',


    close: function () {
        if (this._popup) {
            this._popup.close().remove();
            this.change();
        }
    },


    show: function () {

        var that = this;
        var select = this.select;
        var selectbox = that._selectbox;

        if (!select[0].length) {
            return false;
        }

        var MARGIN = 20;
        var selectHeight = select.outerHeight();
        var topHeight = select.offset().top - $(document).scrollTop();
        var bottomHeight = $(window).height() - topHeight - selectHeight;
        var maxHeight = Math.max(topHeight, bottomHeight) - MARGIN;

        var popup = this._popup = new Popup();
        popup.node.innerHTML = this._dropdownHtml();

        this._dropdown = $(popup.node);
        $(popup.backdrop)
        .css('opacity', 0)
        .on(this._clickType, $.proxy(this.close, this));


        var children = that._dropdown.children();
        var isIE6 = !('minWidth' in children[0].style);


        children.css({
            minWidth: selectbox.innerWidth(),
            maxHeight: maxHeight,
            overflowY: 'auto',
            overflowX: 'hidden'
        });



        this._dropdown
        .on(this._clickType, '[data-option]', function (event) {
            var index = $(this).data('option');
            that.selected(index);
            that.close();

            event.preventDefault();
        });


        popup.onshow = function () {
            $(document).on('keydown', that._globalKeydown);
            selectbox.addClass(that.openClass);
            //selectbox.find('[data-option=' +  + ']').focus()
            that.selectedIndex = select[0].selectedIndex;
            that.selected(that.selectedIndex);
        };


        popup.onremove = function () {
            $(document).off('keydown', that._globalKeydown);
            selectbox.removeClass(that.openClass);
        };


        // ��¼չ��ǰ�� value
        this._oldValue = this.select.val();

        popup.showModal(selectbox[0]);

        if (isIE6) {
            children.css({
                width: Math.max(selectbox.innerWidth(), children.outerWidth()),
                height: Math.min(maxHeight, children.outerHeight())
            });
            
            popup.reset();
        }
    },


    selected: function (index) {

        // ��鵱ǰ���Ƿ񱻽���
        if (this._getOption(index).attr('disabled')) {
            return false;
        }

        var dropdown = this._dropdown;
        var option = this._dropdown.find('[data-option=' + index + ']');
        var value = this.select[0].options[index].value;
        var oldIndex = this.select[0].selectedIndex;
        var selectedClass = this.selectedClass;

        // ����ѡ��״̬��ʽ
        dropdown.find('[data-option=' + oldIndex + ']').removeClass(selectedClass);
        option.addClass(selectedClass);
        option.focus();

        // ����ģ��ؼ�����ʾֵ
        this._value.html(this._getOption(index).html());

        // ���� Selectbox ��������
        this.value = value;
        this.selectedIndex = index;

        // ͬ�����ݵ�ԭ�� select
        this.select[0].selectedIndex = this.selectedIndex;
        this.select[0].value = this.value;

        return true;
    },


    change: function () {
        if (this._oldValue !== this.value) {
            this.select.triggerHandler('change');
        }
    },


    click: function () {
        this.select.focus();
        if (this._popup && this._popup.open) {
            this.close();
        } else {
            this.show();
        }
    },


    focus: function () {
        this._selectbox.addClass(this.focusClass);
    },


    blur: function () {
        this._selectbox.removeClass(this.focusClass);
    },


    remove: function () {
        this.close();
        this._selectbox.remove();
    },


    _clickType: 'onmousedown' in document ? 'mousedown' : 'touchstart',


    // ��ȡԭ�� select �� option jquery ����
    _getOption: function (index) {
        index = index === undefined ? this.select[0].selectedIndex : index;
        return this.select.find('option').eq(index);
    },


    // ��ģ���滻
    _tpl: function (tpl, data) {
        return tpl.replace(/{{(.*?)}}/g, function ($1, $2) {
            return data[$2];
        });
    },


    // ��ȡ������� HTML
    _dropdownHtml: function () {
        var options = '';
        var that = this;
        var select = this.select;
        var selectData = select.data();
        var index = 0;


        var getOptionsData = function ($options) {
            $options.each(function () {
                var $this = $(this);
                var className = '';

                if (this.selected) {
                    className = that.selectedClass;
                } else {
                    className = this.disabled ? that.disabledClass : '';
                }

                options += that._tpl(that.optionHtml, $.extend({
                        value: $this.val(),
                        // ����������ƣ� "&#60;s&#62;ѡ��&#60;/s&#62;" ʹ�� .text() �ᵼ�� XSS
                        // ���⣬ԭ�� option ��֧�� html �ı�
                        textContent: $this.html(),
                        index: index,
                        className: className
                    }, $this.data(), selectData));

                index ++;
            });
        };


        if (select.find('optgroup').length) {

            select.find('optgroup').each(function (index) {
                options += that._tpl(that.optgroupHtml, $.extend({
                    index: index,
                    label: this.label
                }, $(this).data(), selectData));
                getOptionsData($(this).find('option'));
            });

        } else {
            getOptionsData(select.find('option'));
        }


        return this._tpl(this.dropdownHtml, {
            options: options
        });
    },


    // �����ƶ�
    _move: function (n) {
        var min = 0;
        var max = this.select[0].length - 1;
        var index = this.select[0].selectedIndex + n;
        
        if (index >= min && index <= max) {
            // �������� disabled ���Ե�ѡ��
            if (!this.selected(index)) {
                this._move(n + n);
            }
        }
    },


    // ȫ�ּ��̼���
    _globalKeydown: function (event) {

        var p;

        switch (event.keyCode) {
            // backspace
            case 8:
                p = true;
                break;

            // tab
            case 9:
            // esc
            case 27:
            // enter
            case 13:
                this.close();
                p = true;
                break;

            // up
            case 38:

                this._move(-1);
                p = true;
                break;

            // down
            case 40:

                this._move(1);
                p = true;
                break;
        }

        if (p){
            event.preventDefault();
        }
    }

});


return function (elem, options) {
    // ע�⣺��Ҫ���� Selectbox ����ӿڸ��ⲿ��ֻ����װ����;
    // ��֤ģ���������ԭ���ؼ����Ӽ�������������ʱ����Ŀ�г���װ��

    if (elem.type === 'select') {
        new Selectbox(elem, options);
    } else {
        $(elem).each(function () {
            new Selectbox(this, options);
        });
    }
};

 
});