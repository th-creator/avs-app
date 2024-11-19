<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex justify-between items-end mb-4"> 
                <div class="flex flex-col gap-4">
                    <button type="button" class="btn btn-warning" @click="printPayment()">Exporter</button> 
                    <input v-model="params.search" type="text" class="form-input max-w-xs h-10" placeholder="Rechercher..." />

                </div>
                <div class="flex flex-col gap-4"> 
                    <button type="button" class="btn btn-info" @click="showPopup = true">Ajouter</button> 
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
                    :loading="isloading"
                    :sortColumn="params.sort_column"
                    :sortDirection="params.sort_direction"
                    :hasCheckbox="true"
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
                            <div v-else-if="(data.value.paid == 1 && data.value.total == data.value.amount_paid)">
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
                    <template #user_id="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value?.user?.firstName + ' ' + data.value?.user?.lastName }}</p>
                        </div>
                    </template>
                    <template #actions="data">
                        <div v-if="data.value.paid != null" class="flex w-fit mx-auto justify-around gap-5">
                            <IconComponent name="edit" @click="() => toggleEdit(data.value)" />
                            <!-- <IconComponent name="print" @click="printPayment(data.value)" /> -->
                            <IconComponent v-if="authStore?.user && authStore?.user?.roles[0]?.name == 'admin'" name="delete" @click="deleteData(data.value)" />
                        </div>
                        <p class="font-semibold text-xl text-center cursor-pointer" @click="() => toggleNew(data.value)" v-else>+</p>
                    </template>
                </vue3-datatable>
            </div>
        </div>
    </div>
    <AddNew :close="() => showNewPopup = false" :showNewPopup="showNewPopup" v-bind:newData="newData" v-if="showNewPopup"/>
    <Edit :close="() => showEditPopup = false" :showEditPopup="showEditPopup" v-bind:editedData="editedData" v-if="showEditPopup"/>
    <Add :close="() => showPopup = false" :showPopup="showPopup" v-if="showPopup"/>
    <!-- Hidden element to use for printing -->
    <div id="receipt" class="receipt-container hidden">
        <div class="reciept-wrapper">
            <div class="flex justify-between p-">
                <img src="/assets/images/avs-logo.png" alt="Image description" class="w-1/4">
                <img src="/assets/images/isfpt-logo.jpeg" alt="Image description" class="w-2/5">
            </div>
            <p class="text-center text-3xl font-semibold" style="font-family: 'Courier New', monospace;">RENFORCEMENT ET SOUTIEN SCOLAIRE</p>
            <br>
            <!-- <p class="text-center">-------------------------------------------------------------------------------------</p> -->
             <hr >
            <br>
            <div class="flex justify-between">
                <p><strong>Nom :</strong> {{ selectedPayment?.fullName }} / <strong>Mois :</strong> {{ selectedPayment?.month }} </p><p> <strong>Date :</strong> {{ new Date().toLocaleDateString() }}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Groupe</th>
                        <th>Montant à payer</th>
                        <th>Montant reçu</th>
                        <th>Reste à payer</th>
                        <th>Reçu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(payment, index) in selectedPayment.payments" :key="index">
                        <td>{{ payment.group }}</td>
                        <td>{{ payment.total }}</td>
                        <td>{{ payment.amount_paid }}</td>
                        <td>{{ payment.rest }}</td>
                        <td>{{ payment.receipt }}</td>
                    </tr>
                </tbody>
            </table>    
            <div>
                <p class="text-center">Centre AVS MA, Avenue Allal Al Fassi - à côté de la boulangerie Alpha 2000 - Marrakech</p>
                <p class="text-center p-">Centre AVS2 MA, 5ème étage, siège du parti ISTIQLAL, AV AL MOZDALIFA - Marrakech</p>
                <p class="text-center p-">Tel: 0524311982 / 0661843332 / 0662265561</p>
            </div>
        </div>
        <!-- <br> -->
        <hr>
        <!-- <br> -->
        <div class="reciept-wrapper">
            <div class="flex justify-between p-">
                <img src="/assets/images/avs-logo.png" alt="Image description" class="w-1/4">
                <img src="/assets/images/isfpt-logo.jpeg" alt="Image description" class="w-2/5">
            </div>
            <p class="text-center text-3xl font-semibold" style="font-family: 'Courier New', monospace;">RENFORCEMENT ET SOUTIEN SCOLAIRE</p>
            <br>
            <!-- <p class="text-center">-------------------------------------------------------------------------------------</p> -->
            <hr >
            <br>
            <div class="flex justify-between">
                <p><strong>Nom :</strong> {{ selectedPayment?.fullName }} / <strong>Mois :</strong> {{ selectedPayment?.month }} </p><p> <strong>Date :</strong> {{ new Date().toLocaleDateString() }}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Groupe</th>
                        <th>Montant à payer</th>
                        <th>Montant reçu</th>
                        <th>Reste à payer</th>
                        <th>Reçu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(payment, index) in selectedPayment.payments" :key="index">
                        <td>{{ payment.group }}</td>
                        <td>{{ payment.total }}</td>
                        <td>{{ payment.amount_paid }}</td>
                        <td>{{ payment.rest }}</td>
                        <td>{{ payment.receipt }}</td>
                    </tr>
                </tbody>
            </table>    
            <div>
                <p class="text-center">Centre AVS MA, Avenue Allal Al Fassi - à côté de la boulangerie Alpha 2000 - Marrakech</p>
                <p class="text-center p-">Centre AVS2 MA, 5ème étage, siège du parti ISTIQLAL, AV AL MOZDALIFA - Marrakech</p>
                <p class="text-center p-">Tel: 0524311982 / 0661843332 / 0667635797</p>
            </div>
        </div>
    </div>
