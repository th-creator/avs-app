<template>
    <div>
    <div class="flex justify-end my-4 items-end"> 
        <div class="flex flex-col gap-3 justify-between my-4 items-center">
            <button type="button" class="btn btn-success w-40 h-9" @click="exportToExcel()">Excel</button>   
            <button type="button" class="btn btn-warning w-40 h-9" @click="exportToPDF()">PDF</button>
        </div>
    </div>
        <div class="flex gap-3 justify-between my-4 items-center"> 
                <div class="flex gap-10 items-end">
                    <label for="">Du:</label>
                    <input v-model="search.from" type="date" class="form-input max-w-xs" placeholder="Du..." />
                    <label for="">A:</label>
                    <input v-model="search.to" type="date" class="form-input max-w-xs" placeholder="A..." />
                </div>
            <button type="button" class="btn btn-info w-40 h-9" @click="searchPayments">
                <IconComponent v-if="isloading" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" name="loading" />
                <span v-else>Rechercher</span>
            </button>
        </div>
        <div class="panel pb-0 mt-6">
            
            <div class="flex justify-between items-end mb-4"> 
                <input v-model="params.search" type="text" class="form-input max-w-xs h-10" placeholder="Rechercher..." />
                <div>
                    <multiselect
                        v-model="choosenVal"
                        :options="options"
                        class="custom-multiselect"
                        :searchable="true"
                        placeholder="Type"
                        selected-label=""
                        select-label=""
                        deselect-label=""
                    ></multiselect> 
                </div>
                <!-- <button type="button" class="btn btn-info" @click="showPopup = true">Ajouter</button> -->
            </div>
            <div class="datatable">
                <vue3-datatable
                    :rows="choosenData"
                    :columns="cols"
                    :totalRows="rows?.length"
                    :sortable="true"
                    :loading="isloading"
                    :sortColumn="params.sort_column"
                    :sortDirection="params.sort_direction"
                    :hasCheckbox="false"
                    :search="params.search"
                     ref="datatable"
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
                    <template #fullName="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.fullName }}</p>
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
                    <template #reduction="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.reduction }}%</p>
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
                    <template #bank_receipt="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.bank_receipt }}</p>
                        </div>
                    </template>
                    <template #receipt="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.receipt }}</p>
                        </div>
                    </template>
                    <template #paid="data">
                        <div class="flex justify-center w-full">
                            <div v-if="data.value.reduction == 100 && data.value.paid != -1">
                                <div class="px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 w-[120px] text-center text-sm">
                                    Gratuit
                                </div>
                            </div>
                            <div v-else-if="(data.value.paid == 1 && data.value.rest == 0)">
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
                            <div v-else-if="data.value.paid == -2">
                                <div class="px-4 py-2 rounded-full bg-blue-300 text-blue-800 w-[120px] text-center text-sm">
                                    Stationaire
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
                            <IconComponent v-if="authStore?.user && authStore?.user?.roles[0]?.name == 'admin'" name="delete" @click="deleteData(data.value)" />
                        </div>
                    </template>
                </vue3-datatable>
            </div>
        </div>
    <div class="flex justify-end items-center my-4">
        <p class="font-semibold text-lg">Total: {{ total }} MAD</p>
        <!-- <p class="font-semibold text-lg">revenu: {{ choosenData.reduce((total, payment) => total + Number(payment.amount)*((100-Number(payment.reduction))/100), 0) }} MAD</p> -->
    </div>
    </div>
