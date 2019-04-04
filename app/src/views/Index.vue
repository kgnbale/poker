<template>
    <main role="main" class="container" style="max-width: 600px">
        <div class="d-flex align-items-center p-3 my-3 text-white-50 bg-purple rounded box-shadow">
            <div class="lh-100">
                <h6 class="mb-0 text-white lh-100">斗个地主</h6>
                <small>by nb framework</small>
            </div>
        </div>

        <div class="my-3 p-3 bg-white rounded box-shadow" >
            <h6 class="border-bottom border-gray pb-2 mb-0">游戏房间</h6>
            <div id="rooms"></div>
            <small class="d-block text-right mt-3">
                <router-link to="/create">创建房间！</router-link>
            </small>
        </div>
    </main>
</template>
<script>
    import * as Cookie from '../assets/js/cookie.js'
    import {Client} from '../assets/js/websocket.js'
    export default {
        data: function () {
            return {
                page: 'index'
            }
        },
        created() {
            console.log('token',Cookie.get('token'))
            var token = Cookie.get('token');
            var client = new Client('ws://127.0.0.1:9503', function (evt) {
                console.log("Connected to WebSocket server.");
                client.send({
                    action:'user',
                    token:token,
                });
            });

            function draw_room(room) {
                var join='';
                if(room.number < 3) {
                    join = '<a href="/play.html?id='+room.id+'&token='+token+'" >加入</a>';
                }
                var player = '';
                if(room.number > 0) {
                    var comma = '';
                    $.each([room.a,room.b,room.c],function (i,name) {
                        if(name) {
                            player = player + comma+'<span>'+name+'</span>';
                            comma = ',';
                        }
                    })
                }
                else {
                    player = '<span>等待玩家加入</span>';
                }

                return '<div class="media text-muted pt-3">\n' +
                    '            <img data-src="holder.js/32x32?theme=thumb&bg=007bff&fg=007bff&size=1" alt="" class="mr-2 rounded">\n' +
                    '            <div class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">\n' +
                    '                <div class="d-flex justify-content-between align-items-center w-100">\n' +
                    '                    <strong class="text-gray-dark">'+room.number+'/3</strong>\n' + join +
                    '                </div>\n' + player +
                    '            </div>\n' +
                    '        </div>'
            }

            client.reply('room',function (data) {
                if(data.code) {
                    alert(data.msg);
                    return;
                }
                $.each(data.rooms,function (i,room) {
                    $("#rooms").append(draw_room(room));
                });
            })

            client.reply('user',function (data) {
                if(data.play == "room") {
                    window.location.href='play.html?token='+token+'&id='+data.room
                }
                else {
                    client.send({
                        action:'room',
                        token:token,
                    });
                }
            })
        }
    }
</script>

