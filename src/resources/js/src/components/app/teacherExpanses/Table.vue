<template>
    <div>
        <div class="panel pb-0 mt-6">
            <h5 class="font-semibold text-lg dark:text-white-light mb-5">Rémunération</h5>
            <div class="flex justify-between my-4">    
                <div class="flex flex-col gap-2">
                    <button type="button" class="btn btn-warning w-40 h-9" @click="exportToExcel()">Exporter</button>   
                    <button type="button" class="btn btn-success w-40 h-9" @click="() => exportAllGroupsPayments()">Détails groupes</button>   
                </div>
                <button type="button" class="btn btn-info w-40 h-9" @click="showPopup = true">Ajouter</button>
            </div>
            <div class="flex justify-between my-4">    
                <input v-model="params.search" type="text" class="form-input max-w-xs" placeholder="Rechercher..." />
                
                <div class="flex gap-2">
                    <multiselect
                        v-model="selectedStatus"
                        :options="statusOptions"
                        class="custom-multiselect max-w-xs"
                        placeholder="Statut"
                        :searchable="false"
                        selected-label=""
                        select-label=""
                        deselect-label=""
                        ></multiselect>
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
                    :rows="rows"
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
                    <template #total="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ roundMoney(data.value.total, 2) }}MAD</p>
                        </div>
                    </template>
                    <template #amount="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ roundMoney(data.value.amount, 2) }}MAD</p>
                        </div>
                    </template>
                    <template #rest="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ roundMoney(data.value.rest, 2) }}MAD</p>
                        </div>
                    </template>
                    <template #status="data">
                        <div class="flex justify-center">
                            <span
                                v-if="data.value.status === 'Payé'"
                                class="badge badge-outline-success"
                            >
                                Payé
                            </span>
                            <span
                                v-else
                                class="badge badge-outline-warning"
                            >
                                En attente
                            </span>
                        </div>
                    </template>
                    <template #teacher="data"> 
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.teacher }}</p>
                        </div>
                    </template>
                    <template #date="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.date }}</p>
                        </div>
                    </template>
                    <template #group="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.group }}</p>
                        </div>
                    </template>
                    <template #month="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.month }}</p>
                        </div>
                    </template>
                    <template #missing_students="data">
                        <div class="flex justify-center">
                            <span
                            v-if="data.value.missing_students > 0"
                            class="text-red-600 font-semibold"
                            >
                            {{ data.value.missing_students }} MAD
                            </span>
                            <span v-else class="text-emerald-600">
                            0 MAD
                            </span>
                        </div>
                    </template>
                    <template #user_id="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.user.firstName + ' ' + data.value.user.lastName }}</p>
                        </div>
                    </template>
                    <template #actions="data">
                        <div class="flex w-fit mx-auto justify-around gap-5">
                            <span class="text-xl cursor-pointer" @click="() => openFollowUp(data.value)" v-if="data.value.status == 'En attente'">+</span>
                            <IconComponent name="edit" @click="() => toggleEdit(data.value)" v-if="data.value.status == 'Payé'" />
                            <IconComponent v-if="authStore?.user && authStore?.user?.roles[0]?.name == 'admin' && data.value.status == 'Payé'" name="delete" @click="deleteData(data.value)" />
                        </div>
                    </template>
                </vue3-datatable>
            </div>
        </div>
        <!-- <div class="flex flex-col justify-end items-end my-4">
            <table class="w-fit font-semibold text-lg">
                <tr>
                    <td>Montant Total: </td>
                    <td> {{ total }} MAD</td>
                </tr>
                <tr>
                    <td>Montant payé: </td>
                    <td> {{ amount }} MAD</td>
                </tr>
                <tr>
                    <td>Le Reste: </td>
                    <td> {{ rest }} MAD</td>
                </tr>
            </table>
        </div> -->
    </div>
    <Edit :close="() => showEditPopup = false" :showEditPopup="showEditPopup" v-bind:editedData="editedData" v-if="showEditPopup"/>
    <Add :close="() => showPopup = false" :showPopup="showPopup" v-if="showPopup"/>
    <FollowUp
        :close="() => showFollowUp = false"
        :expanse="selectedExpanse" 
        :refresh="() => teacherExpanseStore.index(choosenMonth,choosenYear)"
        :show="showFollowUp"
        />
