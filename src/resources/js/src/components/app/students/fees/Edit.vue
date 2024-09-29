<template>
    <div>
        <TransitionRoot appear :show="showEditPopup" as="template">
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
                    <DialogPanel class="panel border-0 px-4 py-1 rounded-lg overflow-hidden w-full max-w-sm text-black dark:text-white-dark">
                    <button type="button" class="absolute top-7 ltr:right-9 rtl:left-9 text-white-dark hover:text-dark outline-none" @click="close()">
                        X
                    </button>
                    <div class="text-lg text-center font-semibold ltr:pl-5 rtl:pr-5 py-5 ltr:pr-[50px] rtl:pl-[50px]">Modifier</div>
                    <div class="p-5">
                        <form>
                            <div class="relative mb-4">
                                <label class="text-sm">Montant:</label>
                                <input v-model="data.amount" type="number" placeholder="Montant" class="form-input" />
                                <span v-if="errors.amount" class="text-red-600 text-sm">{{ errors.amount[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Reduction:</label>
                                <input v-model="data.reduction" type="number" placeholder="Reduction" class="form-input" />
                                <span v-if="errors.reduction" class="text-red-600 text-sm">{{ errors.reduction[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Montant a payer:</label>
                                <input v-model="data.total" type="number" placeholder="Montant a payer" class="form-input" />
                                <span v-if="errors.total" class="text-red-600 text-sm">{{ errors.total[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Reste a payer:</label>
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
                                <label class="text-sm">Receipt:</label>
                                <input v-model="data.receipt" type="text" placeholder="Receipt" class="form-input" />
                                <span v-if="errors.receipt" class="text-red-600 text-sm">{{ errors.receipt[0] }}</span>
                            </div>
                            <button type="button" class="btn btn-primary w-full" @click="Edit()">Submit</button>
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
import { ref, defineProps } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { useFeesStore } from '@/stores/fees.js';
import { useAlert } from '@/composables/useAlert';
import Multiselect from '@suadelabs/vue3-multiselect';
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

const options = ref(['espèces', 'chèque']);

const feesStore = useFeesStore();

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
    date: props.editedData.date,
    amount: props.editedData.amount,
    reduction: props.editedData.reduction,
    rest: props.editedData.rest,
    type: props.editedData.type,
    bank: props.editedData.bank,
    total: props.editedData.total,
    bank_receipt: props.editedData.bank_receipt,
    receipt: props.editedData.receipt,
})

const errors = ref({})

const Edit = () => {
    feesStore.update(data.value,props.editedData.id).then(res => {
        useAlert('success', 'Créé avec succès!');
        props.close()
    }).catch((err) => {
        if(err.status == 422) {
            errors.value =  err.response.data.errors;
        }
        useAlert('warning', "quelque chose s'est mal passé!");
    });
}
</script>