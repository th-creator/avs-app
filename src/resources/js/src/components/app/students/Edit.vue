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
                    <DialogPanel class="panel border-0 px-4 py-1 rounded-lg overflow-hidden w-full max-w-lg text-black dark:text-white-dark">
                    <button type="button" class="absolute top-7 ltr:right-9 rtl:left-9 text-white-dark hover:text-dark outline-none" @click="close()">
                        X
                    </button>
                    <div class="text-lg text-center font-semibold ltr:pl-5 rtl:pr-5 py-5 ltr:pr-[50px] rtl:pl-[50px]">Modifier</div>
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
                                <label class="text-sm">spécialité:</label>
                                <input v-model="data.field" type="text" placeholder="spécialité" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.field" class="text-red-600 text-sm">{{ errors.field[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">École:</label>
                                <input v-model="data.school" type="text" placeholder="École" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.school" class="text-red-600 text-sm">{{ errors.school[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Niveau:</label>
                                <input v-model="data.level" type="text" placeholder="Niveau" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.level" class="text-red-600 text-sm">{{ errors.level[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Mobile du parent:</label>
                                <input v-model="data.parent_phone" type="text" placeholder="Mobile du parent" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.parent_phone" class="text-red-600 text-sm">{{ errors.parent_phone[0] }}</span>
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
import { ref, defineProps, onMounted } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { useStudentsStore } from '@/stores/students.js';
import { useAlert } from '@/composables/useAlert';

const studentsStore = useStudentsStore();

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
    firstName: props.editedData.firstName,
    lastName: props.editedData.lastName,
    email: props.editedData.email,
    date: props.editedData.date,
    phone: props.editedData.phone,
    parent_phone: props.editedData.parent_phone,
    field: props.editedData.field,
    school: props.editedData.school,
    level: props.editedData.level,
})

const errors = ref({})

const Edit = () => {
    studentsStore.update(data.value,props.editedData.id).then(res => {
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