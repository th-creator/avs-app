<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex justify-between items-end mb-4">    
                <input v-model="params.search" type="text" class="form-input max-w-xs h-10" placeholder="Rechercher..." />
                <!-- <button type="button" class="btn btn-info" @click="showPopup = true">Ajouter</button> -->
                <div class="flex flex-col gap-4">
                    <button type="button" class="btn btn-warning" @click="exportToExcel">Exporter</button>
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
            </div>
            <div class="datatable">
                <vue3-datatable
                    :rows="choosenData"
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
                    <template #month="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.month }}</p>
                        </div>
                    </template>
                    <template #amount="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.amount }}MAD</p>
                        </div>
                    </template>
                    <template #group="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.group }}</p>
                        </div>
                    </template>
                    <template #rest="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.rest }}MAD</p>
                        </div>
                    </template>
                    <template #reduction="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.reduction }}%</p>
                        </div>
                    </template>
                    <template #total="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.total }}MAD</p>
                        </div>
                    </template>
                    <template #amount_paid="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.amount_paid }}MAD</p>
                        </div>
                    </template>
                    <template #date="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.date }}</p>
                        </div>
                    </template>
                    <template #bank="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.bank }}</p>
                        </div>
                    </template>
                    <template #receipt="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.receipt }}</p>
                        </div>
                    </template>
                    <template #paid="data">
                        <div class="flex justify-center w-full">
                            <div v-if="data.value.reduction == 100">
                                <div class="px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 w-[120px] text-center text-sm">
                                    Gratuit
                                </div>
                            </div>
                            <div v-else-if="data.value.paid == 1 && data.value.rest == 0">
                                <div class="px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 w-[120px] text-center text-sm">
                                    Payé
                                </div>
                            </div>
                            <div v-else-if="data.value.paid == 1 && data.value.amount_paid > 0">
                                <div class="px-4 py-2 rounded-full bg-orange-100 text-orange-600 w-[120px] text-center text-sm">
                                    En cours
                                </div>
                            </div>
                            <div v-else-if="data.value.paid == -1">
                                <div class="px-4 py-2 rounded-full bg-blue-100 text-blue-600 w-[120px] text-center text-sm">
                                    Remboursé
                                </div>
                            </div>
                            <div v-else>
                                <div class="px-4 py-2 rounded-full bg-rose-100 text-rose-600 w-[120px] text-center text-sm">
                                    Non payé
                                </div>
                            </div>
                        </div>
                    </template>
                    <template #actions="data">
                        <div class="flex w-fit mx-auto justify-around gap-5">
                            <router-link v-if="data.value.student_id" :to="`/students/${data.value.student_id}/payments`" class="main-logo flex items-center shrink-0">
                                <IconComponent name="view" />
                            </router-link>
                        </div>
                    </template>
                </vue3-datatable>
            </div>
        </div>
    <div class="flex justify-end items-center my-4">
        <p class="font-semibold text-lg">Total: {{ total }} MAD</p>
    </div>
    </div>
