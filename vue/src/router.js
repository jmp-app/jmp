import Vue from 'vue';
import Router from 'vue-router';
import EventOverview from './views/event/Overview.vue';

Vue.use(Router);

export default new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    routes: [
        {
            path: '/',
            name: 'eventOverview',
            component: EventOverview
        },
        {
            path: '/home',
            name: 'home',
            component: function () {
                return import('./views/Home.vue');
            }
        },
        {
            path: '/about',
            name: 'about',
            // route level code-splitting
            // this generates a separate chunk (about.[hash].js) for this route
            // which is lazy-loaded when the route is visited.
            component: function () {
                return import(/* webpackChunkName: "about" */ './views/About.vue');
            }
        }
    ]
});
