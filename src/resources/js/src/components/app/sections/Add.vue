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
                    <DialogPanel class="panel border-0 px-4 py-1 rounded-lg w-full max-w-lg text-black dark:text-white-dark">
                    <button type="button" class="absolute top-7 ltr:right-9 rtl:left-9 text-white-dark hover:text-dark outline-none" @click="close()">
                        X
                    </button>
                    <div class="text-lg text-center font-semibold ltr:pl-5 rtl:pr-5 py-5 ltr:pr-[50px] rtl:pl-[50px]">Ajouter</div>
                    <div class="p-5">
                        <form>
                            <div class="relative mb-4">
                                <label class="text-sm">Matière:</label>
                                <multiselect
                                    v-model="data.subject"
                                    :options="options"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="Matière"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.subject" class="text-red-600 text-sm">{{ errors.subject[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Niveau:</label>
                                <multiselect
                                    v-model="data.level"
                                    :options="levels"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="Niveau"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.level" class="text-red-600 text-sm">{{ errors.level[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Prix:</label>
                                <input v-model="data.price" type="text" placeholder="Prix" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.price" class="text-red-600 text-sm">{{ errors.price[0] }}</span>
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
import { ref, defineProps } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { useSectionsStore } from '@/stores/sections.js';
import { useAlert } from '@/composables/useAlert';
import {useAuthStore} from '@/stores/auth.js';
import Multiselect from '@suadelabs/vue3-multiselect';
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

const options = ref(['PC', 'Maths', 'SVT', 'Comptabilité', 'Economie générale', 'Gestion d\'entreprise', 'Philosophie', 'Anglais', 'Francais', 'Arabe', 'Education islamique', 'Sciences de l\'ingénieurie', 'Allemend', 'Histoire géographie', 'Anglais communication', 'Français comminication']);
const levels = ref(['6em primaire', '1 AC', '2 AC', '3 AC', 'TC', '1 Bac SC ECO', '1 Bac SM1', '1 Bac SC EXP', '1Bac SM2', '2 Bac SP', '2 Bac SP', '2 Bac SM', '2 Bac SC ECO']);

const sectionsStore = useSectionsStore();
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
    firstName: '',
    lastName: '',
    email: '',
    date: '',
    phone: '',
    subject: '',
    user_id: '',
})
const errors = ref({})

const Create = () => {
    data.value.user_id = authStore?.user?.id
    errors.value = {}
    sectionsStore.store(data.value).then(res => {
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
