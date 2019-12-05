//http://www.xuebuyuan.com/1951015.html MQTT

var client = null;

function onConnectionLost(responseObject) {
    if (typeof (IMSDK.connect_fail) == 'function')IMSDK.connect_fail.call(this);
    if (responseObject.errorCode !== 0) {
    }

    client.connect({
        onSuccess:onConnect,
        keepAliveInterval: 10
    });
}

function onConnect(context) {
    // Subscribe after connect success.
    client.subscribe(IMSDK.connect_obj.clientId, {
        qos: 1,
        onSuccess: onSubscribeSuccess
    });
}

function onSubscribeSuccess(granted) {
    var message_arr = [];

    var suback_obj = parseSubscribeAck(granted);
    for (var i = 0; i < suback_obj.length; ++i) {

        if (suback_obj[i].messages) {
            var all_msg = suback_obj[i].messages;
            for (var j = 0; j < all_msg.length; ++j) {
                var msg_data = JSON.parse(all_msg[j]);

                message_arr.push(msg_data);
                //console.log(message_arr)
            }

            if (typeof (IMSDK.message_connect_success) == 'function')IMSDK.message_connect_success.call(this, message_arr)

        }
    }



}

function onMessageArrived(message){
    var msg_data = JSON.parse(message.payloadString);
    // 收到消息后需要主动删除消息，否则下次登录时仍然会收到。message_receive

    if (typeof (IMSDK.message_receive) == 'function')IMSDK.message_receive.call(this,msg_data);
}

var parseSubscribeAck = function (granted) {

    var pos = 0;
    var buffer = granted.grantedQos;
    var result = [];

    while (pos < buffer.length) {
        var obj = {};

        // The mqtt protocol.(1 Byte of qos)
        obj.qos = buffer.subarray(pos, pos + 1)[0];
        pos += 1;

        // The poco chat protocol
        // 4B msg_count + [ 4B idx + 4B length + payload ]
        if (pos > buffer.length - 4) {
            console.error('poco chat protocol fail.');
            return;
        }
        var msg_count = readInt32BE(buffer, pos);
        pos += 4;

        obj.messages = [];
        for (var i = 0; i < msg_count; ++i) {
            var idx = readInt32BE(buffer, pos);
            pos += 4;

            var length = readInt32BE(buffer, pos);
            pos += 4;

            if (0 === length) {
                continue;
            }

            var content = buffer.subarray(pos, pos + length);
            pos += length;

            var uarr_content = (new Zlib.Inflate(content)).decompress();
            obj.messages.push(arrayToString(uarr_content));
        }

        result.push(obj);
    }

    return result;
}

var arrayToString = function (uarr) {
    var asc_content = String.fromCharCode.apply(null, uarr);
    return decodeURIComponent(escape(asc_content));
}

var deleteMessage = function (client, yueyue_id, notice_id) {
    client.subscribe('$DELDOWN/' + yueyue_id + ':' + notice_id);
}

var readInt32BE = function(src, pos) {
    var num = src[pos] << 24 | src[pos + 1] << 16 | src[pos + 2] << 8 | src[pos + 3];
    num <<= 0;
    return num;

//        var int32_array = new Int32Array(src.subarray(pos, pos + 4));
//        int32_array.reverse();
//        return int32_array[0];
}


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
        clientId: ''
    },
    init : function(options)
    {
        var self = this;

        //self.url = options.url || 'ws://online-wifi.yueus.com:8983';

        self.host = options.hostname || 'online-wifi.yueus.com';

        self.port = options.port || 8983;

        self.options.clientId = options.client_id || '';

        self.message_connect_success = options.message_connect_success || function(){};

        self.message_receive = options.message_receive || function(){};

        self.message_send_success = options.message_send_success || function(){};

        self.message_send_fail = options.message_send_fail || function(){};

        self.connect_fail = options.connect_fail || function(){};

        self.render_send_form();

    },
    _connect_valid : function()
    {
        var self = this;

        if(self.options.clientId)
        {
            return true
        }
        else
        {
            throw 'clientId is unvalid';

            return false
        }
    },
    options_parse : function(opt)
    {
        var self = this;

        var options = {};

        options.keepalive = opt.keepalive;

        options.clientId = self.pre_fixed.seller + opt.clientId.toString();

        self.connect_obj = options;

        return options
    },
    connect : function()
    {
        var self = this;

        self._connect_valid();

        var opt = self.options_parse(self.options);

        client = new Paho.MQTT.Client(self.host, self.port, opt.clientId);

        // set callback handlers
        client.onConnectionLost = onConnectionLost;
        client.onMessageArrived = onMessageArrived;

        // connect the client
        client.connect({
            onSuccess:onConnect,
            keepAliveInterval: 10
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
    send_media : function(stringify_obj,media_obj,url,media_preview)
    {
        var self = this;

        var sender = JSON.stringify(stringify_obj);

        var cache = media_obj
        cache.id = 'medias'
        self._media_form.innerHTML = '<input id="media_form_input" type="text" name="data"/>';

        document.getElementById('media_form_input').value = sender;
        document.getElementById('mediaform').appendChild(cache);

        stringify_obj.preview_src = media_preview;

        var myFormData = new FormData(document.getElementById('mediaform'));
        var xhr = new XMLHttpRequest();
        xhr.open('POST',url);
        xhr.onload = function(e) {
            if (xhr.status == 200 && xhr.responseText) {
                if(typeof (self.message_send_success) == 'function')self.message_send_success.call(this,stringify_obj,xhr.response);
            }
            else{
                if(typeof (self.message_send_fail) == 'function')self.message_send_fail.call(this,stringify_obj,xhr.response);
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

        self._media_form = document.createElement('form');
        self._media_form.setAttribute('id','mediaform');
        self._media_form.setAttribute('class','fn-hide');

        document.getElementsByTagName('body')[0].appendChild(self._form);
        document.getElementsByTagName('body')[0].appendChild(self._media_form);
    },
    has_read : function(arr_in_notice_id)
    {
        var self = this;

        for(var i in arr_in_notice_id)
        {
            console.log(i);
            deleteMessage(client, self.connect_obj.clientId, arr_in_notice_id[i])
        }
    }
};
