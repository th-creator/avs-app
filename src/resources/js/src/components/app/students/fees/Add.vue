<template>
    <div>{{ student }}
        <TransitionRoot appear :show="showPopup" as="template">
            <Dialog as="div" @close="close()" class="relative z-50">
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
                    <div class="text-lg text-center font-semibold ltr:pl-5 rtl:pr-5 py-5 ltr:pr-[50px] rtl:pl-[50px]">Ajouter</div>
                    <div class="p-5">
                        <form>
                            <div class="relative mb-4">
                                <input v-model="data.amount" type="number" placeholder="Montant" class="form-input" />
                                <span v-if="errors.amount" class="text-red-600 text-sm">{{ errors.amount[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <input v-model="data.reduction" type="number" placeholder="Reduction" class="form-input" />
                                <span v-if="errors.reduction" class="text-red-600 text-sm">{{ errors.reduction[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <input v-model="data.rest" type="number" placeholder="Reste" class="form-input" />
                                <span v-if="errors.rest" class="text-red-600 text-sm">{{ errors.rest[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <input v-model="data.date" type="date" placeholder="Date d'inscription" class="form-input" />
                                <span v-if="errors.date" class="text-red-600 text-sm">{{ errors.date[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <input v-model="data.type" type="text" placeholder="Type de paiement" class="form-input" />
                                <span v-if="errors.type" class="text-red-600 text-sm">{{ errors.type[0] }}</span>
                            </div>
                            <div v-if="data.type == 'bank'" class="relative mb-4">
                                <input v-model="data.bank" type="text" placeholder="Bank" class="form-input" />
                                <span v-if="errors.bank" class="text-red-600 text-sm">{{ errors.bank[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <input v-model="data.receipt" type="text" placeholder="Receipt" class="form-input" />
                                <span v-if="errors.receipt" class="text-red-600 text-sm">{{ errors.receipt[0] }}</span>
                            </div>
                            <button type="button" class="btn btn-primary w-full" @click="Create()">Submit</button>
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

const studentsStore = useStudentsStore();

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
    date: '',
    fullName: '',
    group: '',
    amount: '',
    reduction: '',
    rest: 0,
    type: '',
    bank: '',
    receipt: '',
    user_id: '',
    student_id: '',
})
const errors = ref({})

const Create = () => {
    errors.value = []
    data.value.user_id = authStore?.user?.id
    data.value.student_id = route.params.id
    data.value.fullName = studentsStore.student && studentsStore.student.firstName + ' ' + studentsStore.student && studentsStore.student.lastName
    feesStore.store(data.value).then(res => {
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