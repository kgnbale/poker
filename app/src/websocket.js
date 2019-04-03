export function Client(open) {

    var oTemp = new Object;

    oTemp.event = {};
    var websocket = new WebSocket(server);

    websocket.onopen = open;

    websocket.onclose = function (evt) {
        console.log("Disconnected");
    };
    websocket.onerror = function (evt, e) {
        console.log('Error occured: ' + evt.data);
    };

    websocket.onmessage = function (evt) {
        var data = JSON.parse(evt.data);
        console.log(data);
        if(data.action in oTemp.event) {
            oTemp.event[data.action](data);
        }
        else {
            console.log('['+data.action+']no matching ...');
        }
    }

    oTemp.websocket = websocket;

    oTemp.event['reply-login-fail'] = function () {
        alert('请重新登录')
    }
    
    oTemp.reply = function (action,func) {
        oTemp.event['reply-'+action] = function (data) {
            if(data.code) {
                alert(data.msg)
                return;
            }
            func(data);
        };
    }

    oTemp.push = function (action,func) {
        oTemp.event['push-'+action] = func;
    }

    oTemp.send = function (args) {
        oTemp.websocket.send(JSON.stringify(args));
    }

    oTemp.listen = function(action) {
        var o = new Object;
        o.action = action;
        o.push = function (func) {
            oTemp.push(o.action,func);
            return o;
        }
        o.reply = function (func) {
            oTemp.reply(o.action,func);
            return o;
        }
        return o;
    }

    return oTemp;
}
