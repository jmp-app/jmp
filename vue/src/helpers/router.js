import Vue from 'vue';
import Router from 'vue-router';
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
            path: '/event/:id',
            name: 'eventDetail',
            component: function () {
                return import('@/views/event/Detail.vue');
            }
        },
        {
            path: '/login',
            name: 'login',
            component: function () {
                return import('../views/Login.vue');
            }
        },
        {
            path: '/change-password',
            name: 'changePassword',
            component: function () {
                return import('../views/ChangePassword.vue');
            }
        },
        {
            path: '/users',
            name: 'userOverview',
            component: function () {
                return import('../views/user/Overview.vue');
            }
        },
        {
            path: '/users/:id',
            name: 'userDetail',
            component: function () {
                return import('../views/user/Detail.vue');
            }
        },
        {
            path: '/groups',
            name: 'groupOverview',
            component: function () {
                return import('../views/group/Overview.vue');
            }
        },
        {
            path: '/groups/:id',
            name: 'groupDetail',
            component: function () {
                return import('../views/group/Detail.vue');
            }
        },
        {
            path: '/groups/:id/addMembers/',
            name: 'groupAddMember',
            component: function () {
                return import('../views/group/AddMembers.vue');
            }
        }
    ]
});

router.beforeEach((to, from, next) => {
    // redirect to login page if not logged in and trying to access a restricted page
    const publicPages = ['/login'];
    const authRequired = !publicPages.includes(to.path);
    const loggedIn = localStorage.getItem('user');

    if (authRequired && !loggedIn) {
        return next('/login');
    }

    next();
});
