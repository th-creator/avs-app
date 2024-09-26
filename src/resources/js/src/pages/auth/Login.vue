<template>
    <div
        class="flex justify-center items-center min-h-screen bg-[url('/assets/images/library-with-books_1063-98.avif')] dark:bg-[url('/assets/images/library-with-books_1063-98.avif')] bg-cover bg-center"
    >
        <div class="panel sm:w-[480px] m-6 p-8 max-w-lg w-full">
            <div class="flex justify-center items-center mb-6">
                <img class="w-44 ml-[5px] flex-none" src="/assets/images/avs-logo.png" alt="" />
            </div>
            <!-- <h2 class="font-bold text-2xl mb-3 text-center">SE CONNECTER</h2>
            <p class="mb-7">Entrez votre adresse e-mail et votre mot de passe pour vous connecter</p> -->
            <div class="space-y-5" @submit.prevent="router.push('/')">
                <div>
                    <label for="email">E-mail</label>
                    <input 
                        id="email"
                        v-model="creds.email"
                        @keyup.enter="passwordInput.focus()"
                        placeholder="Entrer l'adresse e-mail"
                        :class="[!!errors.email && '!border-rose-500']"
                        type="email" class="form-input" />
                        <span v-if="errors.email" :class="[!!errors.email && '!opacity-100']" class="text-xs text-rose-500 opacity-0 duration-200 h-2 block mt-1">{{ errors.email[0] }}</span>
                </div>
                <div>
                    <label for="password">Mot de passe</label>
                    <input 
                        ref="passwordInput"
                        @keyup.enter="login()"
                        v-model="creds.password"
                        :class="[!!errors.password && '!border-rose-500']"
                        id="password" type="password" class="form-input" placeholder="Entrer le mot de passe" />
                            <span v-if="errors.password" :class="[!!errors.password && '!opacity-100']" class="text-xs text-rose-500 opacity-0 duration-200 h-2 block mt-1">{{ errors.password[0] }}</span>
                </div>
                <button type="submit" class="btn btn-primary w-full h-10" @click="login()">
                    <IconComponent v-if="isLoading" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" name="loading" />
                    <span v-else>SE CONNECTER</span>
                </button>
            </div>
        </div>
    </div>
    <!-- <div class="flex min-h-screen">
        <div
            class="bg-cover bg-center w-1/2 min-h-screen hidden lg:flex flex-col items-center justify-center text-white dark:text-black p-4" style="background-image: url('/assets/images/library-with-books_1063-98.avif')"
        >
        </div>
        <div class="w-full lg:w-1/2 relative flex justify-center items-center">
            <div class="w-[480px] p-5 md:p-10 border shadow-md">
                <h2 class="font-bold text-3xl mb-3">Se connecter</h2>
                <p class="mb-7">Entrez votre email et mot de passe pour vous connecter</p>
                <form class="space-y-5" @submit.prevent="login()">
                    <div>
                        <label for="email">Email</label>
                        <input v-model="creds.email" id="email" type="email" class="form-input" placeholder="Entrez Email" />
                        <span v-if="errors.email" class="text-red-600">{{ errors.email[0] }}</span>
                    </div>
                    <div>
                        <label for="password">Mot de passe</label>
                        <input v-model="creds.password" id="password" type="password" class="form-input" placeholder="Entrez Mot de passe" />
                        <span v-if="errors.password" class="text-red-600">{{ errors.password[0] }}</span>
                    </div>
                    <button type="submit" class="btn btn-primary w-full h-10">
                        <IconComponent v-if="isLoading" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" name="loading" />
                        <span v-else>SE CONNECTER</span>
                    </button>
                </form>
            </div>
        </div>
    </div> -->
</template>
<script setup>
    import { useRouter } from 'vue-router';
    import { ref } from 'vue';
    import {useAuthStore} from '@/stores/auth.js';
    import IconComponent from '@/components/icons/IconComponent.vue'

    const authStore = useAuthStore()
    const passwordInput = ref(null);
    
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
