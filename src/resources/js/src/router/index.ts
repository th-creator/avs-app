import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { useAppStore } from '../stores/index';
import appSetting from '../app-setting';
import {useAuthStore} from '../stores/auth.js';

import HomeView from '../pages/Home.vue';

const routes: RouteRecordRaw[] = [
    // dashboard
    { path: '/', name: 'home', component: HomeView },
    {
        path: '/settings',
        name: 'settings',
        component: () => import('../pages/settings/Settings.vue'),
    },
    {
        path: '/users',
        name: 'users',
        component: () => import('../pages/users/Users.vue'),
    },
    {
        path: '/registrants',
        name: 'registrants',
        component: () => import('../pages/registrants/Registrants.vue'),
    },
    {
        path: '/students',
        name: 'students',
        component: () => import('../pages/students/Students.vue'),
    },
    {
        path: '/teachers',
        name: 'teachers',
        component: () => import('../pages/teachers/Teachers.vue'),
    },
    {
        path: '/options',
        name: 'options',
        component: () => import('../pages/sections/Sections.vue'),
    },
    {
        path: '/groups',
        name: 'groups',
        component: () => import('../pages/groups/Groups.vue'),
    },
    {
        path: '/expanses',
        name: 'expanses',
        component: () => import('../pages/expanses/expanses.vue'), 
    },
    {
        path: '/Facturation',
        name: 'Facturation',
        component: () => import('../pages/invoices/Invioces.vue'),
    },
    {
        path: '/groups/:id/registrants',
        name: 'groupsRegistrants',
        component: () => import('../pages/groups/registrants/GroupsRegistrants.vue'),
    },
    {
        path: '/students/:id/payments',
        name: 'studentPayments',
        component: () => import('../pages/students/payments/StudentPayments.vue'),
    },
 
    {
        path: '/pages/error404',
        name: 'error404',
        component: () => import(/* webpackChunkName: "pages-error404" */ '../views/pages/error404.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/pages/error500',
        name: 'error500',
        component: () => import(/* webpackChunkName: "pages-error500" */ '../views/pages/error500.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/pages/error503',
        name: 'error503',
        component: () => import(/* webpackChunkName: "pages-error503" */ '../views/pages/error503.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/pages/maintenence',
        name: 'maintenence',
        component: () => import(/* webpackChunkName: "pages-maintenence" */ '../views/pages/maintenence.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/auth/login',
        name: 'login',
        component: () => import(/* webpackChunkName: "auth-cover-login" */ '../pages/auth/Login.vue'),
        meta: { layout: 'auth' },
    },
];

const router = createRouter({
    history: createWebHistory(),
    linkExactActiveClass: 'active',
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { left: 0, top: 0 };
        }
    },
});

router.beforeEach(async (to, from, next) => {
    const store = useAppStore();
    const authStore = useAuthStore();
    const isLoggedIn: any = authStore.isLoggedIn;

    console.log('Navigating to:', to.name);
    console.log('Is Logged In:', isLoggedIn);

    // User is not logged in
    if (isLoggedIn == 0) {
        console.log(isLoggedIn);
        
        if (to.name === 'login') {
            // Allow access to the login page
            next();
        } else {
            // Redirect to the login page
            next({ name: 'login' });
        }
    } else if(isLoggedIn != 0) {
        if (to.name === 'login') {
            // User is logged in, redirect to home if trying to access login
            next({ name: 'home' });
        } else {
            // Allow access to the requested route
            next();
        }
    }

    // Set layout based on route meta
    if (to?.meta?.layout === 'auth') {
        store.setMainLayout('auth');
    } else {
        store.setMainLayout('app');
    }
});

router.afterEach((to, from, next) => {
    appSetting.changeAnimation();
});
export default router;
