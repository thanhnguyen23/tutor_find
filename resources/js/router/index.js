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
        component: () => import('@views/ProfileTutorNew.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: "/profile-old",
        name: "profile-old",
        component: () => import('@views/ProfileTutor.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: "/tutor/:uid",
        name: "tutor",
        component: () => import('@views/UserDetail.vue'),
    },
    {
        path: "/booking/:uid",
        name: "booking",
        component: () => import('@views/Booking/index.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: "/booking/manager",
        name: "booking-manager",
        component: () => import('@views/Booking/BookingManager.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: "/booking/success/:id",
        name: "booking-success",
        component: () => import('@components/Booking/BookingSuccess.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: "/search",
        name: "search",
        // Nhận các query param: city, subject, level, experience
        component: () => import('@views/Search.vue'),
    },
    {
        path: "/notification",
        name: "notification",
        component: () => import('@views/Notification.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: "/message",
        name: "message",
        component: () => import('@views/Message.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: "/zoom",
        name: "zoom",
        component: () => import('@views/Zoom.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: "/classroom-manager",
        name: "classroom-manager",
        component: () => import('@views/ClassRoom/Index.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: "/classroom/:id",
        name: "classroom-room",
        component: () => import('@views/ClassRoom/ClassRoom.vue'),
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

    // Chặn booking chính mình
    if (to.name === 'booking') {
        const myUid = store.getters?.user?.uid || localStorage.getItem('uid');
        if (to.params.uid === myUid) {
            // Có thể dùng notification/toast ở đây nếu muốn
            return next({ name: 'home' });
        }
    }

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
