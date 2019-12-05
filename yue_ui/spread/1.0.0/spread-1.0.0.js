//����IE8 indexOf����
if (!Array.prototype.indexOf)
{
    Array.prototype.indexOf = function(elt /*, from*/)
    {
        var len = this.length >>> 0;
        var from = Number(arguments[1]) || 0;
        from = (from < 0)
            ? Math.ceil(from)
            : Math.floor(from);
        if (from < 0)
            from += len;
        for (; from < len; from++)
        {
            if (from in this &&
                this[from] === elt)
                return from;
        }
        return -1;
    };
}

function Spread(options)
{
    this.init(options);
}

Spread.prototype =
{
    init : function(options)
    {
        var self = this;
        //ajax ��������
        self.ajax_url = options.ajax_url || 'http://www.yueus.com/action/get_location_data.php'; //�첽url
        self.ajax_data = options.ajax_data || {};                            //�첽����
        self.ajax_type = options.ajax_type || 'jsonp';                       //�첽����
        self.ajax_send_before = options.ajax_send_before || function(){};     //�첽ǰ
        self.ajax_send_success = options.ajax_send_success || function(){};   //�첽�ɹ�
        self.ajax_send_error = options.ajax_send_error || function(){};       //�첽����
        self.ajax_send_complete = options.ajax_send_complete || function(){}; //�첽���

        //parse ��������
        self.parse_note = options.parse_note || 'location_id'; //�㼶���
        self.parse_value = options.parse_value || 'location_name' ; //�㼶ֵ
        self.parse_stack = options.parse_stack || '100';  //�㼶��� 1��ʼ

        //render ��Ⱦ����
        self.render_before = options.render_before || function(){}; //��Ⱦǰ
        self.render_after = options.render_after || function(){};   //��Ⱦ��
        self.render_contain_arr = options.render_contain_arr; //�����ڵ�
        self.render_notice_arr = options.render_notice_arr || []; //������ʾ��
        //event �¼�����
        self.event_change_after = options.event_change_after || function(){};

        //�Զ������� �ο����ݸ�ʽ [[{location_id: 101001,location_name: "����"},{},{}],[{},{},{}],[{},{}]]
        self.custom_data = options.custom_data || [];

        //Ĭ��ֵ
        self.select_default_id = options.select_default_id || '';

        self.random_num = options.random_num || parseInt(Math.random()*1000);//����� ���ָ������

        //���ݴ���
        self._prepare_data();
    },
    _prepare_data : function()
    {
        //��ȡ���� self.order_list
        var self = this;

        if(self.custom_data.length == 0)
        {
            self._ajax()
        }
        else
        {
            self.order_list = self._parse(self.custom_data);
            self._prepare_finish();
        }
    },
    _ajax : function()
    {
        var self = this;

        $.ajax
        ({
            url : self.ajax_url,
            data : self.ajax_data,
            dataType : self.ajax_type,
            method: "POST",
            cache: false,
            beforeSend: function()
            {
                self.ajax_send_before();
            },
            success: function(res)
            {
                //�ص�
                if(typeof self.ajax_send_success == 'function')self.ajax_send_success(res);

                //���ݸ�ʽ res.data
                var data = res.data;

                var ul = [];

                $.each(data,function(i,obj)
                {
                    ul.push(JSON.parse(obj));
                });

                //������������
                self.order_list = self._parse(ul);

                self._prepare_finish();
            },
            error: function(res)
            {
                if(typeof self.ajax_send_error == 'function')self.ajax_send_error(res);
            },
            complete: function()
            {
                if(typeof self.ajax_send_complete == 'function')self.ajax_send_complete();
            }
        })
    },
    _prepare_finish : function()
    {
        var self = this;
        //self.order_list is ready

        //�޸Ĳ㼶
        self.order_list = self.order_list.slice(0,self.parse_stack);

        self._get_parse_note_chats(self.order_list);

        if(typeof self.render_before == 'function')self.render_before();

        self._render()
    },
    _parse : function(ul)
    {
        //��ȡ��������
        var self = this;

        return self._sort(ul);
    },
    _get_parse_note_chats :function(ol)
    {
        //�Է������׸�Ԫ�ص�noteΪ׼����ȡnote����
        var self = this;

        var notes = [];

        $.each(ol,function(i,obj)
        {
            notes.push(obj[0][self.parse_note].toString().length)
        });

        self.parse_note_chats = notes;
    },
    _sort : function(events_data)
    {
        var self = this;

        //���򣬷������л�����
        if(events_data.length < 2)
        {
            return events_data
        }

        for(var k = 1;k <events_data.length;k++)
        {
            var obj_key = (events_data[k][0][self.parse_note] + '').length;

            var j = k-1;

            while( j >= 0 && obj_key < (events_data[j][0][self.parse_note] + '').length)
            {
                events_data[j+1] = events_data[j];
                j = j -1
            }
            events_data[j+1] = events_data[k]
        }
        return events_data
    },
    _render : function()
    {
        //��ʼ��render
        var self = this;

        var select_tag = '<select name="" class=""  spread-stack="{{stack}}" spread-num="{{num}}" spread-unique="{{unique}}"><option value="">{{notice}}</option>{{append}}</select>';

        var option_tag = '<option value="{{value}}" spread-options="">{{text}}</option>';

        var option_selected_tag = '<option selected="selected" value="{{value}}" spread-options="">{{text}}</option>';

        self.render_stack = 0;

        var default_data_arr;
        if(self.select_default_id)
        {
            default_data_arr = self._parse_default();

            for(;self.render_stack<default_data_arr.length;self.render_stack++)//for(var stack = 0;stack<self.order_list.length;stack++)
            {
                var this_tag ;


                this_tag = select_tag.replace('{{stack}}',self.render_stack);

                this_tag = this_tag.replace('{{num}}',self.random_num);

                this_tag = this_tag.replace('{{unique}}','spread_' + self.render_stack + '_' + self.random_num);

                this_tag = this_tag.replace('{{notice}}',self.render_notice_arr[self.render_stack] || "");

                var insert = '';


                $.each(default_data_arr[self.render_stack],function(i,obj)
                {
                    var this_inner_tag;

                    if(self.default_id_arr[self.render_stack] == obj[self.parse_note])
                    {
                        this_inner_tag = option_selected_tag.replace('{{value}}',obj[self.parse_note]);
                    }
                    else
                    {
                        this_inner_tag = option_tag.replace('{{value}}',obj[self.parse_note]);
                    }

                    this_inner_tag = this_inner_tag.replace('{{text}}',obj[self.parse_value]);

                    insert = insert + this_inner_tag
                });


                this_tag = this_tag.replace('{{append}}',insert);

                if(self.render_contain_arr.length == 1)
                {
                    self.render_contain_arr[1].append(this_tag);
                }
                else if(self.render_contain_arr[self.render_stack])
                {
                    self.render_contain_arr[self.render_stack].append(this_tag);
                }
                else
                {
                    $('body').append(this_tag);
                }
            }
        }
        else
        {
            for(;self.render_stack<1;self.render_stack++)//for(var stack = 0;stack<self.order_list.length;stack++)
            {
                var this_tag ;

                this_tag = select_tag.replace('{{stack}}',self.render_stack);

                this_tag = this_tag.replace('{{num}}',self.random_num);

                this_tag = this_tag.replace('{{unique}}','spread_' + self.render_stack + '_' + self.random_num);

                this_tag = this_tag.replace('{{notice}}',self.render_notice_arr[self.render_stack] || "");

                var insert = '';


                $.each(self.order_list[self.render_stack],function(i,obj)
                {
                    var this_inner_tag;

                    this_inner_tag = option_tag.replace('{{value}}',obj[self.parse_note]);

                    this_inner_tag = this_inner_tag.replace('{{text}}',obj[self.parse_value]);

                    insert = insert + this_inner_tag
                });


                this_tag = this_tag.replace('{{append}}',insert);


                if(self.render_contain_arr.length == 1)
                {
                    self.render_contain_arr[1].append(this_tag);
                }
                else if(self.render_contain_arr[self.render_stack])
                {
                    self.render_contain_arr[self.render_stack].append(this_tag);
                }
                else
                {
                    $('body').append(this_tag);
                }
            }
        }

        if(typeof self.render_after == 'function')self.render_after();

        self._event();
    },
    _parse_default : function()
    {
        var self = this;

        self.default_id_arr = [];

        var data_arr = [];

        $.each(self.parse_note_chats,function(i,obj)
        {
            var id = self.select_default_id.toString().substring(0,obj);

            if(self.default_id_arr.indexOf(id) == -1)
            {
                self.default_id_arr.push(id)
            }
        })

        $.each(self.default_id_arr,function(i,obj)
        {
            if(i == 0)
            {
                data_arr.push(self._get_part_data_by_note(obj,{mode:'self',root:self.default_id_arr[i]}));
            }
            else
            {
                data_arr.push(self._get_part_data_by_note(obj,{mode:'next_by_self',root:self.default_id_arr[i-1]}));
            }

        })

        return data_arr

    },
    _get_part_data_by_note : function(parse_note,options)
    {
        //this_level Ϊtrueʱ���ڲ��ҷ�����һ�������ĵ�ǰ�������������һ������
        if(!parse_note){return}
        //����parse_note��ȡ��һ������
        var self = this;

        var render_arr = [];

        var index_of_note_chats = self.parse_note_chats.indexOf(parse_note.toString().length);

        if(!options)
        {
            var valid_chat;

            if(self.parse_note_chats[index_of_note_chats+1])
            {
                valid_chat = self.parse_note_chats[index_of_note_chats+1]
            }
            else
            {
                valid_chat = false
            }
        }


        $.each(self.order_list,function(i,obj)
        {
            $.each(obj,function(k,k_obj)
            {
                if(options)
                {
                    switch (options.mode)
                    {
                        case 'self' :
                            if(k_obj[self.parse_note].toString().length == options.root.toString().length)
                            {
                                render_arr.push(k_obj);
                            }break;
                        case 'next_by_self' :
                            if(k_obj[self.parse_note].toString().indexOf(options.root) == 0 && k_obj[self.parse_note].toString().length == parse_note.toString().length)
                            {
                                render_arr.push(k_obj);
                            }break;break;
                    }

                }
                else
                {
                    //������һ��
                    if(k_obj[self.parse_note].toString().indexOf(parse_note) == 0 && (k_obj[self.parse_note].toString().length == valid_chat))
                    {
                        render_arr.push(k_obj);
                    }
                }

            })
        })

        return render_arr;

    },
    _render_by_note_data : function(data)
    {
        //������������str
        var self = this;

        var option_tag = '<option value="{{value}}" spread-options="">{{text}}</option>';

        var insert = '';

        $.each(data,function(i,obj)
        {
            var this_inner_tag;

            this_inner_tag = option_tag.replace('{{value}}',obj[self.parse_note]);

            this_inner_tag = this_inner_tag.replace('{{text}}',obj[self.parse_value]);

            insert = insert + this_inner_tag
        });

        return insert
    },
    _event : function()
    {
        var self = this;

        $('[spread-num="'+ self.random_num +'"]').change(function()
        {
            var stack = $(this).attr('spread-stack');

            var delete_stack = stack;

            var code = $("option:selected",this).val();

            var data = self._get_part_data_by_note(code); //��ȡ��һ������

            //������������
            while(self.parse_stack - delete_stack > 0)
            {
                var delete_node = $('[spread-unique="spread_'+ (parseInt(delete_stack)+1) + '_' + self.random_num +'"]');

                delete_node.remove();//delete_node.children("[spread-options]").remove();

                delete_stack++;

                if(delete_node.length == 0){break;}
            }

            if(!data || data.length == 0)
            {

            }
            else
            {
                var select_tag = '<select name="" class=""  spread-stack="{{stack}}" spread-num="{{num}}" spread-unique="{{unique}}"><option value="">{{notice}}</option>{{append}}</select>';//������һ����Ԫ��

                var this_tag ;

                this_tag = select_tag.replace('{{stack}}',(parseInt(stack)+1));

                this_tag = this_tag.replace('{{num}}',self.random_num);

                this_tag = this_tag.replace('{{unique}}','spread_' + (parseInt(stack)+1) + '_' + self.random_num);

                this_tag = this_tag.replace('{{notice}}',self.render_notice_arr[(parseInt(stack)+1)] || "");

                if(self.render_contain_arr[(parseInt(stack)+1)])
                {
                    self.render_contain_arr[(parseInt(stack)+1)].append(this_tag);
                }
                else
                {
                    $('body').append(this_tag);
                }

                var next_node = $('[spread-unique="spread_'+ (parseInt(stack)+1) + '_' + self.random_num +'"]');

                var str = self._render_by_note_data(data); //����str

                next_node.append(str);

                setTimeout(function(){$('[spread-num="'+ self.random_num +'"]').unbind('change');self._event();},0)
            }

            if(typeof self.event_change_after == 'function')self.event_change_after($(this));
        });
    },
    result : function()
    {
        var self = this;

        var index = 0;

        var arr = [];

        for(var i = 0;i< self.parse_stack; i++)
        {

            var node = $("option:selected",'[spread-unique="spread_'+ i + '_' + self.random_num +'"]');

            if(node.length != 0 && node.val())
            {
                var obj =
                {
                    id : node.val(),
                    text : node.text()
                }

                arr.push(obj);
            }
        }

        return  arr
    }
};