</template>
<script setup>
    import { ref, reactive, computed, onMounted, nextTick, watch } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import { usePaymentsStore } from '@/stores/payments.js';
    import { useRoute } from 'vue-router';
    import IconComponent from '@/components/icons/IconComponent.vue'
    import Add from './Add.vue'
    import Edit from './Edit.vue'
    import AddNew from './AddNew.vue'
    import Swal from 'sweetalert2';
    import html2pdf from "html2pdf.js";
    import {useAuthStore} from '@/stores/auth.js';
    import Multiselect from '@suadelabs/vue3-multiselect';
    import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

    const authStore = useAuthStore();
    

    const options = ref(['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre', 'Octobre','Novembre','Décembre']);
    const choosenMonth = ref('Septembre');
    const years = ref([2024,2025,2026,2027,2028,2029,2030]);
    const choosenYear = ref(2024);
    const choosenData = ref([]);
    const isloading = ref(true);

    const datatable = ref([]);

    const params = reactive({
        current_page: 1,
        search: '',
        pagesize: 10,
        sort_column: 'id',
        sort_direction: 'desc',
    });

    const paymentsStore = usePaymentsStore();
    const route = useRoute();


    const showPopup = ref(false);
    const showEditPopup = ref(false);
    const showNewPopup = ref(false);
    
    const cols =
        ref([
            // { field: 'id', title: 'ID', isUnique: true, headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'group', title: 'Groupe', headerClass: '!text-center flex justify-center', width: 'full' },
            // { field: 'month', title: 'Mois', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'paid', title: 'Etat', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'amount', title: 'Montant', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'total', title: "montant à payer", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'amount_paid', title: "montant reçu", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'rest', title: "Reste", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'reduction', title: "Réduction", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'type', title: "Type", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'bank', title: "Bank", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'bank_receipt', title: "Chèque", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'receipt', title: "Recu", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'date', title: "Date", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'user_id', title: "Auteur", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'actions', title: 'Actions', headerClass: '!text-center flex justify-center', width: 'full' },
        ]) || [];
    const rows = computed(async() => {
        let data = await paymentsStore.studentPayments.length > 0 ? paymentsStore.studentPayments : []
        
        return data;
        });

    watch(rows, (newVal, oldVal) => {
        console.log(`Month changed from ${oldVal} to ${newVal}`);
        choosenData.value = paymentsStore.studentPayments;
    });

    const editedData = ref({})
    const newData = ref({})
    onMounted(async () => {
        const currentMonth = new Date().getMonth();
        choosenYear.value = new Date().getFullYear();
        choosenMonth.value = options.value[currentMonth];
        await paymentsStore.show(route.params.id,choosenMonth.value,choosenYear.value)
        choosenData.value = paymentsStore.studentPayments;
        isloading.value =false
    })
    watch(choosenMonth, async (newVal, oldVal) => {
        isloading.value = true
        await paymentsStore.show(route.params.id,choosenMonth.value,choosenYear.value)
        choosenData.value = paymentsStore.studentPayments;
        isloading.value = false
    });
    watch(choosenYear, async (newVal, oldVal) => {
        isloading.value = true
        await paymentsStore.show(route.params.id,choosenMonth.value,choosenYear.value)
        choosenData.value = paymentsStore.studentPayments;
        isloading.value = false
    });

    const toggleEdit = (data) => {
        editedData.value = data
        console.log(editedData.value);
        showEditPopup.value = true
    }
    const toggleNew = (data) => {
        newData.value = data
        console.log(editedData.value);
        showNewPopup.value = true
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
    const selectedPayment = ref({
        month:'',
        name:'',
        firstName:'',
        date:'',
        receipt:'',
        total:'',
        rest:'',
        amount:'',
        payments:[],
    });
// Print function using html2pdf.js
const printPayment = async (payment) => {
    const selected = datatable.value.getSelectedRows();
    if(selected.length == 0) {
        return
    }
    selectedPayment.value = selected[0];
    selectedPayment.value.payments = await selected.map((item) => ({
        group:item.group,
        total:item.total,
        amount_paid:item.amount_paid,
        rest:item.rest,
        receipt:item.receipt,
    }));
    
  // Temporarily remove the hidden class to display the receipt
  const element = document.getElementById('receipt');
  element.classList.remove('hidden');

  // Wait for the DOM to update
  nextTick(() => {
    const options = {
      margin: 1,
      filename: `Facture-${selectedPayment.value.fullName.replace(/\s/g, '-')}-${selectedPayment.value.month}.pdf`,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
    };

    html2pdf().from(element).set(options).save().then(() => {
      // Add the hidden class again after printing
      element.classList.add('hidden');
    });
  });
};
</script>


<style scoped>
.hidden {
  display: none;
}

.receipt-container {
  padding: 2.5px 1px;
}
.reciept-wrapper {
padding: 7.5px 20px;
  border: 1px solid #000;
  /* width: 700px; */
  margin: 17.5px 20px;

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