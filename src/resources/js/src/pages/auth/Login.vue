<template>
    <div class="flex min-h-screen">
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
