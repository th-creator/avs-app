<template>
    <div>
        <div class="panel pb-0 mt-6">
            <h5 class="font-semibold text-lg dark:text-white-light mb-5">Les frais d'inscriptions à régler</h5>
            <div class="flex justify-between my-4">    
                <input v-model="params.search" type="text" class="form-input max-w-xs" placeholder="Rechercher..." />
            </div>
            <div class="datatable">
                <vue3-datatable
                    :rows="feesStore.fees"
                    :columns="cols"
                    :totalRows="rows?.length"
                    :sortable="true"
                    :search="params.search"
                    :loading="isloading"
                    :sortColumn="params.sort_column"
                    :sortDirection="params.sort_direction"
                    :paginationInfo="'{0} à {1} de {2}'"
                    skin="whitespace-nowrap bh-table-hover"
                    firstArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    lastArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg> '
                    previousArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    nextArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                >
                    <template #fullName="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.fullName }}</p>
                        </div>
                    </template>
                    <template #amount="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.amount }}MAD</p>
                        </div>
                    </template>
                    <template #rest="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.rest }}MAD</p>
                        </div>
                    </template>
                    <template #total="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.total }}MAD</p>
                        </div>
                    </template>
                    <template #amount_paid="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.amount_paid }}MAD</p>
                        </div>
                    </template>
                    <template #reduction="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.reduction }}%</p>
                        </div>
                    </template>
                    <template #date="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.date }}</p>
                        </div>
                    </template>
                    <template #paid="data">
                        <div class="flex justify-center w-full">
                            <div v-if="(data.value.total > 0 && data.value.total == data.value.amount_paid) || data.value.reduction == 100">
                                <div class="px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 w-[120px] text-center text-sm">
                                    Payé
                                </div>
                            </div>
                            <div v-else-if="data.value.total > 0">
                                <div class="px-4 py-2 rounded-full bg-orange-100 text-orange-600 w-[120px] text-center text-sm">
                                    En cours
                                </div>
                            </div>
                            <div v-else-if="data.value.paid == -1">
                                <div class="px-4 py-2 rounded-full bg-blue-100 text-blue-600 w-[120px] text-center text-sm">
                                    Remboursé
                                </div>
                            </div>
                            <div v-else>
                                <div class="px-4 py-2 rounded-full bg-rose-100 text-rose-600 w-[120px] text-center text-sm">
                                    Non payé
                                </div>
                            </div>
                        </div>
                    </template>
                    <template #bank="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.bank }}</p>
                        </div>
                    </template>
                    <template #bank_receipt="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.bank_receipt }}</p>
                        </div>
                    </template>
                    <template #receipt="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.receipt }}</p>
                        </div>
                    </template>
                    <template #actions="data">
                        <div class="flex w-fit mx-auto justify-around gap-5">
                            <router-link :to="`/students/${data.value.student_id}/payments`" class="main-logo flex items-center shrink-0">
                                <IconComponent name="view" />
                            </router-link>
                        </div>
                    </template>
                </vue3-datatable>
            </div>
        </div>
    </div>
</template>
<script setup>
    import { ref, reactive, computed, onMounted } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import { useFeesStore } from '@/stores/fees.js';
    import IconComponent from '@/components/icons/IconComponent.vue'
    
    const params = reactive({
        current_page: 1,
        search: '',
        pagesize: 10,
        sort_column: 'id',
        sort_direction: 'asc',
    });
    const isloading = ref(false);

    const feesStore = useFeesStore();
    
    const cols =
        ref([
            // { field: 'id', title: 'ID', isUnique: true, headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'fullName', title: 'Nom', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'amount', title: 'Montant', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'total', title: "montant à payer", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'amount_paid', title: "montant reçu", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'rest', title: "Reste", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'paid', title: 'Etat', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'reduction', title: "Reduction", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'type', title: "Type", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'bank', title: "Bank", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'bank_receipt', title: "Chèque", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'receipt', title: "Recu", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'date', title: "Date", headerClass: '!text-center flex justify-center', width: 'full' },
            // { field: 'user_id', title: "Auteur", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'actions', title: 'Actions', headerClass: '!text-center flex justify-center', width: 'full' },
        ]) || [];

    const rows = computed(async() => {
        let data = await feesStore.fees.length > 0 ? feesStore.fees : []
        return data;
        });


    onMounted(async () => {
        feesStore.fees.length == 0 && (isloading.value = true)
        await feesStore.undandledFess()
        isloading.value = false
    })
</script>
