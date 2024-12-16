<template>
    <div>
        <TransitionRoot appear v-if="!showEdit" :show="showEditPopup" as="template">
            <Dialog as="div"  class="relative z-50">
            <TransitionChild 
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <DialogOverlay class="fixed inset-0 bg-[black]/60" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-start justify-center px-4 py-8">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0 scale-95"
                    enter-to="opacity-100 scale-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100 scale-100"
                    leave-to="opacity-0 scale-95"
                >
                    <DialogPanel class="panel border-0 px-4 py-1 rounded-lg w-full max-w-xl text-black dark:text-white-dark">
                    <button type="button" class="absolute top-7 ltr:right-9 rtl:left-9 text-white-dark hover:text-dark outline-none" @click="close()">
                        X
                    </button>
                    <div class="text-lg text-center font-semibold ltr:pl-5 rtl:pr-5 py-5 ltr:pr-[50px] rtl:pl-[50px]">Modifier</div>
                    <div class="p-5">
                        <form>
                            <div class="relative mb-4">
                                <label class="text-sm">Elève:</label>
                                <multiselect
                                    v-model="data.student"
                                    :options="students"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="Elève"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.student_id" class="text-red-600 text-sm">{{ errors.student_id[0] }}</span>
                            </div>
                            <!-- <div class="relative mb-4">
                                <label class="text-sm">Groupe:</label>
                                <multiselect
                                    v-model="data.group"
                                    :options="groups"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="Groupe"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.group_id" class="text-red-600 text-sm">{{ errors.group_id[0] }}</span>
                            </div> -->
                            <div class="relative mb-4">
                                <label class="text-sm">Centre:</label> 
                                <multiselect
                                    v-model="data.center"
                                    :options="options"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="Centre"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.center" class="text-red-600 text-sm">{{ errors.center[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Date d'inscription:</label>
                                <input v-model="data.date" type="date" placeholder="Date d'inscription" class="form-input" />
                                <span v-if="errors.date" class="text-red-600 text-sm">{{ errors.date[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Date d'entrée:</label>
                                <input v-model="data.enter_date" type="date" placeholder="Date d'entrée" class="form-input" />
                                <span v-if="errors.enter_date" class="text-red-600 text-sm">{{ errors.enter_date[0] }}</span>
                            </div>
                            <div class="relative mb-4"  v-if="authStore?.user && authStore?.user?.roles[0]?.name == 'admin'">
                                <label class="text-sm">Etat:</label> 
                                <multiselect
                                    v-model="data.status_show"
                                    :options="etats"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="Etat"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.center" class="text-red-600 text-sm">{{ errors.center[0] }}</span>
                            </div>
                            <button type="button" class="btn btn-primary w-full h-10" @click="Edit()">
                                <IconComponent v-if="isLoading" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" name="loading" />
                                <span v-else>Soumettre</span>
                            </button>
                        </form>
                    </div>
                    </DialogPanel>
                </TransitionChild>
                </div>
            </div>
            </Dialog>
        </TransitionRoot>
        <TransitionRoot appear :show="showEdit" as="template">
            <Dialog as="div"  class="relative z-50">
            <TransitionChild 
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <DialogOverlay class="fixed inset-0 bg-[black]/60" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-start justify-center px-4 py-8">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0 scale-95"
                    enter-to="opacity-100 scale-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100 scale-100"
                    leave-to="opacity-0 scale-95"
                >
                    <DialogPanel class="panel border-0 px-4 py-1 rounded-lg w-full max-w-xl text-black dark:text-white-dark">
                    <button type="button" class="absolute top-7 ltr:right-9 rtl:left-9 text-white-dark hover:text-dark outline-none" @click="close()">
                        X
                    </button>
                    <div class="text-lg text-center font-semibold ltr:pl-5 rtl:pr-5 py-5 ltr:pr-[50px] rtl:pl-[50px]">Rembourser</div>
                    <div class="p-5">
                        <form>
                            <div class="relative mb-4">
                                <label class="text-sm">Montant reçu:</label>
                                <input readonly v-model="data.amount_recieved" type="number" placeholder="Montant remboursé" class="form-input" />
                                <span v-if="errors.amount_recieved" class="text-red-600 text-sm">{{ errors.amount_recieved[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Montant remboursé:</label>
                                <input v-model="data.amount" type="number" placeholder="Montant remboursé" class="form-input" />
                                <span v-if="errors.amount" class="text-red-600 text-sm">{{ errors.amount[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Date de remboursement:</label>
                                <input v-model="data.date_refund" type="date" placeholder="Date de remboursement" class="form-input" />
                                <span v-if="errors.date_refund" class="text-red-600 text-sm">{{ errors.date_refund[0] }}</span>
                            </div>
                            <button type="button" class="btn btn-primary w-full h-10" @click="EditRefund()">
                                <IconComponent v-if="isLoading" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" name="loading" />
                                <span v-else>Soumettre</span>
                            </button>
                        </form>
                    </div>
                    </DialogPanel>
                </TransitionChild>
                </div>
            </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script setup>
import { ref, defineProps, computed, onMounted  } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { useStudentsStore } from '@/stores/students.js';
import { useRegistrantsStore } from '@/stores/registrants.js';
import { useAlert } from '@/composables/useAlert';
import Multiselect from '@suadelabs/vue3-multiselect';
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';
import IconComponent from '@/components/icons/IconComponent.vue'

import {useAuthStore} from '@/stores/auth.js';

const authStore = useAuthStore();
const isLoading = ref(false)
const showEdit = ref(false)

const options = ref(['AVS', 'ISFPT']);
const etats = ref(['Active', 'Remboursé', 'Désactivé']);
const registrantsStore = useRegistrantsStore();
const studentsStore = useStudentsStore();

const students = computed(() => {
        return studentsStore.students.length > 0 ? studentsStore.students.map(res => {
            if(data.value.student == res.id) {
            data.value.student = res.id + ' : ' + res.firstName + ' '+ res.lastName;
            return res.id + ' : ' + res.firstName + ' '+ res.lastName
        } else return res.id + ' : ' + res.firstName + ' '+ res.lastName}) : [];
        });

onMounted(() => {
    studentsStore.index()
})
const props = defineProps({
    showEditPopup: {
        type: Boolean,
        required: true,
    },
    editedData: {
        type: Object,
        required: true,
    },
    close: {
    type: Function,
    required: true,
  },
});

const data = ref({
    center: props.editedData.center,
    date: props.editedData.date,
    enter_date: props.editedData.enter_date,
    group: props.editedData.group_id,
    group_id: props.editedData.group_id,
    student: props.editedData.student_id,
    student_id: props.editedData.student_id,
    status_show: props.editedData.status == -1 ? 'Remboursé' : props.editedData.status == 0 ? 'Désactivé' : 'Actif' ,
    status: props.editedData.status,
    date_refund: '',
    amount_recieved: 0,
})

const errors = ref({})

const Edit = () => {
    isLoading.value = true
    data.value.status = data.value.status_show == 'Remboursé' ? -1 : data.value.status_show == 'Désactivé' ? 0 : 1
    registrantsStore.update(data.value,props.editedData.id).then(res => {
        console.log('dataaaa',res);
        if(res.data.payment) {
            data.value.amount_recieved = res.data.payment.amount_paid
        }
        if(data.value.status == 1) {
            props.close()
        }
        isLoading.value = false
        useAlert('success', 'Créé avec succès!');
        data.value.status == -1 && (showEdit.value = true)
    }).catch((err) => {
        isLoading.value = false
        if(err.status == 422) {
            errors.value =  err.response.data.errors;
        }
        useAlert('warning', "quelque chose s'est mal passé!");
    });
}
const EditRefund = () => {
    isLoading.value = true
    registrantsStore.refund(data.value,props.editedData.id).then(res => {
        isLoading.value = false
        useAlert('success', 'Créé avec succès!');
        showEdit.value = false
        props.close()
    }).catch((err) => {
        isLoading.value = false
        if(err.status == 422) {
            errors.value =  err.response.data.errors;
        }
        useAlert('warning', "quelque chose s'est mal passé!");
    });
}
</script>