</template>
<script setup>
    import { ref, reactive, computed, onMounted, watch } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import { usePaymentsStore } from '@/stores/payments.js';
    import { useRoute } from 'vue-router';
    import IconComponent from '@/components/icons/IconComponent.vue'
    import Multiselect from '@suadelabs/vue3-multiselect';
    import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';
    import * as XLSX from 'xlsx';
    import { useGroupsStore } from '@/stores/groups.js';

    const groupsStore = useGroupsStore();
    
    const options = ref(['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre', 'Octobre','Novembre','Décembre']);
    const choosenMonth = ref('');
    const years = ref([2024,2025,2026,2027,2028,2029,2030]);
    const choosenYear = ref(new Date().getFullYear());
    const choosenData = ref([]);
    const isloading = ref(true);
    const params = reactive({
        current_page: 1,
        search: '',
        pagesize: 10,
        sort_column: 'fullName',
        sort_direction: 'asc',
    });
    watch(choosenMonth, async (newVal, oldVal) => {
        isloading.value = true
        await paymentsStore.fetchGroupPayments(route.params.id,choosenMonth.value,choosenYear.value)
        choosenData.value = paymentsStore.groupPayments;
        total.value = paymentsStore.groupPayments.reduce((total, payment) => {
            if(payment.parent_id === null) {
            let amount = payment.total !== null ? payment.total : Number(payment.amount)*((100-Number(payment.reduction))/100)
            return total + Number(amount)
        } else return total

        }, 0)
        isloading.value = false
    });
    watch(choosenYear, async (newVal, oldVal) => {
        isloading.value = true
        await paymentsStore.fetchGroupPayments(route.params.id,choosenMonth.value,choosenYear.value)
        choosenData.value = paymentsStore.groupPayments;
        
        total.value = paymentsStore.groupPayments.reduce((total, payment) => {
            if(payment.parent_id === null) {
                let amount = payment.total !== null ? payment.total : Number(payment.amount)*((100-Number(payment.reduction))/100)
                return total + Number(amount)
            } else return total
        }, 0)
        isloading.value = false
    });
    
    const paymentsStore = usePaymentsStore();
    const route = useRoute();

    const total = ref();
    
    const cols =
        ref([
            // { field: 'id', title: 'ID', isUnique: true, headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'fullName', title: 'Nom', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'paid', title: 'Etat', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'amount', title: 'Montant', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'total', title: "montant a payer", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'amount_paid', title: "montant reçu", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'rest', title: "Reste", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'reduction', title: "Reduction", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'type', title: "Type", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'bank', title: "Bank", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'bank_receipt', title: "Chèque", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'receipt', title: "Recu", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'date', title: "Date", headerClass: '!text-center flex justify-center', width: 'full' },
            // { field: 'user_id', title: "Auteur", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'actions', title: 'Actions', headerClass: '!text-center flex justify-center', width: 'full' },
        ]) || [];
    const rows = computed(async() => {
        let data = await paymentsStore.groupPayments.length > 0 ? paymentsStore.groupPayments : []
        return data;
        });


    onMounted(async () => {
        const currentMonth = new Date().getMonth();
        // choosenYear.value = new Date().getFullYear();
        choosenMonth.value = options.value[currentMonth];
        // await paymentsStore.fetchGroupPayments(route.params.id,choosenMonth.value,choosenYear.value)
        isloading.value =false
        choosenData.value = paymentsStore.groupPayments;
        total.value = choosenData.value.reduce((total, payment) => {
            if(payment.parent_id === null) {
                let amount = payment.total !== null ? payment.total : Number(payment.amount)*((100-Number(payment.reduction))/100)
                return total + Number(amount)
            } else return total
        }, 0)
    })

    const exportToExcel = () => {
        // Get the attendance data from Vuex
        const attendanceData = choosenData.value.map(res => ({nom: res.fullName, mois: res.month, 'Reste à payer': res.rest ? res.rest : Number(res.amount)*((100-Number(res.reduction))/100), 'montant à payer': res.total ? res.total : Number(res.amount)*((100-Number(res.reduction))/100)}))

        // Calculate the total of all 'montant à payer'
        // const totalMontant = attendanceData.reduce((total, row) => total + row['montant à payer'], 0);

        // Add the total to the attendance data
        attendanceData.push({nom: '', mois: '', 'Reste à payer': '', 'montant à payer': total.value});

        console.log(attendanceData);

        // Create a worksheet from the attendance data
        const worksheet = XLSX.utils.json_to_sheet(attendanceData);

        // Create a new workbook and append the worksheet
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'paiements');

        // Export the workbook to an Excel file
        XLSX.writeFile(workbook, 'Paiements - '+groupsStore.group.intitule+' - '+choosenMonth.value+'.xlsx');
    };

</script>
