<template>
    <div>
        <TransitionRoot appear :show="showPopup" as="template">
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
                    <DialogPanel class="panel border-0 px-4 py-1 rounded-lg overflow-hidden w-full max-w-lg text-black dark:text-white-dark">
                    <button type="button" class="absolute top-7 ltr:right-9 rtl:left-9 text-white-dark hover:text-dark outline-none" @click="close()">
                        X
                    </button>
                    <div class="text-lg text-center font-semibold ltr:pl-5 rtl:pr-5 py-5 ltr:pr-[50px] rtl:pl-[50px]">Ajouter</div>
                    <div class="p-5">
                        <form>
                            <div class="relative mb-4">
                                <label class="text-sm">Groupe:</label>
                                <multiselect
                                    v-model="data.groupHolder"
                                    :options="groups"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="Groupe"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.group_id" class="text-red-600 text-sm">{{ errors.group_id[0] }}</span>
                            </div>
                            <div class="relative mb-4 flex gap-4">
                                <div class="w-[70%]">
                                    <label class="text-sm">Montant:</label>
                                    <input @keyup="calculateRest()" v-model="data.amount" type="number" placeholder="Montant" class="form-input" />
                                    <span v-if="errors.amount" class="text-red-600 text-sm">{{ errors.amount[0] }}</span>
                                </div>
                                <div class="w-[30%]">
                                    <label class="text-sm">Réduction:</label>
                                    <input @keyup="calculateRest()" v-model="data.reduction" type="number" placeholder="Reduction" class="form-input" />
                                    <span v-if="errors.reduction" class="text-red-600 text-sm">{{ errors.reduction[0] }}</span>
                                </div>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Montant à payer:</label>
                                <input v-model="data.total" type="number" placeholder="Montant a payer" class="form-input" />
                                <span v-if="errors.total" class="text-red-600 text-sm">{{ errors.total[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Montant reçu:</label>
                                <input v-model="data.amount_paid" @keyup="calculatePayer()" type="number" placeholder="Montant reçu" class="form-input" />
                                <span v-if="errors.amount_paid" class="text-red-600 text-sm">{{ errors.amount_paid[0] }}</span>
                            </div>  
                            <div class="relative mb-4">
                                <label class="text-sm">Reste à payer:</label>
                                <input v-model="data.rest" type="number" placeholder="Reste a payer" class="form-input" />
                                <span v-if="errors.rest" class="text-red-600 text-sm">{{ errors.rest[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Date de paiement:</label>
                                <input v-model="data.date" type="date" placeholder="Date d'inscription" class="form-input" />
                                <span v-if="errors.date" class="text-red-600 text-sm">{{ errors.date[0] }}</span>
                            </div>
                            <div class="relative mb-4 flex gap-4">
                                <div class="w-[50%]">
                                    <label class="text-sm">Mois:</label>
                                    <multiselect
                                        v-model="data.month"
                                        :options="months"
                                        class="custom-multiselect"
                                        :searchable="true"
                                        placeholder="Mois"
                                        selected-label=""
                                        select-label=""
                                        deselect-label=""
                                    ></multiselect>
                                    <span v-if="errors.month" class="text-red-600 text-sm">{{ errors.month[0] }}</span>    
                                </div>
                                <div class="w-[50%]">
                                    <label class="text-sm">Année:</label>
                                    <input v-model="data.year" type="number" placeholder="Année" class="form-input" />
                                    <span v-if="errors.year" class="text-red-600 text-sm">{{ errors.year[0] }}</span>
                                </div>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Type de paiement:</label>
                                <multiselect
                                    v-model="data.type"
                                    :options="options"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="Type de paiement"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.student_id" class="text-red-600 text-sm">{{ errors.student_id[0] }}</span>
                            </div>
                            <div v-if="data.type == 'chèque'" class="relative mb-4">
                                <label class="text-sm">Bank:</label>
                                <input v-model="data.bank" type="text" placeholder="Bank" class="form-input" />
                                <span v-if="errors.bank" class="text-red-600 text-sm">{{ errors.bank[0] }}</span>
                            </div>
                            <div v-if="data.type == 'chèque'" class="relative mb-4">
                                <label class="text-sm">Chèque:</label>
                                <input v-model="data.bank_receipt" type="text" placeholder="Chèque" class="form-input" />
                                <span v-if="errors.bank_receipt" class="text-red-600 text-sm">{{ errors.bank_receipt[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Reçu:</label>
                                <input v-model="data.receipt" type="text" placeholder="Receipt" class="form-input" />
                                <span v-if="errors.receipt" class="text-red-600 text-sm">{{ errors.receipt[0] }}</span>
                            </div>
                            <button type="button" class="btn btn-primary w-full h-10" @click="Create()">
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
import { ref, defineProps, onMounted, computed, watch } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { usePaymentsStore } from '@/stores/payments.js';
import { useAlert } from '@/composables/useAlert';
import {useAuthStore} from '@/stores/auth.js';
import Multiselect from '@suadelabs/vue3-multiselect';
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';
import { useRoute } from 'vue-router';
import { useStudentsStore } from '@/stores/students.js';
import IconComponent from '@/components/icons/IconComponent.vue'
import { useGroupsStore } from '@/stores/groups.js';

const isLoading = ref(false)

const options = ref(['espèces', 'chèque', 'virement']);
const months = ref(['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre', 'Octobre','Novembre','Décembre']);

const studentsStore = useStudentsStore();
const groupsStore = useGroupsStore();

const data = ref({
    date: '',
    fullName: '',
    group: '',
    amount: 0,
    reduction: 0,
    rest: 0,
    amount_paid: 0,
    total: 0,
    type: 'espèces',
    bank: '',
    bank_receipt: '',
    receipt: '',
    month: '',
    year: 0,
    groupHolder: '',
    group_id: '',
    user_id: '',
    student_id: '',
})

const calculateRest = () => {
    let reduction = data.value.reduction == null ? 0 : data.value.reduction
    data.value.total = data.value.amount*(100-reduction)/100
}
const calculatePayer = () => {
    data.value.rest = data.value.total - data.value.amount_paid
}
watch(async () => data.value.groupHolder, (newVal, oldVal) => {
    const id = Number(data.value.groupHolder.split(':')[0].trim())
    var group = {}
    group = groupsStore.studentGroups.find(res => res.id == id);
    data.value.amount = group.section.price 
    paymentsStore.registrantPayment(route.params.id,id).then(res => {       
        if(res.data.data) {
            data.value.reduction = res.data.data.reduction
            let reduction = data.value.reduction == null ? 0 : data.value.reduction
            data.value.total = data.value.amount*(100-reduction)/100
        }       
    })
});

const paymentsStore = usePaymentsStore();
const authStore = useAuthStore();
const route = useRoute();

const props = defineProps({
    showPopup: {
        type: Boolean,
        required: true,
    },
    close: {
    type: Function,
    required: true,
  },
}); 

const errors = ref({})
const groups = computed(() => {
        return groupsStore.studentGroups.length > 0 ? groupsStore.studentGroups.map(res => res.id + ' : ' + res.intitule) : [];
        });
onMounted(async () => {
    await groupsStore.fetchStudentGroups(studentsStore.student.id)
    const currentMonth = new Date().getMonth();
    data.value.month = months.value[currentMonth];
    data.value.year = new Date().getFullYear();
    // console.log();
}) 
const Create = () => {
    if (isLoading.value == false) {
        isLoading.value = true
        errors.value = []
        data.value.user_id = authStore?.user?.id
        data.value.student_id = route.params.id
        data.value.group_id = data.value.groupHolder.split(':')[0].trim()
        data.value.fullName = studentsStore.student.firstName + ' ' +  studentsStore.student.lastName
        paymentsStore.store(data.value).then(res => {
            isLoading.value = false
            useAlert('success', 'Créé avec succès!');
            props.close()
        }).catch((err) => {
            isLoading.value = false
            if(err.status == 422) {
                errors.value =  err.response.data.errors;
                useAlert('warning', "quelque chose s'est mal passé!");
            } else if(err.status == 400) {
                useAlert('warning', "Le paiement existe déjà!");
            } else useAlert('warning', "quelque chose s'est mal passé!");
        });
    }
}
</script>