<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex justify-between gap-2">    
                <h5 class="font-semibold text-lg dark:text-white-light mb-5">Les Inscrits</h5>
                <button type="button" class="btn btn-warning w-40 h-9" @click="exportToExcel()">Exporter</button>  
            </div>
            <div class="flex justify-between mb-4">    
                <input v-model="params.search" type="text" class="form-input max-w-xs" placeholder="Rechercher..." />
                <div class="flex flex-row gap-4">
                    <div class="flex gap-2">
                        <multiselect
                            v-model="choosenAY"
                            :options="AYs"
                            class="custom-multiselect  max-w-xs"
                            :searchable="true"
                            placeholder="Le mois"
                            selected-label=""
                            select-label=""
                            deselect-label=""
                        ></multiselect>    
                    </div>
                    <button type="button" class="btn btn-info" @click="showPopup = true">Ajouter</button>
                </div>
            </div>
            <div class="datatable">
                <vue3-datatable
                    :rows="registrantsStore.registrants"
                    :search="params.search" 
                    :columns="cols"
                    :totalRows="rows?.length"
                    :sortable="true"
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
                    <template #fullName="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.fullName }}</p>
                        </div>
                    </template>
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
                        <div class="flex justify-center w-full">
                            <div v-if="data.value.status == 1">
                                <div class="px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 w-[120px] text-center text-sm">
                                    Actif
                                </div>
                            </div>
                            <div v-else-if="data.value.status == 0">
                                <div class="px-4 py-2 rounded-full bg-orange-100 text-orange-600 w-[120px] text-center text-sm">
                                    Désactivé
                                </div>
                            </div>
                            <div v-else-if="data.value.status == -1">
                                <div class="px-4 py-2 rounded-full bg-blue-100 text-blue-600 w-[120px] text-center text-sm">
                                    Remboursé
                                </div>
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
                            <router-link v-if="data.value.student_id" :to="`/students/${data.value.student_id}/payments`" class="main-logo flex items-center shrink-0">
                                <IconComponent name="view" />
                            </router-link>
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
    import { ref, reactive, computed, onMounted, watch } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import { useRegistrantsStore } from '@/stores/registrants.js';
    import IconComponent from '@/components/icons/IconComponent.vue'
    import Add from './Add.vue'
    import Edit from './Edit.vue'
    import Transfer from './Transfer.vue'
    import Swal from 'sweetalert2';
import {useAuthStore} from '@/stores/auth.js';
import Multiselect from '@suadelabs/vue3-multiselect';
    import * as XLSX from 'xlsx';


const authStore = useAuthStore();
    
    const params = reactive({
        current_page: 1,
        search: '',
        pagesize: 10,
        sort_column: 'ay_no',
        sort_direction: 'desc',
    });
    const isloading = ref(false);

    const registrantsStore = useRegistrantsStore();

    const AYs = ref(['2024/2025','2025/2026','2026/2027','2027/2028','2028/2029','2029/2030','2030/2031','2031/2032','2032/2033', '2033/2034']);
    const choosenAY = ref(getCurrentAY())

    const showPopup = ref(false);
    const showEditPopup = ref(false);
    const showTransferPopup = ref(false);

    function getCurrentAY() {
        const now = new Date()
        const y = now.getFullYear()
        const m = now.getMonth() + 1 // JS months are 0-based

        if (m >= 9) {
            return `${y}/${y + 1}`   // e.g., Sept 2025 → "2025/2026"
        } else {
            return `${y - 1}/${y}`   // e.g., Feb 2025 → "2024/2025"
        }
    }
    watch(choosenAY, async () => {
        isloading.value = true
        await registrantsStore.index(choosenAY.value)
        isloading.value = false
    });
    
    const cols =
        ref([
            // { field: 'id', title: 'ID', isUnique: true, headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'ay_no', title: "N°", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'fullName', title: 'Nom', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'group', title: "Groupe", headerClass: '!text-center flex justify-center', width: 'full' },
            // { field: 'email', title: 'Email', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'field', title: "Spécialité", headerClass: '!text-center flex justify-center', width: 'full' },
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
        
        return data;
        });

    const editedData = ref({})

    onMounted(async () => {
        registrantsStore.registrants.length == 0 && (isloading.value = true)
        await registrantsStore.index(choosenAY.value)
        isloading.value = false
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
    const exportToExcel = () => {
  // 1) Collapse registrants so each student appears once
  const byStudent = new Map();

  // assuming registrantsStore.registrants is the registrants array you showed
  for (const r of registrantsStore.registrants) {
    const sid = r.student_id;
    const fullName = r.fullName;
    const groupName = r.group ?? '';

    if (!byStudent.has(sid)) {
      byStudent.set(sid, {
        ay_no: r.ay_no,
        fullName,
        groups: groupName ? [groupName] : [],
        field: r.field ?? '',
        level: r.level ?? '',
        parent_phone: r.parent_phone ?? '',
        phone: r.phone ?? '',
        status: r.status,
        date: r.date ?? r.enter_date ?? '',
      });
    } else {
      const s = byStudent.get(sid);
      if (groupName && !s.groups.includes(groupName)) s.groups.push(groupName);
      // keep earliest date if you want:
      // if (r.date && (!s.date || r.date < s.date)) s.date = r.date;
    }
  }

  // 2) Build export rows (groups stacked with newlines in one cell)
  // Excel shows new lines if Wrap Text is enabled; most users toggle it.
  const rows = Array.from(byStudent.values())
    .sort((a, b) => (a.ay_no ?? 0) - (b.ay_no ?? 0)) // optional: sort by AY number
    .map(s => ({
      'N°': s.ay_no,
      'Nom': s.fullName,
      'Groupes': s.groups.join('\n'),      // stacked lines in one cell
      'Spécialité': s.field,
      'Niveau': s.level,
      'Mobile du parent': s.parent_phone,
      'Mobile': s.phone,
      'État': s.status,
      "Date d'inscription": s.date,
    }));

  // 3) Export via xlsx
  const ws = XLSX.utils.json_to_sheet(rows);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, 'Les Inscrits');

  // (Optional) set column widths so group text is readable
  ws['!cols'] = [
    { wch: 6 },   // N°
    { wch: 28 },  // Nom
    { wch: 60 },  // Groupes
    { wch: 12 },  // Spécialité
    { wch: 10 },  // Niveau
    { wch: 16 },  // Mobile du parent
    { wch: 14 },  // Mobile
    { wch: 8 },   // État
    { wch: 14 },  // Date d'inscription
  ];

  XLSX.writeFile(wb, 'Les Inscrits.xlsx');
};

</script>