</template>
<script setup>
    import { ref, reactive, computed, onMounted, watch } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import IconComponent from '@/components/icons/IconComponent.vue'
    import Add from './Add.vue'
    import Edit from './Edit.vue'
    import Swal from 'sweetalert2';
    import {useAuthStore} from '@/stores/auth.js';
    import { useTeacherExpansesStore } from '@/stores/teacherExpanse.js';
    import Multiselect from '@suadelabs/vue3-multiselect';
    import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';
    import * as XLSX from 'xlsx';
    import FollowUp from './FollowUp.vue';
    import { usePaymentsStore } from '@/stores/payments.js';

    const options = ref(['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre', 'Octobre','Novembre','Décembre']);
    const choosenMonth = ref('');
    const years = ref([2024,2025,2026,2027,2028,2029,2030]);
    const choosenYear = ref(new Date().getFullYear());
    const paymentsStore = usePaymentsStore();

    const authStore = useAuthStore();
    const isloading = ref(true);
    
    const params = reactive({
        current_page: 1,
        search: '',
        pagesize: 10,
        sort_column: 'id',
        sort_direction: 'desc',
    });
    const teacherExpanseStore = useTeacherExpansesStore();

    const showPopup = ref(false);
    const showEditPopup = ref(false);
    const total = ref(0)
    const rest = ref(0)
    const amount = ref(0)
    const statusOptions = ref([ 'Tous', 'Payé', 'En attente',
    ]);
    const selectedExpanse = ref(null)
    const showFollowUp = ref(false)

    const selectedStatus = ref('all');
    
    const cols = ref([
        { field: 'teacher', title: "Enseignant", headerClass: '!text-center flex justify-center' },
        { field: 'group', title: 'Groupe', headerClass: '!text-center flex justify-center' },
        { field: 'status', title: "Statut", headerClass: '!text-center flex justify-center' },
        { field: 'total', title: "Montant total", headerClass: '!text-center flex justify-center' },
        { field: 'amount', title: "Montant payé", headerClass: '!text-center flex justify-center' },
        { field: 'rest', title: "Reste", headerClass: '!text-center flex justify-center' },
        { field: 'missing_students', title: "Manquant étudiants", headerClass: '!text-center flex justify-center' },
        { field: 'month', title: "Mois", headerClass: '!text-center flex justify-center' },
        { field: 'date', title: "Date", headerClass: '!text-center flex justify-center' },
        { field: 'actions', title: 'Actions', headerClass: '!text-center flex justify-center' },
    ]);

    const rows = computed(() => {
        if (selectedStatus.value === 'Payé') {
            return teacherExpanseStore.paidExpanses;
        }
        if (selectedStatus.value === 'En attente') {
            return teacherExpanseStore.unpaidExpanses;
        }
        return teacherExpanseStore.allExpanses;
    });
    const openFollowUp = (row) => {
        console.log("selected",row);
        
    selectedExpanse.value = row
    showFollowUp.value = true
    }
//     const totals = computed(() => {
//     let t = 0, a = 0, r = 0;

//     rows.value.forEach(row => {
//         t += Number(row.total);
//         a += Number(row.amount);
//         r += Number(row.rest);
//     });

//     return {
//         total: roundMoney(t, 2),
//         amount: roundMoney(a, 2),
//         rest: roundMoney(r, 2)
//     };
// });

    watch(choosenMonth, async (newVal, oldVal) => { 
        isloading.value = true
        await teacherExpanseStore.index(choosenMonth.value,choosenYear.value)
        isloading.value = false
    });
    watch(choosenYear, async (newVal, oldVal) => { 
        isloading.value = true
        await teacherExpanseStore.index(choosenMonth.value,choosenYear.value)
        isloading.value = false
    });
    const editedData = ref({})
    onMounted(async () => {
        const currentMonth = new Date().getMonth();
        choosenMonth.value = options.value[currentMonth];
        // await teacherExpanseStore.index()
        // isloading.value = false
    })

    const toggleEdit = (data) => {
        editedData.value = data
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
                teacherExpanseStore.destroy(data.id).then(res => {
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
 
    const exportToExcel = () => {
        // Get the attendance data from Vuex
        const attendanceData = rows.value.map(res => ({'Enseignant': res.teacher, 'Groupe': res.group, 'Montant total': res.total, 'Montant payé': res.amount, 'Reste': res.rest, 'Date': res.date}))
        attendanceData.push({'Enseignant': 'Total', 'Groupe': '', 'Montant total': total.value, 'Montant payé': amount.value, 'Reste': rest.value, 'Date': ''})
        // Create a worksheet from the attendance data
        const worksheet = XLSX.utils.json_to_sheet(attendanceData);

        // Create a new workbook and append the worksheet
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'dépenses');

        // Export the workbook to an Excel file
        XLSX.writeFile(workbook, 'dépenses-'+choosenMonth.value+'-'+choosenYear.value+'.xlsx');
    };

const roundMoney = (value, decimals = 2) => {
  return (
    Math.round((Number(value) + Number.EPSILON) * 10 ** decimals) /
    10 ** decimals
  );
};

const mergeFactures = (payments = []) => {
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
const exportAllGroupsPayments = async () => {
  try {
    isloading.value = true;

    const groups = await paymentsStore.fetchAllGroupPayments(
      choosenMonth.value,
      choosenYear.value
    );

    let allRows = [];

    groups.forEach(group => {
      const mergedPayments = mergeFactures(group.payments);
      if (!mergedPayments.length) return;

      mergedPayments.forEach(p => {
        allRows.push({
          'Reçu': p.receipt ?? '',
          'Nom': p.fullName,
          'Groupe': group.group,
          'Montant global': p.total,
          'Montant reçu': p.amount_paid,
          'Reste': p.rest,
          'Réduction': p.reduction,
        });
      });
    });

    if (!allRows.length) {
      console.warn('Aucune donnée à exporter');
      return;
    }

    const worksheet = XLSX.utils.json_to_sheet(allRows);
    const workbook = XLSX.utils.book_new();

    // ✅ ONE sheet, safe name
    const sheetName = 'Details paiements';

    XLSX.utils.book_append_sheet(workbook, worksheet, sheetName);

    XLSX.writeFile(
      workbook,
      `Paiements - Tous groupes - ${choosenMonth.value} ${choosenYear.value}.xlsx`
    );
  } catch (err) {
    console.error(err);
  } finally {
    isloading.value = false;
  }
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