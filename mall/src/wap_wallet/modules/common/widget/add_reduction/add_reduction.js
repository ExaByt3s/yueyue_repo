/**
 *  ��Բ
 *  �Ӽ����
 */

 /**
  * @require ./add_reduction.scss
 **/


var $ = require('zepto');
var fastclick = require('fastclick');

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

// ���庯������
function add_reduction(options) 
{
    var options = options || {} ;
    this.ele_tpl = options.ele ;
    this.param_val = options.param_val||{} ;
    this.is_show_operate_btn = options.is_show_operate_btn ;
    this.name = options.name || 'add-reduction';

    // ��ʼ������
    var defaults = 
    {
        input_val : 0,
        max_val : '',
        min_val : ''
    }
    this.data = $.extend(true,{},defaults,this.param_val);
    //  ��ʼ��
    this.init();

}

add_reduction.prototype = 
{
    init : function() 
    {
        var self = this;

        // ��װ�¼�
        self.setup_event();

        // ��Ⱦģ��
        self.render(self.ele_tpl);

    },

    render : function(ele) 
    {
        var self = this;
        var template  = __inline('./add-reduction-mod.tmpl');  
        var view = ele.html( template({
            default_btn_value : self.data.input_val,
            name : self.name
        }) );

        self.ele = ele;

        self.reduce_ele = view.find('[btn-reduce]');
        self.add_ele  = view.find('[btn-add]');
        self.real_text_ele = view.find('[data-role="real-text"]');

        // ������ֵele
        self.input_val_ele =  view.find('[btn-value]');

        if (!self.is_show_operate_btn) 
        {
            self.hide_operate_btn();
            
        }


        self.reduce_ele.on('click', function() {
            self.reduce();
        });

        self.add_ele.on('click', function() {
            self.add();
        });
    },

    setup_event : function() 
    {
        var self = this;
    },

    // ��ȥ����
    reduce : function() 
    {
        var self = this;
        var btn_value = self.get_val();
        btn_value-- ;

        if (self.data.min_val && ( btn_value < self.data.min_val) ) 
        {
            alert('�������ֲ���С��'+self.data.min_val);
            return ;
        }

        // ����0�˳�
        if (btn_value < 0) 
        {
            self.set_val(0);
            return ;
        }

        self.set_val(btn_value);
        self.reduce_ele.trigger('reduce',btn_value);
    },

    // ���Ӳ���
    add : function() 
    {
        var self = this;
        
        var btn_value = self.get_val();
        btn_value++ ;
        if (self.data.max_val && ( btn_value > self.data.max_val) ) 
        {
            alert('�������ֲ��ô���'+self.data.max_val);
            return ;
        }

        self.set_val(btn_value);
        self.add_ele.trigger('add',btn_value)
    },

    // ȡֵ
    get_val : function() 
    {
        var self = this;
        var btn_value = self.input_val_ele.val();
        return btn_value ;
    },

    // ����ֵ
    set_val :  function(val) 
    {
        var self = this;
        self.input_val_ele.val(val);
        self.real_text_ele.val(val);
    },
    // ����ʾ �Ӽ���ť
    hide_operate_btn : function() 
    {
        var self = this;
        self.reduce_ele.addClass('fn-hide');
        self.add_ele.addClass('fn-hide');
        self.input_val_ele.addClass('input-disabled');
        self.input_val_ele.attr('readonly', 'true');

    }

}

return add_reduction;
