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
                                <label class="text-sm">Prenom:</label>
                                <input v-model="data.firstName" type="text" placeholder="Prenom" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.firstName" class="text-red-600 text-sm">{{ errors.firstName[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Nom:</label>
                                <input v-model="data.lastName" type="text" placeholder="Nom" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.lastName" class="text-red-600 text-sm">{{ errors.lastName[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">E-mail:</label>
                                <input v-model="data.email" type="email" placeholder="E-mail" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.email" class="text-red-600 text-sm">{{ errors.email[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Date d'inscription:</label>
                                <input v-model="data.date" type="date" placeholder="Date d'inscription" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.date" class="text-red-600 text-sm">{{ errors.date[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Mobile:</label>
                                <input v-model="data.phone" type="text" placeholder="Mobile" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.phone" class="text-red-600 text-sm">{{ errors.phone[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Mobile du parent:</label>
                                <input v-model="data.parent_phone" type="text" placeholder="Mobile du parent" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.parent_phone" class="text-red-600 text-sm">{{ errors.parent_phone[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">École:</label>
                                <input v-model="data.school" type="text" placeholder="École" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.school" class="text-red-600 text-sm">{{ errors.school[0] }}</span>
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
                                <label class="text-sm">option:</label>
                                <multiselect
                                    v-model="data.field"
                                    :options="options"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="option"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.field" class="text-red-600 text-sm">{{ errors.field[0] }}</span>
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
import { ref, defineProps } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { useStudentsStore } from '@/stores/students.js';
import { useAlert } from '@/composables/useAlert';
import {useAuthStore} from '@/stores/auth.js';
import Multiselect from '@suadelabs/vue3-multiselect';
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';
import IconComponent from '@/components/icons/IconComponent.vue'

const isLoading = ref(false)

const levels = ref(['6em primaire', '1 AC', '2 AC', '3 AC', 'Tronc commun', '1 Bac', '2 Bac', 'autre']);
const options = ref(['SP', 'SVT', 'SMA', 'SMB', 'SM', 'S. ECO', 'S. EXP', 'Science', 'autre']);

const studentsStore = useStudentsStore();
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
    parent_phone: '',
    field: '',
    level: '',
    school: '',
    user_id: '',
})
const errors = ref({})

const Create = () => {
    if (isLoading.value == false) {
        isLoading.value = true
        data.value.user_id = authStore?.user?.id
        errors.value = {}
        studentsStore.store(data.value).then(res => {
            isLoading.value = false
            useAlert('success', 'Créé avec succès!');
            props.close()
        }).catch((err) => {
            isLoading.value = false
            if(err.status == 422) {
                errors.value =  err.response.data.errors;
                useAlert('warning', "quelque chose s'est mal passé!");
            } else if(err.status == 400) {
                useAlert('warning', "L'élèves existe déjà!");
            } else useAlert('warning', "quelque chose s'est mal passé!");
        });
    }
}
</script>