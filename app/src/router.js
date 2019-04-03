import index from './views/index.vue'
import app from './views/app.vue'
import login from './views/login.vue'
import register from './views/register.vue'
export default {
    routes: [{
        path: '/',
        component: login
      },{
        path: '/register',
        component: register
      },{
        path: '/index',
        component: index
      }, {
        path: '/app',
        component: app
    }]
}