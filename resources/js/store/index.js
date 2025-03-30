import { createStore } from 'vuex';

const store = createStore({
    state: {
        configuration: JSON.parse(localStorage.getItem('configuration')) || {},
        userData: JSON.parse(sessionStorage.getItem('user')) || {},
        token: sessionStorage.getItem('token') || '',
    },
    mutations: {
        setConfiguration(state, payload) {
            state.configuration = payload;
            localStorage.setItem('configuration', JSON.stringify(payload));
        },
        setUserData(state, payload) {
            state.userData = payload;
            sessionStorage.setItem('user', JSON.stringify(payload));
        },
        setToken(state, payload) {
            state.token = payload;
            sessionStorage.setItem('token', payload);
        },
        setAuth(state, payload) {
            state.token = payload.token;
            state.userData = payload.user;

            sessionStorage.setItem('token', payload.token);
            sessionStorage.setItem('user', JSON.stringify(payload.user));
        },
    },
    actions: {
        updateConfiguration({ commit }, payload) {
            commit('setConfiguration', payload);
        },
        updateUserData({ commit }, payload) {
            commit('setUserData', payload);
        },
        updateToken({ commit }, payload) {
            commit('setToken', payload);
        },
        updateAuth({ commit }, payload) {
            commit('setAuth', payload);
        },
        clearAuth({ commit }) {
            commit('setToken', '');
            commit('setUserData', {});
            sessionStorage.clear();
        },
    },
    getters: {
        configuration: (state) => state.configuration,
        userData: (state) => state.userData,
        token: (state) => state.token,
        isAuthenticated: (state) => !!state.token && Object.keys(state.userData).length > 0,
    },
});

export default store;