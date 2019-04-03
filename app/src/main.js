import Vue from "vue"
import VueRouter from "vue-router"
import App from "./app.vue"
import routerConfig from "./router"
import './assets/css/bootstrap.css'
import './assets/css/style.css'

Vue.use(VueRouter)
const router = new VueRouter(routerConfig)

new Vue({
    el: "#app",
    router: router,
    render: h => h(App)
})