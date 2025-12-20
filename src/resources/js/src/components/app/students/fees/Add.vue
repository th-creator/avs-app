<template>
    <div>{{ student }}
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
                                <label class="text-sm">Montant:</label>
                                <input @keyup="calculateRest()" v-model="data.amount" type="number" placeholder="Montant" class="form-input" />
                                <span v-if="errors.amount" class="text-red-600 text-sm">{{ errors.amount[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Reduction:</label>
                                <input @keyup="calculateRest()" v-model="data.reduction" type="number" placeholder="Reduction" class="form-input" />
                                <span v-if="errors.reduction" class="text-red-600 text-sm">{{ errors.reduction[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Montant à payer:</label>
                                <input v-model="data.total" type="number" placeholder="Montant à payer" class="form-input" />
                                <span v-if="errors.total" class="text-red-600 text-sm">{{ errors.total[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Montant reçu:</label>
                                <input v-model="data.amount_paid" @keyup="calculatePayer()" type="number" placeholder="Montant reçu" class="form-input" />
                                <span v-if="errors.amount_paid" class="text-red-600 text-sm">{{ errors.amount_paid[0] }}</span>
                            </div>  
                            <div class="relative mb-4">
                                <label class="text-sm">Reste à payer :</label>
                                <input v-model="data.rest" type="number" placeholder="Reste a payer" class="form-input" />
                                <span v-if="errors.rest" class="text-red-600 text-sm">{{ errors.rest[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Date d'inscription:</label>
                                <input v-model="data.date" type="date" placeholder="Date d'inscription" class="form-input" />
                                <span v-if="errors.date" class="text-red-600 text-sm">{{ errors.date[0] }}</span>
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
                                <span v-if="errors.type" class="text-red-600 text-sm">{{ errors.type[0] }}</span>
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
                                <label class="text-sm">Receipt:</label>
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
import { ref, defineProps, computed } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { useFeesStore } from '@/stores/fees.js';
import { useAlert } from '@/composables/useAlert';
import {useAuthStore} from '@/stores/auth.js';
import Multiselect from '@suadelabs/vue3-multiselect';
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';
import { useRoute } from 'vue-router';
import { useStudentsStore } from '@/stores/students.js';
import IconComponent from '@/components/icons/IconComponent.vue'

const isLoading = ref(false)

const options = ref(['espèces', 'chèque', 'virement']);

const studentsStore = useStudentsStore();

const calculateRest = () => {
    let reduction = data.value.reduction == null ? 0 : data.value.reduction
    data.value.total = data.value.amount*(100-reduction)/100
}
const calculatePayer = () => {
    data.value.rest = data.value.total - data.value.amount_paid
}

const feesStore = useFeesStore();
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

const data = ref({
    date: new Date().toISOString().slice(0, 10),
    fullName: '',
    group: '',
    amount: 0,
    reduction: 0,
    rest: 0,
    amount_paid: 0,
    type: 'espèces',
    total: 0,
    bank: '',
    bank_receipt: '',
    receipt: '',
    user_id: '',
    student_id: '',
})
const errors = ref({})

const Create = () => {
    if (isLoading.value == false) {
        isLoading.value = true
        errors.value = []
        data.value.user_id = authStore?.user?.id
        data.value.student_id = route.params.id
        data.value.fullName = studentsStore.student.firstName + ' ' +  studentsStore.student.lastName
        feesStore.store(data.value).then(res => {
            isLoading.value = false
            useAlert('success', 'Créé avec succès!');
            props.close()
        }).catch((err) => {
            isLoading.value = false
            if(err.status == 422) {
                errors.value =  err.response.data.errors;
            }
            useAlert('warning', "quelque chose s'est mal passé!");
        });
    }
}
</script>