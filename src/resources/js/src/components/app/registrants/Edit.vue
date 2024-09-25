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
                                <label class="text-sm">Elève:</label>
                                <multiselect
                                    v-model="data.student"
                                    :options="students"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="Elève"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.student_id" class="text-red-600 text-sm">{{ errors.student_id[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Groupe:</label>
                                <multiselect
                                    v-model="data.group"
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
                            <div class="relative mb-4">
                                <label class="text-sm">Centre:</label> 
                                <multiselect
                                    v-model="data.center"
                                    :options="options"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="Centre"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.center" class="text-red-600 text-sm">{{ errors.center[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Date:</label>
                                <input v-model="data.date" type="date" placeholder="Date d'inscription" class="form-input" />
                                <span v-if="errors.date" class="text-red-600 text-sm">{{ errors.date[0] }}</span>
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
import { ref, defineProps, computed, onMounted  } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { useStudentsStore } from '@/stores/students.js';
import { useGroupsStore } from '@/stores/groups.js';
import { useRegistrantsStore } from '@/stores/registrants.js';
import { useAlert } from '@/composables/useAlert';
import Multiselect from '@suadelabs/vue3-multiselect';
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

const options = ref(['AVS', 'ISFPT']);
const registrantsStore = useRegistrantsStore();
const studentsStore = useStudentsStore();
const groupsStore = useGroupsStore();

const students = computed(() => {
        return studentsStore.students.length > 0 ? studentsStore.students.map(res => {
            if(data.value.student == res.id) {
            data.value.student = res.id + ' : ' + res.firstName + ' '+ res.lastName;
            return res.id + ' : ' + res.firstName + ' '+ res.lastName
        } else return res.id + ' : ' + res.firstName + ' '+ res.lastName}) : [];
        });

const groups = computed(() => {
        return groupsStore.groups.length > 0 ? groupsStore.groups.map(res => {
            if(data.value.group == res.id) {
            data.value.group = res.id + ' : ' + res.intitule;
            return res.id + ' : ' + res.intitule
        } else return res.id + ' : ' + res.intitule}) : [];
        });

onMounted(() => {
    studentsStore.index()
    groupsStore.index()
})
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
    center: props.editedData.center,
    date: props.editedData.date,
    group: props.editedData.group_id,
    group_id: props.editedData.group_id,
    student: props.editedData.student_id,
    student_id: props.editedData.student_id,
})

const errors = ref({})

const Edit = () => {
    registrantsStore.update(data.value,props.editedData.id).then(res => {
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