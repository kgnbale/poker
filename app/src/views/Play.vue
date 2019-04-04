<template>
    <main role="main" class="container" style="max-width: 798px">
        <div class="card">
            <div class="card-header" id="tips">请准备游戏！</div>
            <div class="card-body">
                <ul class="list-group list-group-flush" id="info"></ul>
            </div>
            <div class="card-footer text-muted">
                <div class="input-group">
                    <input id="text" type="text" class="form-control" placeholder="请输入发送内容！">
                    <div class="input-group-append">
                        <span class="input-group-text" id="send"> 发 送 </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="my-3 p-3 bg-white rounded box-shadow" style="min-height: 250px">
            <h6 class="console border-bottom border-gray pb-2 mb-0">
                <span id="room" class="badge">房间</span>
                <span id="exit" class="badge badge-secondary">离开</span>
                <span id="ready" class="badge badge-secondary">准备</span>
                <div id="lead" class="float-right" style="display: none">
                    <span class="no badge badge-secondary">过</span>
                    <span class="yes badge badge-secondary">出牌</span>
                </div>
                <div id="rob" class="float-right" style="display: none">
                    <span class="no badge badge-secondary">不叫</span>
                    <span class="yes badge badge-secondary">叫地主</span>
                </div>
            </h6>
            <div class="poker" id="poker"></div>
        </div>

        <div class="my-3 p-3 bg-white rounded box-shadow">
            <h6 class="border-bottom border-gray pb-2 mb-0">房间成员</h6>
            <div class="media text-muted pt-3" id="a">
                <span class="mr-2 rounded">A</span>
                <div class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <strong class="text-gray-dark">等待玩家</strong>
                        <a id="prepare-a"></a>
                    </div>
                    <div class="info"></div>
                </div>
            </div>
            <div class="media text-muted pt-3" id="b">
                <span class="mr-2 rounded">B</span>
                <div class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <strong class="text-gray-dark">等待玩家</strong>
                        <a id="prepare-b"></a>
                    </div>
                    <div class="info"></div>
                </div>
            </div>
            <div class="media text-muted pt-3" id="c">
                <span class="mr-2 rounded">C</span>
                <div class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <strong class="text-gray-dark">等待玩家</strong>
                        <a id="prepare-c"></a>
                    </div>
                    <div class="info"></div>
                </div>
            </div>
        </div>
    </main>
