var httpserver = 'http://127.0.0.1:9503';
var server = 'ws://127.0.0.1:9503';

function get(name) {
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)
        return  unescape(r[2]);
    return null;
}