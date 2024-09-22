<template>
    <div>
        <TransitionRoot appear :show="showEditPopup" as="template">
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
                    <div class="text-lg text-center font-semibold ltr:pl-5 rtl:pr-5 py-5 ltr:pr-[50px] rtl:pl-[50px]">Modifier</div>
                    <div class="p-5">
                        <form>
                            <div class="relative mb-4">
                                <label class="text-sm">Matière:</label>
                                <input v-model="data.subject" type="text" placeholder="Matière" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.subject" class="text-red-600 text-sm">{{ errors.subject[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Niveau:</label>
                                <input v-model="data.level" type="text" placeholder="Niveau" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.level" class="text-red-600 text-sm">{{ errors.level[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Prix:</label>
                                <input v-model="data.price" type="text" placeholder="Prix" class="form-input ltr:pl-10 rtl:pr-10" />
                                <span v-if="errors.price" class="text-red-600 text-sm">{{ errors.price[0] }}</span>
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
import { useSectionsStore } from '@/stores/sections.js';
import { useAlert } from '@/composables/useAlert';

const sectionsStore = useSectionsStore();

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
    subject: props.editedData.subject,
    level: props.editedData.level,
    price: props.editedData.price,
})

const errors = ref({})

const Edit = () => {
    sectionsStore.update(data.value,props.editedData.id).then(res => {
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