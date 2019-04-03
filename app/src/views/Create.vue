<template>
    <main role="main" class="container" style="max-width: 600px">
        <div class="d-flex align-items-center p-3 my-3 text-white-50 bg-purple rounded box-shadow">
            <div class="lh-100">
                <h6 class="mb-0 text-white lh-100">创建游戏房间</h6>
                <small>by nb framework</small>
            </div>
        </div>

        <div class="my-3 p-3 bg-white rounded box-shadow" id="rooms">
            <form name="creat">
                <div class="form-group">
                    <label>房间名称</label>
                    <input name="name" type="text" class="form-control" placeholder="长度不得超过5个">
                </div>
                <div class="form-group">
                    <label>设置房间进入密码</label>
                    <input name="pass" type="password" class="form-control" placeholder="Password">
                    <small class="form-text text-muted">密码留空则为不设置密码.</small>
                </div>
                <a href="javascript:submit();" class="btn btn-primary">Submit</a>
            </form>
        </div>

    </main>
</template>
<script>
    import * as Cookie from '../cookie.js'
    import {Client} from '../websocket.js'
    export default {
        data: function () {
            return {
                page: 'App'
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

            client.reply('user',function (data) {
                if(data.play == "room") {
                    window.location.href='play.html?token='+token+'&id='+data.room
                }
            })

            client.reply('room-establish',function (data) {
                window.location.href='play.html?token='+token+'&id='+data.id
            })

            function submit() {
                var d = {};
                var t = $('form').serializeArray();
                $.each(t, function() {
                    d[this.name] = this.value;
                });

                if(!d['name']) {
                    alert('请输入房间名成');
                    return;
                }

                client.send({
                    action:'room/establish',
                    token:token,
                    name:d['name'],
                    pass:d['pass'],
                });
            }
        }
    }
</script>

