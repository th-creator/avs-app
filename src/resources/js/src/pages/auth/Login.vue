<template>
    <div class="flex min-h-screen">
        <div
            class="bg-gradient-to-t from-[#ff1361bf] to-[#44107A] w-1/2 min-h-screen hidden lg:flex flex-col items-center justify-center text-white dark:text-black p-4"
        >
            <div class="w-full mx-auto mb-5">
                <img src="/assets/images/auth-cover.svg" alt="coming_soon" class="lg:max-w-[370px] xl:max-w-[500px] mx-auto" />
            </div>
            <h3 class="text-3xl font-bold mb-4 text-center">Join the community of expert developers</h3>
            <p>It is easy to setup with great customer experience. Start your 7-day free trial</p>
        </div>
        <div class="w-full lg:w-1/2 relative flex justify-center items-center">
            <div class="w-[480px] p-5 md:p-10 border shadow-md">
                <h2 class="font-bold text-3xl mb-3">Sign In</h2>
                <p class="mb-7">Enter your email and password to login</p>
                <form class="space-y-5" @submit.prevent="login()">
                    <div>
                        <label for="email">Email</label>
                        <input v-model="creds.email" id="email" type="email" class="form-input" placeholder="Enter Email" />
                        <span v-if="errors.email" class="text-red-600">{{ errors.email[0] }}</span>
                    </div>
                    <div>
                        <label for="password">Password</label>
                        <input v-model="creds.password" id="password" type="password" class="form-input" placeholder="Enter Password" />
                        <span v-if="errors.password" class="text-red-600">{{ errors.password[0] }}</span>
                    </div>
                    <button type="submit" class="btn btn-primary w-full h-10">
                        <IconComponent v-if="isLoading" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" name="loading" />
                        <span v-else>SIGN IN</span>
                    </button>
                </form>
                <div
                    class="relative my-7 h-5 text-center before:w-full before:h-[1px] before:absolute before:inset-0 before:m-auto before:bg-[#ebedf2] dark:before:bg-[#253b5c]"
                >
                    <div class="font-bold text-white-dark bg-[#fafafa] dark:bg-[#060818] px-2 relative z-[1] inline-block"><span>OR</span></div>
                </div>
                <p class="text-center">
                    Dont't have an account ? <router-link to="/auth/cover-register" class="text-primary font-bold hover:underline">Sign Up</router-link>
                </p>
            </div>
        </div>
    </div>
</template>
<script setup>
    import { useRouter } from 'vue-router';
    import { ref } from 'vue';
    import useAuth, {useAuthStore} from '@/stores/auth.js';
    import IconComponent from '@/components/icons/IconComponent.vue'

    const authStore = useAuthStore()
    const auth = useAuth()
    
    const router = useRouter();

    const isLoading = ref(false)
    const creds = ref({
        email: '',
        password: ''
    })
    const errors = ref({
        email: '',
        password: ''
    })
    const login = () => {
        errors.value = []
        isLoading.value = true
        authStore.login(creds.value.email,creds.value.password).then((res) => {
            if(res.status == 200) {
                router.push('/')
            }
        }).catch(err => {
            if(err.status == 422) {
                errors.value =  err.response.data.errors;
            } else {
                errors.value.email =  ['invalid credentials'];
            }
        }).finally(() => isLoading.value = false)
    }
</script>
