import Vue from 'vue';
import Router from 'vue-router';
import Home from '../views/Home.vue';
import EventOverview from '../views/event/Overview.vue';

Vue.use(Router);

export const router = new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    routes: [
        {
            path: '/',
            name: 'eventOverview',
            component: EventOverview
        },
        {
            path: '/sample-home',
            name: 'home',
            component: function () {
                return import('../views/Home.vue');
            }
        },
        {
            path: '/sample-about',
            name: 'about',
            // route level code-splitting
            // this generates a separate chunk (about.[hash].js) for this route
            // which is lazy-loaded when the route is visited.
            component: function () {
                return import(/* webpackChunkName: "about" */ '../views/About.vue');
            }
        },
        {
            path: '/login',
            name: 'login',
            component: function () {
                return import('../views/Login.vue');
            }
        }
    ]
});

router.beforeEach((to, from, next) => {
// redirect to login page if not logged in and trying to access a restricted page
    const publicPages = ['/login']; // TODO: Add page for testing
    const authRequired = !publicPages.includes(to.path);
    const loggedIn = localStorage.getItem('user');

    if (authRequired && !loggedIn) {
        return next('/login');
    }

    next();
});
