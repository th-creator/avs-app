<template>
    <div>
        <div class="panel pb-0 mt-6">
            <h5 class="font-semibold text-lg dark:text-white-light mb-5">Les Employés</h5>
            <div class="flex justify-between mb-4">    
                <input v-model="params.search" type="text" class="form-input max-w-xs" placeholder="Rechercher..." />
                <button type="button" class="btn btn-info" @click="showPopup = true">Ajouter</button>
            </div>
            <div class="datatable">
                <vue3-datatable
                    :rows="usersStore.users"
                    :columns="cols"
                    :totalRows="rows?.length"
                    :sortable="true"
                    :search="params.search"
                    :loading="isloading"
                    :sortColumn="params.sort_column"
                    :sortDirection="params.sort_direction"
                    :paginationInfo="'{0} à {1} de {2}'"
                    skin="whitespace-nowrap bh-table-hover"
                    firstArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    lastArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg> '
                    previousArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    nextArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                >
                    <template #id="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <strong class="text-info">#{{ data.value.id }}</strong>
                        </div>
                    </template>
                    <template #name="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.firstName + ' ' + data.value.lastName }}</p>
                        </div>
                    </template>
                    <template #center="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.center }}</p>
                        </div>
                    </template>
                    <template #email="data">
                        <div class="flex justify-around w-full">
                            <a :href="`mailto:${data.value.email}`" class="text-primary hover:underline">{{ data.value.email }}</a>
                        </div>
                    </template>
                    <template #status="data">
                        <div class="flex justify-around w-full">
                            <div class="relative !p-1">
                                <button class="absolute left-0 top-0 z-10 h-full w-full" @click="usersStore.toggle({status: data.value.status ? 0 : 1},data.value.id)"></button>
                                <label class="!relative !inline-flex !cursor-pointer !items-center">
                                    <div
                                        :class="[data.value.status && '!bg-blue-600 after:!translate-x-full after:!border-white']"
                                        class="!peer !h-6 !w-11 !rounded-full bg-gray-200 after:!absolute after:!left-[2px] after:!top-[2px] after:!h-5 after:!w-5 after:!rounded-full after:!border after:!border-gray-300 after:!bg-white after:!transition-all after:!content-[''] peer-focus:!outline-none peer-focus:!ring-4 peer-focus:!ring-blue-300 dark:!border-gray-600 dark:!bg-gray-700 dark:peer-focus:!ring-blue-800"
                                    ></div>
                                </label>
                            </div>    
                        </div>
                    </template>
                    <template #actions="data">
                        <div class="flex w-fit mx-auto justify-around gap-5">
                            <IconComponent name="edit" @click="() => toggleEdit(data.value)" />
                            <IconComponent name="delete" @click="deleteData(data.value)" />
                        </div>
                    </template>
                </vue3-datatable>
            </div>
        </div>
    </div>
    <Edit :close="() => showEditPopup = false" :showEditPopup="showEditPopup" v-bind:editedData="editedData" v-if="showEditPopup"/>
    <Add :close="() => showPopup = false" :showPopup="showPopup" v-if="showPopup"/>
</template>
<script setup>
    import { ref, reactive, computed, onMounted } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import { useUsersStore } from '@/stores/users.js';
    import IconComponent from '@/components/icons/IconComponent.vue'
    import Add from './Add.vue'
    import Edit from './Edit.vue'
    import Swal from 'sweetalert2';
    
    const params = reactive({
        current_page: 1,
        search: '',
        pagesize: 10,
        sort_column: 'id',
        sort_direction: 'asc',
    });
    const isloading = ref(false);

    const usersStore = useUsersStore();

    const showPopup = ref(false);
    const showEditPopup = ref(false);
    
    const cols =
        ref([
            { field: 'id', title: 'ID', isUnique: true, headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'name', title: 'Name', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'email', title: 'Email', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'center', title: 'Centre', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'status', title: 'Etat', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'actions', title: 'Actions', headerClass: '!text-center flex justify-center', width: 'full' },
        ]) || [];

    const rows = computed(() => {
        console.log('usersStore.users',usersStore.users);
        return usersStore.users.length > 0 ? usersStore.users : [];
        });


    const editedData = ref({})
    onMounted(async () => {
        usersStore.users.length == 0 && (isloading.value = true)
        await usersStore.index()
        isloading.value = false
    })

    const toggleEdit = (data) => {
        editedData.value = data
        console.log(editedData.value);
        showEditPopup.value = true
    }

    const deleteData = (data) => {
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
                usersStore.destroy(data.id).then(res => {
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
