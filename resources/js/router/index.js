import { verifyToken } from '@api/auth';
import { useStore } from 'vuex';

import {
    createRouter,
    createWebHistory
} from 'vue-router'


const routes = [
    {
        path: "/",
        name: "home",
        component: () => import('@views/Home.vue'),
    },
    {
        path: "/login",
        name: "login",
        component: () => import('@components/Auth/Login.vue'),
    },
    {
        path: "/register",
        name: "register",
        component: () => import('@components/Auth/Register.vue'),
    },
    {
        path: "/profile",
        name: "profile",
        component: () => import('@views/Profile.vue'),
        meta: {
            requiresAuth: true
        }
    },
]

const router = createRouter({
    mode: 'hash',
    history: createWebHistory(),
    routes,
})

router.beforeEach(async (to, from, next) => {
    const store = useStore();
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);

    if (!requiresAuth) return next();

    if (!store.getters.isAuthenticated) {
        store.dispatch('clearAuth');
        return next('/login');
    }

    const isTokenValid = await verifyToken(store.getters.token);
    if (!isTokenValid) {
        store.dispatch('clearAuth');
        return next('/login');
    }

    next();
});

export default router;
