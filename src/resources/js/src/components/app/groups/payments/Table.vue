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

        <!-- Gratuit -->
        <div v-if="data.value.reduction == 100 && data.value.paid != -1">
            <div class="px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 w-[120px] text-center text-sm">
                Gratuit
            </div>
        </div>

        <!-- Remboursé -->
        <div v-else-if="data.value.paid == -1">
            <div class="px-4 py-2 rounded-full bg-blue-100 text-blue-600 w-[120px] text-center text-sm">
                Remboursé
            </div>
        </div>

        <!-- Payé (FULL) -->
        <div v-else-if="Number(data.value.amount_paid) >= Number(data.value.total)">
            <div class="px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 w-[120px] text-center text-sm">
                Payé
            </div>
        </div>

        <!-- En cours (PARTIAL BUT PAID FLAG = 1) -->
        <div v-else-if="data.value.paid == 1">
            <div class="px-4 py-2 rounded-full bg-orange-100 text-orange-600 w-[120px] text-center text-sm">
                En cours
            </div>
        </div>

        <!-- Non payé -->
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

const options = ref([
    'Janvier','Février','Mars','Avril','Mai','Juin',
    'Juillet','Août','Septembre', 'Octobre','Novembre','Décembre'
]);

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

const paymentsStore = usePaymentsStore();
const route = useRoute();

const total = ref(0);

/* --------------------------------------------------------
   🔥 MERGE PARENT FACTURE + SUB-FACTURES
---------------------------------------------------------*/
const roundMoney = (value, decimals = 2) =>
  Math.round((Number(value) + Number.EPSILON) * 10 ** decimals) / 10 ** decimals;

const mergeFactures = (payments) => {
  const parents = {};
  const parentsById = {};   // ✅ fast + reliable child merge
  const children = [];

  // 1️⃣ Split + index parents
  payments.forEach((p) => {
    if (p.parent_id === null) {
      const parentKey = p.id ?? `reg-${p.registrant_id}`;

      const parentObj = {
        ...p,
        merged_amount_paid: Number(p.amount_paid),
      };

      parents[parentKey] = parentObj;

      // ✅ Only DB parents have an id that children reference
      if (p.id != null) {
        parentsById[p.id] = parentObj;
      }
    } else {
      children.push(p);
    }
  });

  // 2️⃣ Merge children into their DB parent
  children.forEach((c) => {
    const parent = parentsById[c.parent_id];
    if (!parent) return;
    parent.merged_amount_paid += Number(c.amount_paid);
  });

  // 3️⃣ Finalize
  return Object.values(parents).map((p) => {
    const total =
      p.total !== null && p.total !== undefined
        ? Number(p.total)
        : Number(p.amount) * ((100 - Number(p.reduction)) / 100);

    const roundedTotal = roundMoney(total, 2);
    const mergedPaid = roundMoney(p.merged_amount_paid, 2);

    // ✅ Clamp tiny rounding noise
    let rest = roundMoney(roundedTotal - mergedPaid, 2);
    if (Math.abs(rest) < 0.01) rest = 0;
    if (rest < 0) rest = 0; // avoid negative rest when overpaid

    // ✅ Your rule: paid=1 as soon as any payment exists, keep refunded as -1
    const paid =
      Number(p.paid) === -1 ? -1 : mergedPaid > 0 ? 1 : 0;

    return {
      ...p,
      amount_paid: mergedPaid,
      rest,
      paid,
    };
  });
};


/* --------------------------------------------------------
   🔄 WATCHERS — Load data & merge invoices
---------------------------------------------------------*/
watch(choosenMonth, async () => {
    isloading.value = true;
    await paymentsStore.fetchGroupPayments(route.params.id, choosenMonth.value, choosenYear.value);

    choosenData.value = mergeFactures(paymentsStore.groupPayments);

    total.value = choosenData.value.reduce((sum, p) => sum + Number(p.total), 0);

    isloading.value = false;
});

watch(choosenYear, async () => {
    isloading.value = true;
    await paymentsStore.fetchGroupPayments(route.params.id, choosenMonth.value, choosenYear.value);

    choosenData.value = mergeFactures(paymentsStore.groupPayments);

    total.value = choosenData.value.reduce((sum, p) => sum + Number(p.total), 0);

    isloading.value = false;
});

/* --------------------------------------------------------
   🟢 onMounted — Initial load
---------------------------------------------------------*/
onMounted(async () => {
    const currentMonth = new Date().getMonth();
    choosenMonth.value = options.value[currentMonth];

    await paymentsStore.fetchGroupPayments(route.params.id, choosenMonth.value, choosenYear.value);

    choosenData.value = mergeFactures(paymentsStore.groupPayments);

    total.value = choosenData.value.reduce((sum, p) => sum + Number(p.amount_paid), 0);

    isloading.value = false;
});

/* --------------------------------------------------------
   📤 EXPORT EXCEL (works with merged data)
---------------------------------------------------------*/
const exportToExcel = () => {
    const attendanceData = choosenData.value.map(res => ({
        Reçu: res.receipt,
        Nom: res.fullName,
        'Montant à payer': res.total,
        'Reduction': res.reduction,
        'montant reçu': res.amount_paid,
        'Reste à payer': res.rest
    }));

    const worksheet = XLSX.utils.json_to_sheet(attendanceData);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'paiements');

    XLSX.writeFile(
        workbook,
        `Paiements - ${groupsStore.group.intitule} - ${choosenMonth.value}.xlsx`
    );
};

/* --------------------------------------------------------
   Columns for datatable
---------------------------------------------------------*/
const cols = ref([
    { field: 'fullName', title: 'Nom', headerClass: '!text-center flex justify-center' },
    { field: 'paid', title: 'Etat', headerClass: '!text-center flex justify-center' },
    { field: 'amount', title: 'Montant', headerClass: '!text-center flex justify-center' },
    { field: 'total', title: "Montant à payer", headerClass: '!text-center flex justify-center' },
    { field: 'amount_paid', title: "Montant reçu", headerClass: '!text-center flex justify-center' },
    { field: 'rest', title: "Reste", headerClass: '!text-center flex justify-center' },
    { field: 'reduction', title: "Reduction", headerClass: '!text-center flex justify-center' },
    { field: 'type', title: "Type", headerClass: '!text-center flex justify-center' },
    { field: 'bank', title: "Bank", headerClass: '!text-center flex justify-center' },
    { field: 'bank_receipt', title: "Chèque", headerClass: '!text-center flex justify-center' },
    { field: 'receipt', title: "Reçu", headerClass: '!text-center flex justify-center' },
    { field: 'date', title: "Date", headerClass: '!text-center flex justify-center' },
    { field: 'actions', title: 'Actions', headerClass: '!text-center flex justify-center' }
]);

</script>
