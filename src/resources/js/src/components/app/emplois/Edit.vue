<template>
    <div>
        <TransitionRoot appear :show="showEditPopup" as="template">
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

                                <div class="text-lg text-center font-semibold py-5">
                                    Modifier Emploi
                                </div>

                                <div class="p-5">
                                    <form>

                                        <!-- GROUP NAME (READ ONLY) -->
                                        <div class="relative mb-4">
                                            <label class="text-sm">Groupe:</label>
                                            <input type="text" class="form-input" :value="editedData.group" disabled />
                                        </div>

                                        <!-- SALLE -->
                                        <div class="relative mb-4">
                                            <label class="text-sm">Salle:</label>
                                            <input v-model="data.salle" type="text" class="form-input" />
                                            <span v-if="errors.salle" class="text-red-600 text-sm">{{ errors.salle[0] }}</span>
                                        </div>

                                        <!-- TYPE -->
                                        <div class="relative mb-4">
                                            <label class="text-sm">Type:</label>
                                            <input v-model="data.type" type="text" class="form-input" readonly />
                                        </div>

                                        <!-- PERIOD DATES -->
                                        <div v-if="data.type === 'period'" class="relative mb-4">
                                            <label class="text-sm">Période:</label>
                                            <div class="flex justify-between gap-3 mt-2">
                                                <input type="date" v-model="data.from" class="form-input w-[48%]" />
                                                <input type="date" v-model="data.to" class="form-input w-[48%]" />
                                            </div>

                                            <span v-if="errors.from" class="text-red-600 text-sm">{{ errors.from[0] }}</span>
                                            <span v-if="errors.to" class="text-red-600 text-sm">{{ errors.to[0] }}</span>
                                        </div>

                                        <!-- TIMING -->
                                        <div class="relative mb-4">
                                            <label class="text-sm">Temps:</label>

                                            <multiselect
                                                v-model="data.day"
                                                :options="days"
                                                class="custom-multiselect"
                                                :searchable="false"
                                                placeholder="Jour"
                                                selected-label=""
                                                deselect-label=""
                                                select-label=""
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
                                                <span class="border px-3 py-[6px] rounded cursor-pointer" @click="attachTiming">+</span>
                                            </div>

                                            <div class="mt-2">
                                                <div 
                                                    v-for="t in data.timing"
                                                    :key="t.day + t.dates"
                                                    class="flex justify-around items-center gap-2 mt-2"
                                                    @click="detachTiming(t)"
                                                >
                                                    <p class="cursor-pointer font-semibold border rounded-3xl px-4 py-1 bg-white-dark">
                                                        {{ t.day + ' : ' + t.dates }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SUBMIT -->
                                        <button type="button" class="btn btn-primary w-full h-10" @click="Edit">
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
import { ref, defineProps } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import Multiselect from '@suadelabs/vue3-multiselect';
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

import { useEmploiStore } from '@/stores/emploi.js';
import { useAlert } from '@/composables/useAlert';
import IconComponent from '@/components/icons/IconComponent.vue';

const emploiStore = useEmploiStore();

const props = defineProps({
    showEditPopup: Boolean,
    editedData: Object,
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
    salle: props.editedData.salle,
    type: props.editedData.type,
    from: props.editedData.from,
    to: props.editedData.to,
    timing: JSON.parse(JSON.stringify(props.editedData.timing)),
    day: "",
    from_time: "",
    to_time: "",
});

// add timing
const attachTiming = () => {
    if (!data.value.day || !data.value.from_time || !data.value.to_time) return;
    data.value.timing.push({
        day: data.value.day,
        dates: [`${data.value.from_time} - ${data.value.to_time}`]
    });
    data.value.day = "";
    data.value.from_time = "";
    data.value.to_time = "";
};

// remove timing
const detachTiming = (t) => {
    data.value.timing = data.value.timing.filter(item => item !== t);
};

// submit
const Edit = async () => {
    isLoading.value = true;
    errors.value = {};

    const payload = {
        salle: data.value.salle,
        type: data.value.type,
        from: data.value.type === "period" ? data.value.from : null,
        to: data.value.type === "period" ? data.value.to : null,
        timing: data.value.timing,
    };

    emploiStore.update(payload, props.editedData.id)
        .then(() => {
            isLoading.value = false;
            useAlert("success", "Modifié avec succès!");
            props.close();
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
