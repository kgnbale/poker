import index from './views/index.vue'
import app from './views/app.vue'
import login from './views/login.vue'
import register from './views/register.vue'
import create from './views/Create.vue'
import play from './views/Play.vue'

export default {
    routes: [{
        path: '/',
        component: login
      },{
        path: '/register',
        component: register
      },{
        path: '/create',
        component: create
      },{
        path: '/index',
        component: index
      },{
        path: '/play',
        component: play
      }, {
        path: '/app',
        component: app
    }]
}