import Vue from 'vue';
import Router from 'vue-router';
import {store} from '../store';

Vue.use(Router);

export const router = new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    routes: [
        {
            path: '/',
            name: 'eventOverview',
            component: function () {
                return import('@/views/event/Overview.vue');
            }
        },
        {
            path: '/calendar',
            name: 'eventCalendar',
            component: function () {
                return import('@/views/event/EventCalendar.vue');
            }
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
        },
        {
            path: '/events',
            name: 'adminEventOverview',
            component: function () {
                return import('../views/event/admin/Overview.vue');
            }
        },
        {
            path: '/events/:id',
            name: 'adminEventDetail',
            component: function () {
                return import('../views/event/admin/Detail.vue');
            }
        },
        {
            path: '/events/:id/registrations',
            name: 'adminEventRegistrations',
            component: function () {
                return import('../views/event/admin/Registrations.vue');
            }
        },
        {
            path: '/eventTypes',
            name: 'adminEventTypeOverview',
            component: function () {
                return import('../views/eventType/admin/Overview');
            }
        },
        {
            path: '/eventTypes/:id',
            name: 'adminEventTypeDetail',
            component: function () {
                return import('../views/eventType/admin/Detail');
            }
        },
        {
            path: '/registrationStates',
            name: 'adminRegistrationStateOverview',
            component: function () {
                return import('../views/registrationState/admin/Overview');
            }
        },
        {
            path: '/registrationStates/:id',
            name: 'adminRegistrationStateDetail',
            component: function () {
                return import('../views/registrationState/admin/Detail');
            }
        }
    ]
});

router.beforeEach((to, from, next) => {
    // redirect to login page if not logged in and trying to access a restricted page
    const publicPages = ['/login'];
    const userPages = ['eventOverview', 'eventDetail', 'changePassword'];
    const authRequired = !publicPages.includes(to.path);
    const adminRightsRequired = !userPages.includes(to.name);
    const loggedIn = localStorage.getItem('token');

    if (authRequired && !loggedIn) {
        return next('/login');
    }

    if (loggedIn) {
        const isAdmin = store.state.authentication.user.isAdmin;
        if (adminRightsRequired && !isAdmin) {
            next(from); // TODO: Navigate to a "Not Allowed" page
        }
    }
    next();
});
