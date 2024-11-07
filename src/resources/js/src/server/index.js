import axios from "axios";
import useAuthStore  from '@/stores/auth';
import { useAlert } from '../composables/useAlert';
const defaultOptions = {
    Accept:'application/json',
}

export default (url, options = defaultOptions) => {

    const api = axios.create({
        baseURL: url,
    });

    api.interceptors.request.use((config) => {
        const token = localStorage.getItem('token');

        config.headers.Authorization = `Bearer ${token}`;
        config.headers.Accept = options.Accept;
        return config;
    })
    api.interceptors.response.use(
        res => res,
        async err => {
            const authStore = useAuthStore();
            if(err?.response?.status == 500 && err?.response?.data?.message == 'Token has expired') {
                useAlert('danger', 'La session a expiré!');
                authStore.logout();
                useRouter().push({name: 'auth-login'})
            }
            if(err?.response?.status == 401 && err?.response?.data?.message == 'Token has expired') {
                useAlert('danger', 'La session a expiré!');
                // authStore.emptyLogout()
                await authStore.logout()
                localStorage.setItem('isLoggedIn', 0);
                localStorage.setItem('token', 0);
                localStorage.setItem('user', 0);
                window.location.href = '/auth/login';
            }
            return Promise.reject(err);
        }
    )
    return api;
};