</template>
<script>
    import * as Cookie from '../assets/js/cookie.js'
    import {Client} from '../assets/js/websocket.js'
    export default {
        data: function () {
            return {
                page: 'App'
            }
        },
        created() {
            var id = get('id');
            var user = {};
            var lead = [],leadlock=false;
            var token = Cookie.get('token');
            var client = new Client('ws://127.0.0.1:9503', function (evt) {
                console.log("Connected to WebSocket server.");
                client.send({
                    action:'user',
                    token:token,
                });
            });
            var landowner = 0;//地主

            function drawPlayer(seat) {
                return '<small>座位('+seat+'):</small>&nbsp;';
            }

            function drawPoker(id) {
                var poker = cards[parseInt(id)-1];
                return '<span class="badge badge-primary">'+poker.name+'</span>&nbsp;';
            }

            function drawChat(seat,msg) {
                var name = $("#" + seat + " strong").html();
                $("#info").append('<li class="list-group-item">'+name+' : '+msg+'</li>');
            }

            function tips(msg) {
                $("#tips").html(msg);
            }

            function drawLead(seat,poker,residue) {
                var info = '';
                if(poker.length < 1) {
                    info = '<span class="badge badge-secondary">要不起！</span>';
                }
                else {
                    $.each(poker,function (i,id) {
                        info += drawPoker(id);
                    })
                }
                $("#" + seat + " .info").html(info);
                $("#prepare-" + seat).html(residue);
            }

            client.reply('user',function (data) {
                user = data;
                if(data.play == "hall") {
                    client.send({
                        action:'room/enter',
                        token:token,
                        id:id?id:0
                    });
                }
                else {
                    client.send({
                        action:'room/synchro',
                        token:token,
                    });
                }
            })

            client.reply('room-synchro',function (data) {
                if(data.status == 'startd') {
                    $("#ready").hide();
                    if(data.landowner) {
                        landowner = data.landowner;
                        $('#'+landowner).addClass('landowner');
                        //如果该我出牌，显示出牌按钮
                        if(data.leader == user.seat) {
                            tips('该你出牌了！');
                            $("#lead").show();
                        }
                        else {
                            tips('等待 '+data.leader+' 出牌！');
                        }
                    }
                    else {
                        if(data.call == user.seat) {
                            $("#rob").show();
                        }
                    }
                    var player = data[user.seat];
                    var div = $("#poker");
                    $.each(player.poker,function(k,poker){
                        div.append('<img id="'+poker.id+'" val="'+poker.name+'" src="card/'+poker.id+'.png">');
                    });
                    if(data.landowner) {
                        $.each({a:data.a,b:data.b,c:data.c},function (i,p) {
                            $("#" + i + " strong").html(p.name);
                            drawLead(i,p.lead,p.residue);
                        })
                        $("#" + data.leader + " .info").html('<span>等待出牌！</span>');
                    }
                    else {
                        //叫地主
                        if(data.call == user.seat) {
                            $("#rob").show();
                        }
                        else {
                            tips('等待座位'+data.call+'的玩家抢地主');
                        }
                    }
                }
                $.each({a:data.a,b:data.b,c:data.c},function (i,p) {
                    $("#" + i + " strong").html(p.name);
                    if(p.ready) {
                        $("#prepare-" + i).html('ready');
                        if(i == user.seat) $("#ready").html('取消准备');
                    }
                    else {
                        if(i == user.seat) $("#ready").html('准备');
                    }
                })
                $("#room").html(data.name+ '--座位'+ user.seat);

            })

            client.listen('room-enter').reply(function (data) {
                if(data.code) {
                    console.log(data.msg);
                    return;
                }
                $("#room").html(data.name);
                user.room = data.id;
                user.seat = data.seat
                $.each({a:data.a,b:data.b,c:data.c},function (i,p) {
                    $("#" + i + " strong").html(p.name);
                    $("#coin-" + i).html(p.coin);
                    if(p.ready === 1) {
                        $("#prepare-" + i).html('ready');
                    }
                })
            }).push(function (data) {
                $("#" + data.seat + " strong").html(data.name);
            })

            client.listen('room-quit').reply(function () {
                window.location.href='room.html?token='+token
            }).push(function (data) {
                $("#" + data.seat + " strong").html('等待玩家');
            })

            client.listen('play-lead').reply(function (data) {
                leadlock = false;
                var info = '<li class="list-group-item">'+drawPlayer(user.seat)+'<h6>';

                if(lead.length == 0) {
                    info+='<span class="badge badge-secondary">不要</span>&nbsp;';
                    drawLead(user.seat,[],data.residue);
                }
                else {
                    var tmp = new Array();
                    for(var x in lead) {
                        info+=drawPoker(lead[x].id);
                        $("#"+lead[x].id).remove();
                        tmp[x] = lead[x].id;
                    }
                    console.log(user.seat,tmp,data.residue);
                    drawLead(user.seat,tmp,data.residue);
                    lead = [];
                }
                info+='</h6></li>';
                $("#info").append(info);
                $("#info").scrollTop($("#info")[0].scrollHeight);
                $("#lead").hide();
                tips('等待 '+data.nexter+' 出牌！');
                $("#" + data.nexter + " .info").html('<span>等待出牌！</span>');
            }).push(function (data) {
                if(data.nexter == user.seat) {
                    tips('该你出牌了！');
                    $("#lead").show();
                }
                else {
                    tips('等待 '+data.nexter+' 出牌！');
                }
                var info = '<li class="list-group-item">'+drawPlayer(data.seat)+'<h6>';
                if(data.poker.length == 0) {
                    info+='<span class="badge badge-secondary">不要</span>&nbsp;';
                }
                else {
                    $.each(data.poker,function (i,p) {
                        info+=drawPoker(p);
                    })
                }
                info+='</h6></li>';
                $("#info").append(info);
                $("#info").scrollTop($("#info")[0].scrollHeight);
                drawLead(data.seat,data.poker,data.residue);
                $("#" + data.nexter + " .info").html('<span>等待出牌！</span>');
            })

            client.listen('play-ready').reply(function (data) {
                if(data.ready) {
                    $("#ready").html('取消准备');
                    $("#prepare-" + user.seat).html('ready');
                    tips('等待其它玩家开始游戏');
                }
                else  {
                    $("#ready").html('准备');
                    $("#prepare-" + user.seat).html('');
                }
            }).push(function (data) {
                if(data.ready) {
                    $("#prepare-" + data.seat).html('ready');
                }
                else {
                    $("#prepare-" + data.seat).html('');
                }
            })

            client.listen('play-rob').reply(function (data) {
                $("#rob").hide();
                if(data.call) {
                    tips('等待座位'+data.call+'的玩家抢地主');
                }

            }).push(function (data) {
                if(data.landowner) {
                    if(data.landowner == user.seat) {
                        landowner = user.seat;
                        //如果我是地主
                        tips('你抢到地主了，请出牌！');
                        $("#lead").show();
                        var div = $("#poker");
                        div.empty();
                        $.each(data.poker,function(k,poker){
                            div.append('<img id="'+poker.id+'" val="'+poker.name+'" src="card/'+poker.id+'.png">');
                        });
                    }
                    else {
                        landowner = data.landowner;
                        tips('座位'+data.landowner+'的玩家成为地主，等待其出牌！');
                    }
                    $('#'+landowner).addClass('landowner');
                    $("#prepare-" + landowner).html('20');
                }
                else {
                    if(data.call == user.seat) {
                        $("#rob").show();
                        tips('该你抢地主了');
                    }
                    else {
                        tips('等待座位'+data.call+'的玩家抢地主');
                    }
                }
            })

            client.push('user-online',function (data) {

            });
            client.push('play-start',function (data) {
                var div = $("#poker");
                $.each(data.poker,function(k,poker){
                    div.append('<img id="'+poker.id+'" val="'+poker.name+'" src="card/'+poker.id+'.png">');
                });
                $("#prepare-a").html('');
                $("#prepare-b").html('');
                $("#prepare-c").html('');
                $("#ready").hide();
                //叫地主
                if(data.call == user.seat) {
                    $("#rob").show();
                    tips('该你叫地主了！');
                }
                else {
                    tips('等待座位'+data.call+'的玩家叫地主！');
                }
                $.each(['a','b','c'],function (i,v) {
                    $("#prepare-" + v).html('17');
                })
            })

            client.push('play-end',function (data) {
                $win = data.landowner?(landowner == user.seat?true:false):(landowner == user.seat?false:true)
                if($win) {
                    $('#end-info-title').html('恭喜你赢了');
                }
                else {
                    $('#end-info-title').html('你输了');
                }
                $info = '';
                $.each(['a','b','c'],function (i,v) {
                    $info += '<li class="list-group-item">玩家'+v+': '+data[v]+'</li>';
                })
                $('#end-info-content').append($info);
                $('#end-info').modal();
                $('#end-info').on('hidden.bs.modal', function (e) {
                    tips('请准备游戏！');
                    $("#ready").show();
                    $("#poker").html('');
                    $.each(['a','b','c'],function (i,v) {
                        $("#" + v + " .info").html('');
                        $("#prepare-" + v).html('');
                    })
                })

            })

            client.listen('chat-room').reply(function (data) {
                if(data.code) {
                    alert(data.msg);
                    return;
                }
                drawChat(user.seat,$('#text').val())
                $('#text').val('');
                $("#info").scrollTop($("#info")[0].scrollHeight);
            }).push(function (data) {
                drawChat(data.seat,data.text)
                $("#info").scrollTop($("#info")[0].scrollHeight);
            })

            //取消/准备
            $("#ready").click(function(){
                client.send({
                    action:'play/ready',
                    token:token,
                });
            });

            //抢地主
            $("#rob .yes").click(function(){
                client.send({
                    action:'play/rob',
                    token:token,
                    rob:1
                });
            });

            //不抢
            $("#rob .no").click(function(){
                client.send({
                    action:'play/rob',
                    token:token,
                    rob:0
                });
            });

            $("#poker").on("click",'img',function(){
                var img = $(this);
                $alt = img.attr('alt');
                if(img.hasClass("active")) {
                    $(this).removeClass("active");
                }
                else {
                    $(this).addClass("active");
                }
            });

            //过
            $("#lead .no").click(function(){
                lead = [];
                client.send({
                    action:'play/lead',
                    token:token,
                    poker:''
                });
            });

            //出牌
            $("#lead .yes").click(function(){
                if(leadlock) {
                    return;
                }
                lead = [];
                var ids = '',d = '';
                $('.active').each(function(){
                    var id = $(this).attr("id");
                    var val = $(this).attr("val");
                    lead.push({
                        id:id,
                        val:val
                    });
                    ids += d+id;
                    d = ',';
                });
                if(!ids) {
                    return;
                }
                client.send({
                    action:'play/lead',
                    token:token,
                    poker:ids
                });
            });

            //退出房间
            $("#exit").click(function(){
                client.send({
                    token:token,
                    action:'room/quit',
                });
            });

            //发送
            $("#send").click(function(){
                var msg = $('#text').val();
                if(!msg) {
                    return;
                }
                client.send({
                    action:'chat/room',
                    token:token,
                    text:msg
                });
            });
        }
    }
</script>
<style>
    .form-control:focus {
        outline: none;
        box-shadow: none;
        border-color: #ced4da;
    }
    .card-footer {
        padding:0;
        background-color:unset;
    }
    .form-control {
        display: inline-block;
    }
    .card-footer {
        border: 0;
    }
    .card-footer .btn {
        width: 100%;
    }
    .list-group-item {
        border:none;
        padding: .25rem .15rem;
        position: relative;
        display: block;
        margin-bottom: -1px;
        background-color: #fff;
    }
    .poker img {
        display: inline-block;
        width: 4.5rem;
        height: 6.2rem;
        margin: .25rem .05rem;
        background-color: #f5f5f5;
        vertical-align: unset;
        border: 2px solid;
        cursor: pointer;
    }
    .active {
        border-color: blue!important;
    }
    #info {
        height: 120px;
        overflow-y: scroll;
        width: 100%;
    }
    .console span {
        cursor: pointer;
    }
    #send {
        cursor: pointer;
    }
    .card .card-header {
        padding: .25rem 0.6rem;
    }
    .card .card-body {
        padding: .25rem 0.6rem .55rem;

    }
    .landowner strong{
        color: red;
    }
</style>
