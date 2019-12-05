//http://www.xuebuyuan.com/1951015.html MQTT
var IMSDK =
{
    subscribe_mode : 1,
    pre_fixed :
    {
        buyer : 'yuebuyer/',
        seller : 'yueseller/'
    },
    options :
    {
        keepalive: 10,
        client_id: ''
    },
    init : function(options)
    {
        var self = this;

        self.url = options.url || 'ws://online-wifi.yueus.com:8983';

        self.options.client_id = options.client_id || '';

        self.message_receive = options.message_receive || function(){};

        self.message_send_success = options.message_send_success || function(){};

        self.message_send_fail = options.message_send_fail || function(){};

        self.render_send_form();

    },
    _connect_valid : function()
    {
        var self = this;

        if(self.options.client_id)
        {
            return true
        }
        else
        {
            throw 'client_id is unvalid';

            return false
        }
    },
    options_parse : function(opt)
    {
        var self = this;

        var options = {};

        options.keepalive = opt.keepalive;

        options.client_id = self.pre_fixed.seller + opt.client_id.toString();

        self.connect_obj = options;

        return options
    },
    connect : function()
    {
        var self = this;

        self._connect_valid();

        var opt = self.options_parse(self.options);


        self.client = mqtt.connect(self.url, opt);
    },
    get_msg : function()
    {
        var self = this;

        console.log('链接! ' + self.connect_obj.client_id);
        self.client.subscribe(self.connect_obj.client_id,{qos: self.subscribe_mode},function(error, subs){
            console.log("in_client");
            console.log(subs);
            for (var i in subs) {
                if (subs[i].messages) {
                    var msg = self._arrayToString(subs[i].messages);
                    console.log(JSON.parse(msg));
                    var msg_data = JSON.parse(msg);
                    // 收到消息后需要主动删除消息，否则下次登录时仍然会收到。
                    //self._deleteMessage(self.client, self.connect_obj.client_id, msg_data.notice_id)
                }
            }

            // 接收完所有离线消息后再获取新消息
            self.client.on('message', function (topic, payload) {
                var msg = self._arrayToString(payload);

                console.log(topic);
                var msg_data = JSON.parse(msg);

                if(typeof (self.message_receive) == 'function')self.message_receive.call(this,msg_data);
                // 收到消息后需要主动删除消息，否则下次登录时仍然会收到。
                //self._deleteMessage(self.client, self.connect_obj.client_id, msg_data.notice_id)
            })


        });
    },
    send_msg : function(stringify_obj,url)
    {
        var self = this;

        var sender = JSON.stringify(stringify_obj);

        self._form.innerHTML = '<input id="form_input" type="text" name="data" data-role="submit"/>';

        document.getElementById('form_input').value = sender;

        var myFormData = new FormData(document.getElementById('ajaxform'));
        var xhr = new XMLHttpRequest();
        xhr.open('POST',url);
        xhr.onload = function(e) {
            if (xhr.status == 200 && xhr.responseText) {
                if(typeof (self.message_send_success) == 'function')self.message_send_success.call(this,stringify_obj,xhr);
            }
            else{
                if(typeof (self.message_send_fail) == 'function')self.message_send_fail.call(this,stringify_obj,xhr);
            }
        }

        xhr.send(myFormData);


    },
    render_send_form : function()
    {
        var self = this;

        self._form = document.createElement('form');
        self._form.setAttribute('id','ajaxform');
        self._form.setAttribute('class','fn-hide');

        document.getElementsByTagName('body')[0].appendChild(self._form);
    },
    _deleteMessage : function (client, yueyue_id, notice_id) {
        client.subscribe('$DELDOWN/' + yueyue_id + ':' + notice_id);
    },
    _arrayToString : function (uarr) {
        var asc_content = String.fromCharCode.apply(null, uarr);
        return decodeURIComponent(escape(asc_content));
    }
};
