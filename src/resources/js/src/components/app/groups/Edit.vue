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
                    <DialogPanel class="panel border-0 px-4 py-1 rounded-lg overflow-hidden w-full max-w-ls text-black dark:text-white-dark">
                    <button type="button" class="absolute top-7 ltr:right-9 rtl:left-9 text-white-dark hover:text-dark outline-none" @click="close()">
                        X
                    </button>
                    <div class="text-lg text-center font-semibold ltr:pl-5 rtl:pr-5 py-5 ltr:pr-[50px] rtl:pl-[50px]">Modifier</div>
                    <div class="p-5">
                        <form>
                            <div class="relative mb-4">
                                <label class="text-sm">Enseignant:</label>
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
                                <label class="text-sm">Option:</label>
                                <multiselect
                                    v-model="data.section"
                                    :options="sections"
                                    class="custom-multiselect"
                                    :searchable="true"
                                    placeholder="Option"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <span v-if="errors.section_id" class="text-red-600 text-sm">{{ errors.section_id[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Intitule:</label>
                                <input v-model="data.intitule" type="text" placeholder="Intitule" class="form-input" />
                                <span v-if="errors.intitule" class="text-red-600 text-sm">{{ errors.intitule[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Nombre de places:</label>
                                <input v-model="data.n_place" type="number" placeholder="Nombre de places" class="form-input" />
                                <span v-if="errors.n_place" class="text-red-600 text-sm">{{ errors.n_place[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Disponibilité:</label>
                                <input v-model="data.availability" type="number" placeholder="Disponibilite" class="form-input" />
                                <span v-if="errors.availability" class="text-red-600 text-sm">{{ errors.availability[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Salle:</label>
                                <input v-model="data.salle" type="text" placeholder="Salle" class="form-input" />
                                <span v-if="errors.salle" class="text-red-600 text-sm">{{ errors.salle[0] }}</span>
                            </div>
                            <div class="relative mb-4">
                                <label class="text-sm">Temps:</label>
                                <multiselect
                                    v-model="data.day"
                                    :options="options"
                                    class="custom-multiselect [w-75%]"
                                    :searchable="false"
                                    :preselect-first="true"
                                    placeholder="Temps"
                                    :allow-empty="false"
                                    selected-label=""
                                    select-label=""
                                    deselect-label=""
                                ></multiselect>
                                <div class="mt-4 flex justify-between items-center">
                                    <input type="text" v-model="data.from" placeholder="Du" class="form-input w-[42%]" />
                                    <input type="text" v-model="data.to" placeholder="A" class="form-input w-[42%]" />
                                    <span class="border px-3 py-[6px] rounded cursor-pointer" @click="attachTiming">+</span>
                                </div>
                                <div class="mt-2">
                                    <div class="flex justify-around w-full items-center gap-2 mt-2" v-for="time in data.timing" @click="detachTiming(time)"><p class="cursor-pointer font-semibold text-center border-gray-700 border rounded-3xl px-4 py-1 bg-white-dark">{{ time.day + ' : ' + time.dates }}</p></div>
                                </div>
                                <span v-if="errors.day" class="text-red-600 text-sm">{{ errors.day[0] }}</span>
                            </div>
                            <button type="button" class="btn btn-primary w-full" @click="Edit()">Submit</button>
                        </form>
                    </div>
                    </DialogPanel>
                </TransitionChild>
                </div>
            </div>{{ editedData }}
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script setup>
import { ref, defineProps, onMounted, computed } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import { useAlert } from '@/composables/useAlert';
import { useTeachersStore } from '@/stores/teachers.js';
import { useGroupsStore } from '@/stores/groups.js';
import { useSectionsStore } from '@/stores/sections.js';
import Multiselect from '@suadelabs/vue3-multiselect';
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

const options = ref(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimenche']);
const sections = computed(() => {
        return sectionsStore.sections.length > 0 ? sectionsStore.sections.map(res => res.id + ' : ' + res.level + ' / '+ res.subject) : [];
        });

const teachers = computed(() => {
        return teachersStore.teachers.length > 0 ? teachersStore.teachers.map(res => res.id + ' : ' + res.firstName + ' ' + res.lastName) : [];
        });

const teachersStore = useTeachersStore();
const groupsStore = useGroupsStore();
const sectionsStore = useSectionsStore();
onMounted(async () => {
    await sectionsStore.index()
    await teachersStore.index()
    console.log(sections.value);
    sections.value.map((res) => {
        if(res.split(':')[0].trim()==data.value.section_id) {
            data.value.section = res
            return
        }
    })
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
    intitule: props.editedData.intitule,
    n_place: props.editedData.n_place,
    availability: props.editedData.availability,
    salle: props.editedData.salle,
    timing: JSON.parse(props.editedData.timing),
    teacher: props.editedData.teacher_id + ' : ' + props.editedData.teacher.firstName + ' ' + props.editedData.teacher.lastName,
    teacher_id: props.editedData.teacher_id,
    section: props.editedData.section_id,
    section_id: props.editedData.section_id,
})

const errors = ref([])

const attachTiming = () => {
    console.log(data.value.day,data.value.from,data.value.to);
    data.value.timing.push({
        day: data.value.day,
        dates: ["du "+data.value.from+" a "+data.value.to]
      })
      data.value.day = null
      data.value.from = null
      data.value.to = null
}
const detachTiming = (value) => {
    data.value.timing = data.value.timing.filter(res => (res.dates[0] != value.dates[0] || res.day != value.day))
}
const Edit = () => {
    errors.value = []
    data.value.teacher_id = data.value.teacher.split(':')[0].trim()
    data.value.section_id = data.value.section.split(':')[0].trim()
    groupsStore.update(data.value,props.editedData.id).then(res => {
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
