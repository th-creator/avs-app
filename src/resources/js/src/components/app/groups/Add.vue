<template>
    <div>
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
                                <input v-model="data.intitule" type="text" placeholder="Intitule" class="form-input" />
                                <span v-if="errors.intitule" class="text-red-600 text-sm">{{ errors.intitule[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <input v-model="data.n_place" type="number" placeholder="Nombre de places" class="form-input" />
                                <span v-if="errors.n_place" class="text-red-600 text-sm">{{ errors.n_place[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <multiselect
                                    v-model="data.availability"
                                    :options="options"
                                    class="custom-multiselect"
                                    :searchable="false"
                                    :preselect-first="true"
                                    :allow-empty="false"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.availability" class="text-red-600 text-sm">{{ errors.availability[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <input v-model="data.salle" type="text" placeholder="Salle" class="form-input" />
                                <span v-if="errors.salle" class="text-red-600 text-sm">{{ errors.salle[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <multiselect
                                    v-model="data.teacher"
                                    :options="teachers"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="Enseignant"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.teacher_id" class="text-red-600 text-sm">{{ errors.teacher_id[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <multiselect
                                    v-model="data.section"
                                    :options="sections"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="Section"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.section_id" class="text-red-600 text-sm">{{ errors.section_id[0] }}</span>
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
import { ref, defineProps, computed, onMounted  } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { useTeachersStore } from '@/stores/teachers.js';
import { useGroupsStore } from '@/stores/groups.js';
import { useSectionsStore } from '@/stores/sections.js';
import { useAlert } from '@/composables/useAlert';
import {useAuthStore} from '@/stores/auth.js';
import Multiselect from '@suadelabs/vue3-multiselect';
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';


const options = ref(['active', 'inactive']);
const sections = computed(() => {
        return sectionsStore.sections.length > 0 ? sectionsStore.sections.map(res => res.id + ' : ' + res.level + ' / '+ res.subject) : [];
        });

const teachers = computed(() => {
        return teachersStore.teachers.length > 0 ? teachersStore.teachers.map(res => res.id + ' : ' + res.firstName + ' ' + res.lastName) : [];
        });

const teachersStore = useTeachersStore();
const groupsStore = useGroupsStore();
const sectionsStore = useSectionsStore();
const authStore = useAuthStore();
onMounted(() => {
    sectionsStore.index()
    teachersStore.index()
    })
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
    intitule: '',
    n_place: '',
    availability: 'active',
    salle: '',
    timing: [{
        day: "monday",
        dates: ["du 10 a 12","du 16 a 18"],
      }],
    teacher: '',
    teacher_id: '',
    section: '',
    section_id: '',
    user_id: '',
})
const errors = ref({})

const Create = () => {
    errors.value = []
    data.value.user_id = authStore?.user?.id
    data.value.teacher_id = data.value.teacher.split(':')[0].trim()
    data.value.section_id = data.value.section.split(':')[0].trim()
    
    groupsStore.store(data.value).then(res => {
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