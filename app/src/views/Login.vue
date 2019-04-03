<template>
    <form id="form" class="form-signin" @submit.prevent="submit">
        <div class="mb-4"></div>
        <h1 class="h3 mb-3 font-weight-normal">请登录</h1>
        <label class="sr-only">用户名</label>
        <input type="text" v-model="logins.name"  name="name" class="form-control" placeholder="用户名" required autofocus>
        <label class="sr-only">密码</label>
        <input name="pass" v-model="logins.pass" type="password" class="form-control" placeholder="密码" required>

        <div class="checkbox mb-3">
            <label>
                没有账号，请<router-link to="/register">前往注册！</router-link>
            </label>
        </div>
        <button type="submit" class="btn btn-lg btn-primary btn-block">登录</button>
        <p class="mt-5 mb-3 text-muted">&copy; 2017-2018</p>
    </form>
</template>
<script>
    import axios from "axios"
    import Qs from 'qs'
    import {setCookie} from '../cookie.js'
    export default {
        data: function () {
            return {
                page: 'index',
                logins:{}
            }
        },
        methods: {
            submit:function(){
                axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8'
                axios.post('http://127.0.0.1:9503/login', Qs.stringify({
                    name:this.logins.name,
                    pass:this.logins.pass
                }))
                .then((res) => {
                    var data = res.data;
                    if(data.code) {
                        alert(data.msg);
                    }
                    else {
                        console.log(data);
                        setCookie('token',data.token)
                        this.$router.push('/index')
                    }
                }).then((err)=>{
                    console.log(err);
                });
            }
        }
    }
</script>
<style>
    html,
    body {
        height: 100%;
    }
    body {
        display: -ms-flexbox;
        display: -webkit-box;
        display: flex;
        -ms-flex-align: center;
        -ms-flex-pack: center;
        -webkit-box-align: center;
        align-items: center;
        -webkit-box-pack: center;
        justify-content: center;
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
    }

    .form-signin {
        width: 100%;
        max-width: 330px;
        padding: 15px;
        margin: 0 auto;
    }
    .form-signin .checkbox {
        font-weight: 400;
    }
    .form-signin .form-control {
        position: relative;
        box-sizing: border-box;
        height: auto;
        padding: 10px;
        font-size: 16px;
    }
    .form-signin .form-control:focus {
        z-index: 2;
    }
    .form-signin input[type="text"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }
    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
</style>
