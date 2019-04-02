import index from './views/index.vue'
import app from './views/app.vue'
export default {
    routes: [
        {
            path: '/index',
            component: index
        },
        {
            path: '/app',
            component: app
        }
    ]
}