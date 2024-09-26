import { defineStore } from 'pinia';
import { ref } from 'vue';

import server from "../server/index";
import {
    BACKEND_URL
} from "../server/app";


let api = server(BACKEND_URL)

let initialState = {
    isLoggedIn: localStorage.getItem('isLoggedIn') || false,
    user: localStorage.getItem('user') || null,
    token: localStorage.getItem('token') || null,
    roles: [],
};

export default defineStore('authStore', {
    state: () => initialState,

    getters: {
        getUser() {
            try {
                return this.user ?? null;
            } catch (error) {
                return null;
            }
        },
        user_id() {
            try {
                return this.user.id ?? null;
            } catch (error) {
                return null;
            }
        },
    },

    actions: {
        setIsLoggedIn(value) {
            this.isLoggedIn = value;
            localStorage.setItem('isLoggedIn', value);
        },
        setUser(user) {
            this.user = user;
            localStorage.setItem('user', user);
        },
        setProfile(path) {
            this.user.path = path;
            localStorage.setItem('user', this.user);
        },

        setToken(token) {
            this.token = token;
            localStorage.setItem('token', token);
        },

        logout() {
            this.setIsLoggedIn(false);
            this.setUser(null);
            this.setToken(null);
        },
    },
});

export const useAuthStore = defineStore("auth", () => {
    const user = ref(JSON.parse(localStorage.getItem('user')) || null);
    const token = ref(localStorage.getItem('token') || null);
    const isLoggedIn = ref(localStorage.getItem('isLoggedIn') || false);
    //actions
    const setIsLoggedIn =(value) => {
        isLoggedIn.value = value;
        localStorage.setItem('isLoggedIn', value);
    }
    const setUser =(value) => {
        user.value = value;
        console.log(value);
        localStorage.setItem('user', JSON.stringify(value));
    }
    const setProfile =(path) => {
        user.value.path = path;
        localStorage.setItem('user',JSON.stringify(user.value));
    }

    const setToken =(value) => {
        token.value = value;
        localStorage.setItem('token', value);
    }

    const login = async (email,password) => {
        const res = await api.post('api/login', {
            email,
            password
        });
        console.log('email',res);
        if(res.status == 200) {
            setUser(res.data.user);
            setToken(res.data.authorisation.token);
            setIsLoggedIn(true);
        }
        return res;
    };
    const logout = async () => {
        var response = await api.post('api/logout');
        return response;
    };
    const emptyLogout = async () => {
        setIsLoggedIn(0);
        setUser(null);
        setToken(null);
    };
    const editInfo = async (payload, id) => {
        const response = await api.post(`api/user/${id}/update`, payload).then(res => {
            if(res.status == 200) {
                user.value.firstName = payload.firstName
                user.value.lastName = payload.lastName
                user.value.email = payload.email
            }
        });

        return response
    };
    const editPassword = async (payload, id) => {
        const response = await api.post(`api/user/${id}/password`, payload);

        return response
    };
    const editUserProfile = async (payload, id) => {
        const response = await api.post(`api/user/${id}/profile`, payload);
        setProfile(response.data.data)

        return response
    };
    const isAuth = async () => {
        await api.get("/api/user").then(res => console.log(res));
    }
    return { user, isLoggedIn, login, logout, setIsLoggedIn, setUser, setProfile, setToken, editInfo, editPassword, editUserProfile, isAuth, emptyLogout };
  }); 