</template>
<script setup>
    import { ref, reactive, computed, watch } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import { usePaymentsStore } from '@/stores/payments.js';
    import IconComponent from '@/components/icons/IconComponent.vue'
    import Swal from 'sweetalert2';
    import {useAuthStore} from '@/stores/auth.js';
    import Multiselect from '@suadelabs/vue3-multiselect';
    import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';
    import jsPDF from "jspdf";
    import autoTable from "jspdf-autotable";
    import * as XLSX from 'xlsx';

    const authStore = useAuthStore();
    

    const options = ref(['tous', 'espèces', 'chèque', 'virement', 'remboursé']);
    const choosenVal = ref('tous');
    const choosenData = ref([]);
    const isloading = ref(false);

    const datatable = ref([]);
    const total = ref();

    const params = reactive({
        current_page: 1,
        search: '',
        pagesize: 10,
        sort_column: 'id',
        sort_direction: 'desc',
    });

    watch(choosenVal, (newVal, oldVal) => {
        choosenData.value = paymentsStore.financePayments.filter(payment => {if(choosenVal.value == 'tous' && payment.paid != -2 && payment.paid != -1) {return payment} if(choosenVal.value == 'remboursé' && payment.paid == -1) {return payment} if (payment.type == choosenVal.value && payment.paid != -2 && payment.paid != -1) return payment});
        total.value = choosenData.value.reduce((total, payment) => {
            // let amount = payment.total !== null ? payment.total : Number(payment.amount)*((100-Number(payment.reduction))/100)
            return total + Number(payment.amount_paid)
        }, 0)
        
    });

    const paymentsStore = usePaymentsStore();

    
    const search = ref({
        from: '',
        to: ''
    })

    const cols =
        ref([
            // { field: 'id', title: 'ID', isUnique: true, headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'fullName', title: 'Nom', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'paid', title: 'Etat', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'month', title: 'Mois', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'amount', title: 'Montant', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'total', title: "Montant à payer", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'amount_paid', title: "Montant reçu", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'rest', title: "Reste", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'reduction', title: "Réduction", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'type', title: "Type", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'bank', title: "Bank", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'bank_receipt', title: "Chèque", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'receipt', title: "Recu", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'date', title: "Date", headerClass: '!text-center flex justify-center', width: 'full' },
            // { field: 'user_id', title: "Auteur", headerClass: '!text-center flex justify-center', width: 'full' },
            // { field: 'actions', title: 'Actions', headerClass: '!text-center flex justify-center', width: 'full' },
        ]) || [];
    const rows = computed(async() => {
        let data = await paymentsStore.financePayments.length > 0 ? paymentsStore.financePayments : []
        
        return data;
        });

    const searchPayments = async () => {
        isloading.value =true
        await paymentsStore.fetchFacturation(search.value)
        isloading.value =false
        choosenData.value = paymentsStore.financePayments.filter(payment => {if(choosenVal.value == 'tous' && payment.paid != -2 && payment.paid != -1) {return payment} if(choosenVal.value == 'remboursé' && payment.paid == -1) {return payment} if (payment.type == choosenVal.value && payment.paid != -2 && payment.paid != -1) return payment});
        total.value = choosenData.value.reduce((total, payment) => {
            // let amount = payment.total !== null ? payment.total : Number(payment.amount)*((100-Number(payment.reduction))/100)
            return total + Number(payment.amount_paid)
        }, 0)
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
                paymentsStore.destroy(data.id).then(res => {
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
    const getTodayDate = () => {
    const d = new Date();
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const day = String(d.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
};
const exportToPDF = () => {
    const doc = new jsPDF('landscape'); // landscape like your screenshot

    // Table columns like your Excel/PDF screenshot
    const columns = [
        { header: "N° Reçu", dataKey: "receipt" },
        { header: "Nom Prénom", dataKey: "fullName" },
        { header: "Groupe", dataKey: "group" },   // or subject if you have it
        { header: "Montant", dataKey: "amount_paid" },
        { header: "Reste", dataKey: "rest" },
        { header: "Chèque/Espèce", dataKey: "type" },
    ];

    // Data for the table
    const rows = choosenData.value.map(p => ({
        receipt: p.receipt,
        fullName: p.fullName,
        group: p.group,
        bank: p.bank,
        amount_paid: p.amount_paid + " MAD",
        rest: p.rest + " MAD",
        type: p.type,
    }));

    doc.setFontSize(14);
    doc.text("Liste des Paiements", 14, 15);

    autoTable(doc, {
        columns,
        body: rows,
        startY: 25,
        styles: {
            fontSize: 10,
            cellPadding: 3,
            lineColor: [0, 0, 0],
            lineWidth: 0.25,
        },
        headStyles: {
            fillColor: [230, 230, 230], // light grey like screenshot
            textColor: 0,
            halign: "center",
        },
        bodyStyles: {
            halign: "center"
        },
        tableWidth: "auto"
    });

    doc.save(`paiements-${getTodayDate()}.pdf`);
};

    const exportToExcel = () => {
        // Get the attendance data from Vuex
        const attendanceData = paymentsStore.financePayments.map(res => ({'Recu': res.receipt, Nom: res.fullName,'Groupe': res.group, 'Montant à payer': res.total, 'Montant reçu': res.amount_paid, 'Reste à payer': res.rest, 'Réduction': res.reduction}))
        // Create a worksheet from the attendance data
        const worksheet = XLSX.utils.json_to_sheet(attendanceData);

        // Create a new workbook and append the worksheet
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'groupes');

        // Export the workbook to an Excel file
        XLSX.writeFile(workbook, `paiements-${getTodayDate()}.xlsx`);
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