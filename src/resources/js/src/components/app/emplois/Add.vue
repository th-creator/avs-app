<template>
    <div>
        <TransitionRoot appear :show="showPopup" as="template">
            <Dialog as="div" class="relative z-50">
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
                                
                                <!-- CLOSE BUTTON -->
                                <button type="button" 
                                    class="absolute top-7 ltr:right-9 rtl:left-9 text-white-dark hover:text-dark" 
                                    @click="close()">
                                    X
                                </button>

                                <!-- TITLE -->
                                <div class="text-lg text-center font-semibold py-5">
                                    Ajouter un Emploi
                                </div>

                                <div class="p-5">

                                    <form>
                                        <!-- GROUP SELECTION -->
                                        
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

                                        <!-- SALLE -->
                                        <div class="relative mb-4">
                                            <label class="text-sm">Salle:</label>
                                            <input v-model="data.salle" type="text" class="form-input" placeholder="Salle" />
                                            <span v-if="errors.salle" class="text-red-600 text-sm">{{ errors.salle[0] }}</span>
                                        </div>

                                        <!-- TIMING (SAME LOGIC AS GROUP ADD) -->
                                        <div class="relative mb-4">
                                            <label class="text-sm">Temps:</label>

                                            <multiselect
                                                v-model="data.day"
                                                :options="days"
                                                class="custom-multiselect"
                                                :searchable="false"
                                                placeholder="Jour"
                                                selected-label=""
                                                select-label=""
                                                deselect-label=""
                                            ></multiselect>

                                            <div class="mt-4 flex justify-between items-center">
                                                    <div class="flex w-[42%] items-center">
                                                        <multiselect
                                                            v-model="data.from_time"
                                                            :options="timeOptions"
                                                            placeholder="Du"
                                                            class="custom-multiselect w-full"
                                                            :searchable="false"
                                                            selected-label=""
                                                            select-label=""
                                                            deselect-label=""
                                                        ></multiselect>

                                                    </div>

                                                    <div class="flex w-[42%] items-center">
                                                        <multiselect
                                                            v-model="data.to_time"
                                                            :options="timeOptions"
                                                            placeholder="À"
                                                            class="custom-multiselect w-full"
                                                            :searchable="false"
                                                            selected-label=""
                                                            select-label=""
                                                            deselect-label=""
                                                        ></multiselect>
                                                    </div>
                                                <span @click="attachTiming" class="border px-3 py-[6px] rounded cursor-pointer">+</span>
                                            </div>

                                            <!-- SHOW ATTACHED TIMING -->
                                            <div class="mt-2">
                                                <div 
                                                    class="flex justify-around items-center gap-2 mt-2"
                                                    v-for="t in data.timing"
                                                    @click="detachTiming(t)"
                                                >
                                                    <p class="cursor-pointer font-semibold text-center border rounded-3xl px-4 py-1 bg-white-dark">
                                                        {{ t.day + ' : ' + t.dates }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SUBMIT BUTTON -->
                                        <button type="button" class="btn btn-primary w-full h-10" @click="Create">
                                            <IconComponent v-if="isLoading" name="loading" class="absolute left-1/2 top-1/2" />
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
import { ref, onMounted, defineProps, computed } from "vue";
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from "@headlessui/vue";
import Multiselect from "@suadelabs/vue3-multiselect";
import "@suadelabs/vue3-multiselect/dist/vue3-multiselect.css";

import { useEmploiStore } from "@/stores/emploi.js";
import { useGroupsStore } from "@/stores/groups.js";
import { useAlert } from "@/composables/useAlert";

const emploisStore = useEmploiStore();
const groupsStore = useGroupsStore();

const props = defineProps({
    showPopup: Boolean,
    close: Function,
});

const isLoading = ref(false);
const errors = ref({});

const days = ref(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche']);
const timeOptions = [
  "08:00","08:30",
  "09:00","09:30",
  "10:00","10:30",
  "11:00","11:30",
  "12:00","12:30",
  "13:00","13:30",
  "14:00","14:30",
  "15:00","15:30",
  "16:00","16:30",
  "17:00","17:30",
  "18:00","18:30",
  "19:00","19:30",
  "20:00","20:30",
  "21:00","21:30",
  "22:00","22:30",
  "23:00","23:30"
];

const data = ref({
    group: '',
    group_id: '',
    niveau: "",
    filiere: "",
    salle: "",
    type: "default",
    from: null,
    to: null,
    timing: [],
    day: "",
    from_time: "",
    to_time: "",
});

// GROUP OPTIONS
const groups = computed(() => {
    return groupsStore.groups.length > 0 ? groupsStore.groups.map(res => res.id + ' : ' + res.intitule) : [];
});

onMounted(() => {
    if (groupsStore.groups.length === 0) {
        groupsStore.index();
    }
});

// -------------------- TIMING LOGIC (copied from group popup) --------------------
const attachTiming = () => {
    if (data.value.day && data.value.from_time && data.value.to_time) {    
        data.value.timing.push({
            day: data.value.day,
            dates: [`${data.value.from_time} - ${data.value.to_time}`]
        });

        data.value.day = "";
        data.value.from_time = "";
        data.value.to_time = "";
    }
};

const detachTiming = (item) => {
    data.value.timing = data.value.timing.filter(t => t !== item);
};

// -------------------- CREATE EMPLOI --------------------
const Create = () => {
    if (isLoading.value) return;

    isLoading.value = true;
    errors.value = {};
    data.value.group_id = data.value.group.split(':')[0].trim()

    emploisStore.store(data.value)
        .then((res) => {
            console.log(res);
            
            if(res.status == 201) {
                isLoading.value = false;
                useAlert("success", "Créé avec succès!");
                props.close();
            }else if (err.status === 422) {
                errors.value = err.response.data.errors;
                useAlert("warning", "Les données sont incorrectes!");
            } else {
                useAlert("warning", "Une erreur s'est produite!");
            }
        })
        .catch(err => {
            isLoading.value = false;

            if (err.status === 422) {
                errors.value = err.response.data.errors;
            }

            useAlert("warning", "Une erreur s'est produite!");
        });
};
</script>
<style scoped>
    /* Timepicker input styling */
.vue__time-picker-input {
    border: 1px solid #d1d5db !important; /* gray-300 */
    padding: 7px 10px !important;
    border-radius: 8px !important;
    width: 10% !important;
    font-size: 14px !important;
    color: #111827 !important; /* gray-900 */
}

.vue__time-picker-input:focus {
    outline: none !important;
    border-color: #3b82f6 !important; /* blue-500 */
    box-shadow: 0 0 0 2px #bfdbfe !important; /* blue-200 */
}

/* DROPDOWN */
.vue__time-picker-dropdown {
    position: fixed !important;
    z-index: 999999 !important;
    background: white !important;
    border: 1px solid #e5e7eb !important; /* gray-200 */
    border-radius: 8px !important;
    padding: 6px !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
    max-height: 200px !important;
    overflow-y: auto !important;
    width: 10px !important;
}

/* OPTION */
.vue__time-picker-option {
    padding: 6px 10px !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    font-size: 14px !important;
}

.vue__time-picker-option:hover {
    background: #f3f4f6 !important; /* gray-100 */
}

.vue__time-picker-option.selected {
    background: #3b82f6 !important; /* blue-500 */
    color: white !important;
}

/* SLIM SCROLLBAR */
.vue__time-picker-dropdown::-webkit-scrollbar {
    width: 6px;
}
.vue__time-picker-dropdown::-webkit-scrollbar-track {
    background: #f3f4f6;
}
.vue__time-picker-dropdown::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

</style>