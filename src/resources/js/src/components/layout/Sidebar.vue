<template>
    <div :class="{ 'dark text-white-dark': store.semidark }">
        <nav class="sidebar fixed min-h-screen h-full top-0 bottom-0 w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] z-50 transition-all duration-300">
            <div class="bg-white dark:bg-[#0e1726] h-full">
                <div class="flex justify-between items-center px-4 py-3">
                    <div></div>
                    <router-link to="/" class="main-logo flex items-center justify-center shrink-0">
                        <img class="w-32 ml-[5px] flex-none" src="/assets/images/avs-logo.png" alt="" />
                        <!-- <span class="text-2xl ltr:ml-1.5 rtl:mr-1.5 font-semibold align-middle lg:inline dark:text-white-light">AVS</span> -->
                    </router-link>
                    <a
                        href="javascript:;"
                        class="collapse-icon w-8 h-8 rounded-full flex items-center hover:bg-gray-500/10 dark:hover:bg-dark-light/10 dark:text-white-light transition duration-300 rtl:rotate-180 hover:text-primary"
                        @click="store.toggleSidebar()"
                    >
                        <svg class="w-5 h-5 m-auto" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                opacity="0.5"
                                d="M16.9998 19L10.9998 12L16.9998 5"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </a>
                </div>
                <perfect-scrollbar
                    :options="{
                        swipeEasing: true,
                        wheelPropagation: false,
                    }"
                    class="h-[calc(100vh-80px)] relative mt-8"
                >
                    <ul class="relative font-semibold space-y-0.5 p-4 py-0">
                        <li class="nav-item">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <svg
                                        class="group-hover:!text-primary shrink-0"
                                        width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            opacity="0.5"
                                            d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z"
                                            fill="currentColor"
                                        />
                                        <path
                                            d="M9 17.25C8.58579 17.25 8.25 17.5858 8.25 18C8.25 18.4142 8.58579 18.75 9 18.75H15C15.4142 18.75 15.75 18.4142 15.75 18C15.75 17.5858 15.4142 17.25 15 17.25H9Z"
                                            fill="currentColor"
                                        />
                                    </svg>

                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">Accueil</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/registrants" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <IconComponent name="users"/>                                            
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">inscrits</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/students" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <IconComponent name="students"/>                                            
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">élèves</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/teachers" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <IconComponent name="teachers"/>                                            
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">Enseignants</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item" v-if="authStore?.user && authStore?.user?.roles[0]?.name == 'admin'">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/users" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <IconComponent name="employees"/>  
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">Employés</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/expanses" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <IconComponent name="expanses"/>  
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">Dépenses</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <!-- <li class="nav-item">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/teacherExpanses" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <IconComponent name="teacherExpanses"/>  
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">Prof Dépenses</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </li> -->
                        <li class="nav-item" v-if="authStore?.user && authStore?.user?.roles[0]?.name == 'admin'">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/Finance" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <IconComponent name="finance"/>  
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">Finance</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item" v-if="authStore?.user && authStore?.user?.roles[0]?.name == 'admin'">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/facturation" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <IconComponent name="invoices"/>  
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">Facturation</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/receipt" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <IconComponent name="receipt"/>  
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">Reçu</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/options" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <IconComponent name="sections"/>  
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">Options</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/groups" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <IconComponent name="groups"/>  
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">Groupes</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/settings" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <IconComponent name="settings"/>
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">paramètres</span>
                                        </div>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </perfect-scrollbar>
            </div>
        </nav>
    </div>
</template>

<script setup>
    import { ref, onMounted } from 'vue';

    import { useAppStore } from '@/stores/index';
    import VueCollapsible from 'vue-height-collapsible/vue3';
    import IconComponent from '@/components/icons/IconComponent.vue'
    import {useAuthStore} from '@/stores/auth.js';

    const authStore = useAuthStore();
    const store = useAppStore(); 
    const activeDropdown = ref('');
    const subActive = ref('');

    onMounted(() => {
        const selector = document.querySelector('.sidebar ul a[href="' + window.location.pathname + '"]');
        if (selector) {
            selector.classList.add('active');
            const ul = selector.closest('ul.sub-menu');
            if (ul) {
                let ele = ul.closest('li.menu').querySelectorAll('.nav-link') || [];
                if (ele.length) {
                    ele = ele[0];
                    setTimeout(() => {
                        ele.click();
                    });
                }
            }
        }
    });

    const toggleMobileMenu = () => {
        if (window.innerWidth < 1024) {
            store.toggleSidebar();
        }
    };
</script>
