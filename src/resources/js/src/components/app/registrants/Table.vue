<template>
    <div>
        <div class="panel pb-0 mt-6">
            <h5 class="font-semibold text-lg dark:text-white-light mb-5">Les Inscrits</h5>
            <div class="flex justify-between mb-4">    
                <input v-model="params.search" type="text" class="form-input max-w-xs" placeholder="Rechercher..." />
                <button type="button" class="btn btn-info" @click="showPopup = true">Ajouter</button>
            </div>
            <div class="datatable">
                <vue3-datatable
                    :rows="registrantsStore.registrants"
                    :columns="cols"
                    :totalRows="rows?.length"
                    :sortable="true"
                    :search="params.search" 
                    :sortColumn="params.sort_column"
                    :sortDirection="params.sort_direction"
                    skin="whitespace-nowrap bh-table-hover"
                    firstArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    lastArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg> '
                    previousArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    nextArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                >
                    <template #firstName="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.firstName }}</p>
                        </div>
                    </template>
                    <template #lastName="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.lastName }}</p>
                        </div>
                    </template>
                    <template #student_id="data">
                        <div class="flex justify-around w-full">
                            <a :href="`mailto:${data.value.student_id}`" class="text-primary hover:underline">{{ data.value.student_id }}</a>
                        </div>
                    </template>
                    <template #email="data">
                        <div class="flex justify-around w-full">
                            <a :href="`mailto:${data.value.email}`" class="text-primary hover:underline">{{ data.value.email }}</a>
                        </div>
                    </template>
                    <template #phone="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.phone }}</p>
                        </div>
                    </template>
                    <template #parent_phone="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.parent_phone }}</p>
                        </div>
                    </template>
                    <template #date="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.date }}</p>
                        </div>
                    </template>
                    <template #field="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.field }}</p>
                        </div>
                    </template>
                    <template #level="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.level }}</p>
                        </div>
                    </template>
                    <template #center="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.center }}</p>
                        </div>
                    </template>
                    <template #status="data">
                        <div class="flex justify-around w-full">
                            <div class="relative !p-1">
                                <button class="absolute left-0 top-0 z-10 h-full w-full" @click="registrantsStore.toggle({user_id:authStore?.user?.id,status: data.value.status ? 0 : 1},data.value.id)"></button>
                                <label class="!relative !inline-flex !cursor-pointer !items-center">
                                    <div
                                        :class="[data.value.status && '!bg-blue-600 after:!translate-x-full after:!border-white']"
                                        class="!peer !h-6 !w-11 !rounded-full bg-gray-200 after:!absolute after:!left-[2px] after:!top-[2px] after:!h-5 after:!w-5 after:!rounded-full after:!border after:!border-gray-300 after:!bg-white after:!transition-all after:!content-[''] peer-focus:!outline-none peer-focus:!ring-4 peer-focus:!ring-blue-300 dark:!border-gray-600 dark:!bg-gray-700 dark:peer-focus:!ring-blue-800"
                                    ></div>
                                </label>
                            </div>    
                        </div>
                    </template>
                    <template #user_id="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.user.firstName + ' ' + data.value.user.lastName }}</p>
                        </div>
                    </template>
                    <template #actions="data">
                        <div class="flex w-fit mx-auto justify-around gap-5">
                            <IconComponent name="edit" @click="() => toggleEdit(data.value)" />
                            <IconComponent name="transfer" @click="() => transfer(data.value)" />
                            <!-- <router-link :to="`/students/${data.value.student_id}/payments`" class="main-logo flex items-center shrink-0">
                                <IconComponent name="view" />
                            </router-link> -->
                            <IconComponent v-if="authStore?.user && authStore?.user?.roles[0]?.name == 'admin'" name="delete" @click="deleteData(data.value)" />
                        </div>
                    </template>
                </vue3-datatable>
            </div>
        </div>
    </div>
    <Edit :close="() => showEditPopup = false" :showEditPopup="showEditPopup" v-bind:editedData="editedData" v-if="showEditPopup"/>
    <Transfer :close="() => showTransferPopup = false" :showTransferPopup="showTransferPopup" v-bind:editedData="editedData" v-if="showTransferPopup"/>
    <Add :close="() => showPopup = false" :showPopup="showPopup" v-if="showPopup"/>
</template>
<script setup>
    import { ref, reactive, computed, onMounted } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import { useRegistrantsStore } from '@/stores/registrants.js';
    import IconComponent from '@/components/icons/IconComponent.vue'
    import Add from './Add.vue'
    import Edit from './Edit.vue'
    import Transfer from './Transfer.vue'
    import Swal from 'sweetalert2';
import {useAuthStore} from '@/stores/auth.js';

const authStore = useAuthStore();
    
    const params = reactive({
        current_page: 1,
        search: '',
        pagesize: 10,
        sort_column: 'id',
        sort_direction: 'desc',
    });

    const registrantsStore = useRegistrantsStore();

    const showPopup = ref(false);
    const showEditPopup = ref(false);
    const showTransferPopup = ref(false);
    
    const cols =
        ref([
            // { field: 'id', title: 'ID', isUnique: true, headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'student_id', title: "N° d'inscription", isUnique: true, headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'lastName', title: 'Nom', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'firstName', title: 'Prenom', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'group', title: "Groupe", headerClass: '!text-center flex justify-center', width: 'full' },
            // { field: 'email', title: 'Email', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'field', title: "spécialité", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'level', title: "Niveau", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'phone', title: "Mobile", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'parent_phone', title: "Mobile du parent", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'date', title: "Date d'incription", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'center', title: "Centre", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'status', title: "Etat", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'user_id', title: "Auteur", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'actions', title: 'Actions', headerClass: '!text-center flex justify-center', width: 'full' },
        ]) || [];

    const rows = computed(async() => {
        let data = await registrantsStore.registrants.length > 0 ? registrantsStore.registrants : []
        console.log(data);
        return data;
        });


    const editedData = ref({})
    onMounted(() => {
        registrantsStore.index()
    })

    const toggleEdit = (data) => {
        editedData.value = data
        showEditPopup.value = true
    }
    const transfer = (data) => {
        editedData.value = data
        showTransferPopup.value = true
    }

    const deleteData = (data) => {
        console.log(data);
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                popup: 'sweet-alerts',
                confirmButton: 'btn btn-secondary',
                cancelButton: 'btn btn-dark ltr:mr-3 rtl:ml-3',
            },
            buttonsStyling: false,
        });
        swalWithBootstrapButtons
        .fire({
            title: 'Es-tu sûr?',
            text: "Vous ne pourrez pas revenir en arrière!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Non, Annuler!',
            reverseButtons: true,
            padding: '2em',
        })
        .then((result) => {
            if (result.value) {
                registrantsStore.destroy(data.id).then(res => {
                    swalWithBootstrapButtons.fire('supprimé!', 'il a été supprimé.', 'success');
                    rows.value = res.data.data
                }).catch(err => {
                    swalWithBootstrapButtons.fire('supprimé!', "il a été supprimé.", 'success');
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire('Annulé', "aucune mesure n'a été prise:)", 'error');
            }
        });
    }

</script>
