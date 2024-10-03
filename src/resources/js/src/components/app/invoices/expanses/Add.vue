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
                                <label class="text-sm">Titre:</label>
                                <input v-model="data.title" type="text" placeholder="Titre" class="form-input" />
                                <span v-if="errors.title" class="text-red-600 text-sm">{{ errors.title[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Montant:</label>
                                <input v-model="data.amount" type="number" placeholder="Montant" class="form-input" />
                                <span v-if="errors.amount" class="text-red-600 text-sm">{{ errors.amount[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Date:</label>
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
                                <label class="text-sm">Document:</label>
                                <input
                                    @change="handleFileChange"
                                    type="file"
                                    placeholder="Document"
                                    class="form-input"
                                />
                                <span v-if="errors.file" class="text-red-600 text-sm">{{ errors.file[0] }}</span>
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
import { useExpansesStore } from '@/stores/expanses.js';
import { useAlert } from '@/composables/useAlert';
import {useAuthStore} from '@/stores/auth.js';
import Multiselect from '@suadelabs/vue3-multiselect';
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

const options = ref(['espèces', 'chèque']);

const expansesStore = useExpansesStore();
const authStore = useAuthStore();

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
    title: '',
    amount: '',
    bank: '',
    bank_receipt: '',
    type: 'espèces',
    user_id: '',
    file: '',
})
const errors = ref({})

const handleFileChange = ($e) => {
  const file = $e.target.files[0];

  if (!file) return;

  data.value.file = file;
};
const Create = () => {
    errors.value = []
    const formData = new FormData();
    formData.append('file', data.value.file);
    formData.append('date', data.value.date);
    formData.append('amount', data.value.amount);
    formData.append('title', data.value.title);
    formData.append('bank', data.value.bank);
    formData.append('bank_receipt', data.value.bank_receipt);
    formData.append('type', data.value.type);
    formData.append('user_id', authStore?.user?.id);
    data.value.user_id = authStore?.user?.id
    expansesStore.store(formData).then(res => {
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