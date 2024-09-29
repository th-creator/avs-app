<template>
    <div>
        <div class="pt-5">
            <div class="flex items-center justify-between mb-5">
                <h5 class="font-semibold text-lg dark:text-white-light">Settings</h5>
            </div>
            <TabGroup>
                <TabList class="flex font-semibold border-b border-[#ebedf2] dark:border-[#191e3a] mb-5 whitespace-nowrap overflow-y-auto">
                    <Tab as="template" v-slot="{ selected }">
                        <a
                            href="javascript:;"
                            class="flex gap-2 p-4 border-b border-transparent hover:border-primary hover:text-primary !outline-none"
                            :class="{ '!border-primary text-primary': selected }"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                <path
                                    opacity="0.5"
                                    d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                />
                                <path d="M12 15L12 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                            Accueil
                        </a>
                    </Tab>
                </TabList>
                <TabPanels>
                    <TabPanel>
                        <div>
                            <form class="border border-[#ebedf2] dark:border-[#191e3a] rounded-md p-4 mb-5 bg-white dark:bg-[#0e1726]">
                                <h6 class="text-lg font-bold mb-5">Informations Générales</h6>
                                <div class="flex flex-col sm:flex-row">
                                    <div class="ltr:sm:mr-4 rtl:sm:ml-4 w-full sm:w-2/12 mb-5">
                                        <img :src="user.path" alt="" class="w-20 h-20 md:w-32 md:h-32 rounded-full object-cover mx-auto" />
                                        <div
                                            class="cursor-pointer relative mb-2 mt-5 flex w-full items-center justify-center rounded-full bg-primary-500 px-8 py-2.5 text-sm font-medium text-white shadow-md duration-200 hover:bg-primary-600 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                                        >
                                            <input @dragenter="uploadImage" @change="uploadImage" type="file" class="absolute h-full w-full rounded-full opacity-0 cursor-pointer" />
                                            <IconComponent class="cursor-pointer" :name="'edit'" />
                                        </div>
                                    </div>
                                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label for="name">Prénom</label>
                                            <input id="name" type="text" v-model="user.firstName" class="form-input" />
                                        </div>
                                        <div>
                                            <label for="name">Nom</label>
                                            <input id="name" type="text" v-model="user.lastName" class="form-input" />
                                        </div>
                                        <div>
                                            <label for="email">Email</label>
                                            <input id="email" type="email" v-model="user.email" class="form-input" />
                                        </div>
                                        <div>
                                            <label for="profession">Profession</label>
                                            <input id="profession" disabled type="text" v-model="user.role" class="form-input" />
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <div class="sm:col-span-2 mt-3">
                                        <button type="button" class="btn btn-primary" @click="Edit">Enregistrer</button>
                                    </div>
                                </div>
                            </form>
                            <form class="border border-[#ebedf2] dark:border-[#191e3a] rounded-md p-4 bg-white dark:bg-[#0e1726]">
                                <h6 class="text-lg font-bold mb-5">Sécurité</h6>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label for="profession">Nouveau mot de passe</label>
                                        <input id="profession" type="text" v-model="user.password" class="form-input"  :class="[!!errors.password && '!border-rose-500']"/>
                                        <span v-if="errors.password" :class="[!!errors.password[0] && '!opacity-100']" class="text-xs text-rose-500 opacity-0 duration-200 h-2 block mt-1">{{ errors.password[0] }}</span>
                                    </div>
                                    <div>
                                        <label for="profession">Répéter le mot de passe</label>
                                        <input id="profession" type="text" v-model="user.password_confirmation" class="form-input"  :class="[!!errors.password && '!border-rose-500']"/>
                                        <span v-if="errors.password" :class="[!!errors.password[0] && '!opacity-100']" class="text-xs text-rose-500 opacity-0 duration-200 h-2 block mt-1">{{ errors.password[0] }}</span>
                                    </div>
                                    <div>
                                        <label for="profession">Ancien mot de passe</label>
                                        <input id="profession" type="text" v-model="user.oldPassword" class="form-input"  :class="[!!errors.oldPassword || !!errors.ifOldPasswordNoMatch ? 'border-rose-500' : '']"/>
                                        <span v-if="errors.oldPassword" :class="[!!errors.oldPassword[0] && '!opacity-100']" class="text-xs text-rose-500 opacity-0 duration-200 h-2 block mt-1">{{ errors.oldPassword[0] }}</span>
                                        <span v-if="errors.ifOldPasswordNoMatch" :class="[!!errors.ifOldPasswordNoMatch[0] && '!opacity-100']" class="text-xs text-rose-500 opacity-0 duration-200 h-2 block mt-1">{{ errors.ifOldPasswordNoMatch }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <div class="sm:col-span-2 mt-3">
                                        <button type="button" class="btn btn-primary" @click="EditPassword">Enregistrer</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </TabPanel>
                </TabPanels>
            </TabGroup>
        </div>
    </div>
</template>
<script setup>
    import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
    import {useAuthStore} from '@/stores/auth.js';
    import { ref } from 'vue'
    import { useAlert } from '@/composables/useAlert';
    import IconComponent from '@/components/icons/IconComponent.vue'
    
    const authStore = useAuthStore();

    const user = ref({
        firstName: authStore.user.firstName,
        lastName: authStore.user.lastName,
        email: authStore.user.email,
        path: authStore.user.path,
        role: authStore.user.roles[0]?.name,
        password: '',
        password_confirmation: '',
        oldPassword: '',
    })

    const errors = ref([])

    const Edit = () => {
        errors.value = []
        
        if( user.value.firstName == '' || user.value.lastName == '' || user.value.email == '' ) {
            useAlert('warning', 'Fill in all the fields!');
            return
        }
        let payload = {
            firstName: user.value.firstName,
            lastName: user.value.lastName,
            email: user.value.email,
        }
        authStore.editInfo(payload,authStore.user.id).then(res => {
            useAlert('success', 'Edited successfully!');
        }).catch((err) => {
            if(err.status == 422) {
                errors.value =  err.response.data.errors;
            }
            useAlert('warning', 'Something went wrong!');
        });
    }

    async function EditPassword(){
        errors.value = []
        if( user.value.password == '' || user.value.password_confirmation == '' || user.value.oldPassword == '' ) {
            useAlert('warning', 'Fill in all the fields!');
            return
        }
        let payload = {
            password: user.value.password,
            password_confirmation: user.value.password_confirmation,
            oldPassword: user.value.oldPassword,
        }
        await authStore.editPassword(payload,authStore.user.id)
        .then( (response) =>
        {
            user.value.password = ''
            user.value.password_confirmation = ''
            user.value.oldPassword = ''
            console.log(response);
            useAlert('success', 'Updated succussfully')
        })
        .catch( (error) =>
        {
            console.log(error);
            if (error.response && error.response.data && error.response.data.errors)
            {
                errors.value = error.response.data.errors;
                useAlert('error', 'An error happened', 'danger')
            }
            else
            {
                useAlert('warning', 'Something went wrong!');
            }
        })
    }

    
    const uploadImage =  async(e)=>{
        var file = e.target.files
        let profile = file[0]
      
        const fd = new FormData()
        fd.append( "image" , profile) 

        await authStore.editUserProfile(fd,authStore.user.id).then(res => user.value.path = res.data.data)
    };
</script>
