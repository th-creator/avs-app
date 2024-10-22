<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex flex-col gap-4">
                <button type="button" class="btn btn-warning w-40" @click="exportToExcel()">Exporter</button> 

            </div>
            <!-- <h5 class="font-semibold text-lg dark:text-white-light mb-5">Les Payements et Inscriptions</h5> -->
            <div class="flex justify-between my-4">    
                <input v-model="params.search" type="text" class="form-input max-w-xs" placeholder="Rechercher..." />
                
                <div class="flex gap-2">
                    <multiselect
                        v-model="choosenMonth"
                        :options="options"
                        class="custom-multiselect  max-w-xs"
                        :searchable="true"
                        placeholder="Le mois"
                        selected-label=""
                        select-label=""
                        deselect-label=""
                    ></multiselect>    
                    <multiselect
                        v-model="choosenYear"
                        :options="years"
                        class="custom-multiselect  max-w-xs"
                        :searchable="true"
                        placeholder="L'année"
                        selected-label=""
                        select-label=""
                        deselect-label=""
                    ></multiselect>    
                </div>
            </div>
            <div class="datatable">
                <vue3-datatable
                    :rows="groupsStore.allPayments"
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
                    <template #amount="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.amount }}</p>
                        </div>
                    </template>
                    <template #intitule="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.intitule }}</p>
                        </div>
                    </template>
                    <template #total="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.total }} MAD</p>
                        </div>
                    </template>
                    <template #teacher="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.teacher }}</p>
                        </div>
                    </template>
                    <template #actions="data">
                        <div class="flex w-fit mx-auto justify-around gap-5">
                            <router-link :to="`/groups/${data.value.id}/registrants`" class="main-logo flex items-center shrink-0">
                                <IconComponent name="view" />
                            </router-link>
                        </div>
                    </template>
                </vue3-datatable>
            </div>
        </div>
    </div>
</template>
<script setup>
    import { ref, reactive, computed, onMounted, watch } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import IconComponent from '@/components/icons/IconComponent.vue'
    import { useGroupsStore } from '@/stores/groups.js';
    import Multiselect from '@suadelabs/vue3-multiselect';
    import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';
    import * as XLSX from 'xlsx';

    const isloading = ref(true);
    const groupsStore = useGroupsStore();

    const options = ref(['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre', 'Octobre','Novembre','Décembre']);
    const choosenMonth = ref('Septembre');
    const years = ref([2024,2025,2026,2027,2028,2029,2030]);
    const choosenYear = ref(2024);
    
    const params = reactive({
        current_page: 1,
        search: '',
        pagesize: 10,
        sort_column: 'id',
        sort_direction: 'desc',
    });
    
    watch(choosenMonth, async (newVal, oldVal) => { 
        isloading.value = true
        await groupsStore.fetchPayments(choosenMonth.value,choosenYear.value )
        isloading.value = false
    });
    onMounted(async () => {
        const currentMonth = new Date().getMonth();
        choosenMonth.value = options.value[currentMonth];
        choosenYear.value = new Date().getFullYear();
        // await groupsStore.fetchPayments(choosenMonth.value)
        // isloading.value = false
    })
    const cols =
        ref([
            // { field: 'id', title: 'ID', isUnique: true, headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'teacher', title: "Enseignant", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'intitule', title: 'Groupe', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'total', title: 'Montant', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'actions', title: 'Actions', headerClass: '!text-center flex justify-center', width: 'full' },
        ]) || [];

    const rows = computed(async() => {
        let data = await groupsStore.allPayments.length > 0 ? groupsStore.allPayments : []
        return data;
        });
        
    const exportToExcel = () => {
        // Get the attendance data from Vuex
        const attendanceData = groupsStore.allPayments.map(res => ({Enseignant: res.teacher, Groupe: res.intitule, Montant: res.total}))
        // Create a worksheet from the attendance data
        const worksheet = XLSX.utils.json_to_sheet(attendanceData);

        // Create a new workbook and append the worksheet
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'groupes');

        // Export the workbook to an Excel file
        XLSX.writeFile(workbook, 'paiements_groupes_'+choosenMonth.value+'.xlsx');
    };
</script>

<style scoped>
.hidden {
  display: none;
}

.receipt-container {
  padding: 5px;
}
.reciept-wrapper {
padding: 10px 20px;
  border: 1px solid #000;
  /* width: 700px; */
  margin: 30px 20px;

}
.receipt-container table {
  width: 100%;
  border-collapse: collapse;
  margin: 15px 0;
}

.receipt-container table th,
.receipt-container table td {
  border: 1px solid #000;
  padding: 10px;
  text-align: left;
}

.receipt-container table th {
  background-color: #f2f2f2;
}

</style>