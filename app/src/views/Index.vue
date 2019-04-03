<template>
    <div class="view">
        <div><router-link to="/app">App</router-link></div>
        这是{{page}}页面
        <img src="../assets/images/vue.png">
    </div>
</template>
<script>
    import {getCookie} from '../cookie.js'
    export default {
        data: function () {
            return {
                page: 'index'
            }
        },
        created() {
            console.log('token',getCookie('token'))
        }
    }
</script>
