<template>
    <div>
        <div class="panel pb-0 mt-6">
            <h5 class="font-semibold text-lg dark:text-white-light mb-5">Les élèves</h5>
            <div class="flex justify-between mb-4">    
                <input v-model="params.search" type="text" class="form-input max-w-xs" placeholder="Rechercher..." />
                <button type="button" class="btn btn-info" @click="showPopup = true">Ajouter</button>
            </div>
            <div class="datatable">
                <vue3-datatable
                    :rows="studentsStore.students"
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
                        <div class="flex justify-around w-full">
                            <h5 class="text-primary hover:underline">{{ data.value.id }}</h5>
                        </div>
                    </template>
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
                    <template #email="data">
                        <div class="flex justify-around w-full">
                            <a class="font-semibold text-center" >{{ data.value.email }}</a>
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
                    <template #user_id="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.user.firstName + ' ' + data.value.user.lastName }}</p>
                        </div>
                    </template>
                    <template #actions="data">
                        <div class="flex w-fit mx-auto justify-around gap-5">
                            <IconComponent name="edit" @click="() => toggleEdit(data.value)" />
                            <router-link :to="`/students/${data.value.id}/payments`" class="main-logo flex items-center shrink-0">
                                <IconComponent name="view" />
                            </router-link>
                            <IconComponent name="print" @click="printPayment(data.value)" />
                            <IconComponent v-if="authStore?.user && authStore?.user?.roles[0]?.name == 'admin'" name="delete" @click="deleteData(data.value)" />
                        </div>
                    </template>
                </vue3-datatable>
            </div>
        </div>
    </div>
    <Edit :close="() => showEditPopup = false" :showEditPopup="showEditPopup" v-bind:editedData="editedData" v-if="showEditPopup"/>
    <Add :close="() => showPopup = false" :showPopup="showPopup" v-if="showPopup"/>
    <!-- Hidden element to use for printing -->
    <div id="receipt" class="receipt-container hidden m-4">
        <div class="flex flex-col justify-between h-screen">
            <div class="reciept-wrapper h-screen">
                <div class="flex justify-between p-10">
                    <img src="/assets/images/avs-logo.png" alt="Image description" class="w-1/4">
                    <img src="/assets/images/isfpt-logo.jpeg" alt="Image description" class="w-2/5">
                </div>
                <p class="text-center text-3xl font-semibold" style="font-family: 'Courier New', monospace;">RENFORCEMENT ET SOUTIEN SCOLAIRE</p>
                <br>
                <hr >
                <br>
                <div>
                    <p class="text-center text-3xl font-medium" style="font-family: 'Courier New', monospace;">Emploi du temps</p>
                    <br>
                </div>
                <table class="border-collapse border-2 border-gray-500 my-4">
                    <thead>
                        <tr>
                            <th class="border-2 border-gray-500">Jour</th>
                            <th class="border-2 border-gray-500">Matière</th>
                            <th class="border-2 border-gray-500">Professeur</th>
                            <th class="border-2 border-gray-500">Heure</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(payment, index) in selectedPayment" :key="index" class="border-2 border-gray-500">
                            <td class="border-2 border-gray-500">{{ payment.day }}</td>
                            <td class="border-2 border-gray-500">{{ payment.subject }}</td>
                            <td class="border-2 border-gray-500">{{ payment.teacher }}</td>
                            <td class="border-2 border-gray-500">{{ payment.timing }}</td>
                        </tr>
                    </tbody>
                </table>    
            </div>
            <div class="mt-auto">
                <p class="text-center p-2">Centre AVS MA, Avenue Allal Al Fassi - à côté de la boulangerie Alpha 2000 - Marrakech</p>
                <p class="text-center p-2">Centre AVS2 MA, 5ème étage, siège du parti ISTIQLAL, AV AL MOZDALIFA - Marrakech</p>
                <p class="text-center p-2">Tel: 0524311982 / 0661843332 / 0667635797</p>
            </div>    
        </div>
        
    </div>
</template>
<script setup>
    import { ref, reactive, computed, onMounted, nextTick } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import { useStudentsStore } from '@/stores/students.js';
    import IconComponent from '@/components/icons/IconComponent.vue'
    import Add from './Add.vue'
    import Edit from './Edit.vue'
    import Swal from 'sweetalert2';
    import {useAuthStore} from '@/stores/auth.js';
    import html2pdf from "html2pdf.js";
    import { useRegistrantsStore } from '@/stores/registrants.js';

    const authStore = useAuthStore();
    
    const params = reactive({
        current_page: 1,
        search: '',
        pagesize: 10,
        sort_column: 'id',
        sort_direction: 'desc',
    });
    const isloading = ref(false);

    const studentsStore = useStudentsStore();
    const registrantsStore = useRegistrantsStore();

    const showPopup = ref(false);
    const showEditPopup = ref(false);
    const selectedPayment = ref([]);
    const cols =
        ref([
            { field: 'id', title: 'N°', isUnique: true, headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'fullName', title: 'Nom', headerClass: '!text-center flex justify-center', width: 'full' },
            // { field: 'lastName', title: 'Nom', headerClass: '!text-center flex justify-center', width: 'full' },
            // { field: 'firstName', title: 'Prenom', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'email', title: 'Email', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'field', title: "Option", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'level', title: "Niveau", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'phone', title: "Mobile", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'parent_phone', title: "Mobile du parent", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'date', title: "Date d'incription", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'user_id', title: "Auteur", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'actions', title: 'Actions', headerClass: '!text-center flex justify-center', width: 'full' },
        ]) || [];

    const rows = computed(() => {
        return studentsStore.students.length > 0 ? studentsStore.students : [];
        });


    const editedData = ref({})
    onMounted(async () => {
        studentsStore.students.length == 0 && (isloading.value = true)
        await studentsStore.index()
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
                studentsStore.destroy(data.id).then(res => {
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
// Print function using html2pdf.js
const printPayment = async (payment) => {
    let response = await registrantsStore.show(payment.id)
    await response.map(res => {
        JSON.parse(res.timing).map(time => {console.log(time); selectedPayment.value.push({
        id: payment.id,
        day: time.day,
        subject: res.section.subject,
        teacher: res.teacher.firstName + ' ' + res.teacher.lastName,
        timing: time.dates[0]
    })})
    })
    console.log(selectedPayment.value);
    // return
  

  // Temporarily remove the hidden class to display the receipt
  const element = document.getElementById('receipt');
  element.classList.remove('hidden');

  // Wait for the DOM to update
  nextTick(() => {
    const options = {
      margin: 1,
      filename: `emploi-${payment.firstName + '_' + payment.lastName}.pdf`,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
    };

    html2pdf().from(element).set(options).save().then(() => {
      // Add the hidden class again after printing
      element.classList.add('hidden');
    });
    selectedPayment.value = []
  });
};
</script>
