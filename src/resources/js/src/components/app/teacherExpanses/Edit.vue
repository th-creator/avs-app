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
                    <DialogPanel class="panel border-0 px-4 py-1 rounded-lg overflow-hidden w-full max-w-lg text-black dark:text-white-dar">
                    <button type="button" class="absolute top-7 ltr:right-9 rtl:left-9 text-white-dark hover:text-dark outline-none" @click="close()">
                        X
                    </button>
                    <div class="text-lg text-center font-semibold ltr:pl-5 rtl:pr-5 py-5 ltr:pr-[50px] rtl:pl-[50px]">Modifier</div>
                    <div class="p-5">
                        <form>
                            <div class="relative mb-4 flex gap-4">
                                <div class="w-[70%]">
                                    <label class="text-sm">Montant total:</label>
                                    <input v-model="data.total" type="number" placeholder="Montant total" class="form-input" />
                                    <span v-if="errors.total" class="text-red-600 text-sm">{{ errors.total[0] }}</span>
                                </div>
                                <div class="w-[30%]">
                                    <label class="text-sm">Pourcentage:</label>
                                    <input v-model="data.percentage" type="number" placeholder="Pourcentage" class="form-input" />
                                    <span v-if="errors.percentage" class="text-red-600 text-sm">{{ errors.percentage[0] }}</span>
                                </div>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Montant à payer:</label>
                                <input v-model="data.amount" type="number" placeholder="Montant à payer" class="form-input" />
                                <span v-if="errors.amount" class="text-red-600 text-sm">{{ errors.amount[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Reste:</label>
                                <input v-model="data.rest" type="number" placeholder="Reste" class="form-input" />
                                <span v-if="errors.rest" class="text-red-600 text-sm">{{ errors.rest[0] }}</span>
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
                                <label class="text-sm">Date:</label>
                                <input v-model="data.date" type="date" placeholder="Date d'inscription" class="form-input" />
                                <span v-if="errors.date" class="text-red-600 text-sm">{{ errors.date[0] }}</span>
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
    </div>
</template>

<script setup>
import { ref, defineProps } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { useTeacherExpansesStore } from '@/stores/teacherExpanse.js';
import { useAlert } from '@/composables/useAlert';
import Multiselect from '@suadelabs/vue3-multiselect';
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';
import IconComponent from '@/components/icons/IconComponent.vue'

const isLoading = ref(false)

const months = ref(['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre', 'Octobre','Novembre','Décembre']);



const teacherExpansesStore = useTeacherExpansesStore();

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
    percentage: props.editedData.percentage,
    rest: props.editedData.rest,
    total: props.editedData.total,
    month: props.editedData.month,
    year: props.editedData.year,
})

const errors = ref({})

const Edit = () => {
    isLoading.value = true
    teacherExpansesStore.update(data.value,props.editedData.id).then(res => {
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
</script>