<template>
    <div>
        <TransitionRoot appear :show="showTransferPopup" as="template">
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
                    <DialogPanel class="panel border-0 px-4 py-1 rounded-lg w-full max-w-xl text-black dark:text-white-dark">
                    <button type="button" class="absolute top-7 ltr:right-9 rtl:left-9 text-white-dark hover:text-dark outline-none" @click="close()">
                        X
                    </button>
                    <div class="text-lg text-center font-semibold ltr:pl-5 rtl:pr-5 py-5 ltr:pr-[50px] rtl:pl-[50px]">Transfert</div>
                    <div class="p-5">
                        <form>
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
import { ref, defineProps, computed, onMounted  } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { useGroupsStore } from '@/stores/groups.js';
import { useRegistrantsStore } from '@/stores/registrants.js';
import { useAlert } from '@/composables/useAlert';
import Multiselect from '@suadelabs/vue3-multiselect';
import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';
import {useAuthStore} from '@/stores/auth.js';
import IconComponent from '@/components/icons/IconComponent.vue'

const authStore = useAuthStore();
const isLoading = ref(false)

const registrantsStore = useRegistrantsStore();
const groupsStore = useGroupsStore();

const groups = computed(() => {
        return groupsStore.groups.length > 0 ? groupsStore.groups.map(res => {
            if(data.value.group == res.id) {
            data.value.group = res.id + ' : ' + res.intitule;
            return res.id + ' : ' + res.intitule
        } else return res.id + ' : ' + res.intitule}) : [];
        });

onMounted(() => {
    groupsStore.index()
})
const props = defineProps({
    showTransferPopup: {
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
    group: props.editedData.group_id,
    group_id: props.editedData.group_id,
})

const errors = ref({})

const Edit = () => {
    isLoading.value = true
    data.value.group_id = data.value.group.split(':')[0].trim()
    data.value.user_id = authStore?.user?.id
    registrantsStore.transfer(data.value,props.editedData.id).then(res => {
        isLoading.value = false
        useAlert('success', 'Créé avec succès!');
        props.close()
    }).catch((err) => {
        isLoading.value = false
        if(err.status == 422) {
            errors.value =  err.response.data.errors;
            useAlert('warning', "quelque chose s'est mal passé!");
        } else if(err.status == 400) {
            useAlert('warning', "L'elève existe déjà dans ce groupe!");
        } else useAlert('warning', "quelque chose s'est mal passé!");
    });
}
</script>