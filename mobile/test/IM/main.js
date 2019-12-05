$(function() {
    var deleteMessage = function (client, yueyue_id, notice_id) {
        client.subscribe('$DELDOWN/' + yueyue_id + ':' + notice_id);
    }

    var arrayToString = function (uarr) {
        var asc_content = String.fromCharCode.apply(null, uarr);
        return decodeURIComponent(escape(asc_content));
    }

    var url = 'ws://online-wifi.yueus.com:8983';
    var yueyue_id = client_id;

    var options = {
      keepalive: 10,
      clientId: 'yuebuyer/' + yueyue_id
    };

console.log(yueyue_id);
    // 连接时的clientId与订阅的topic必须是约约id且必须相同，keepalive与客户端相同为10秒
    var client = mqtt.connect(url, options);

    client.subscribe([options.clientId,'yuebuyer/100019'], {qos: 1}, function (error, subs) {
        console.log(subs);
        for (var i in subs) {
            if (subs[i].messages) {
                var msg = arrayToString(subs[i].messages);
                console.log(msg);
                var msg_data = JSON.parse(msg);
                // 收到消息后需要主动删除消息，否则下次登录时仍然会收到。
                //deleteMessage(client, yueyue_id, msg_data.notice_id)
            }
        }

        // 接收完所有离线消息后再获取新消息
        client.on('message', function (topic, payload) {
            var msg = arrayToString(payload);

            console.log(topic);
            console.log(msg);
            var msg_data = JSON.parse(msg);
            // 收到消息后需要主动删除消息，否则下次登录时仍然会收到。
            //deleteMessage(client, yueyue_id, msg_data.notice_id)
        })


    });
});
