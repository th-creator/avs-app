<template>
    <div class="flex justify-between my-4">    
        <div class="flex gap-10 items-end">
            <label for="">Du:</label>
            <input v-model="search.from" type="date" class="form-input max-w-xs" placeholder="Du..." />
            <label for="">A:</label>
            <input v-model="search.to" type="date" class="form-input max-w-xs" placeholder="A..." />
        </div>
        <button type="button" class="btn btn-info w-36" @click="searchPayments">
            <IconComponent v-if="isloading" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" name="loading" />
            <span v-else>Chercher</span>
        </button>
    </div>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="grid gap-6 xl:grid-flow-row">
            <!-- Previous Statement -->
            <div class="panel overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-lg font-bold">Paiements</div>
                    </div>
                    <!-- <div class="dropdown">
                        <Popper :placement="store.rtlClass === 'rtl' ? 'bottom-start' : 'bottom-end'" offsetDistance="0" class="align-middle">
                            <button type="button">
                                <svg
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5 hover:opacity-80 opacity-70"
                                >
                                    <circle cx="5" cy="12" r="2" stroke="currentColor" stroke-width="1.5" />
                                    <circle opacity="0.5" cx="12" cy="12" r="2" stroke="currentColor" stroke-width="1.5" />
                                    <circle cx="19" cy="12" r="2" stroke="currentColor" stroke-width="1.5" />
                                </svg>
                            </button>
                            <template #content="{ close }">
                                <ul @click="close()">
                                    <li>
                                        <a href="javascript:;">View Report</a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">Edit Report</a>
                                    </li>
                                </ul>
                            </template>
                        </Popper>
                    </div> -->
                </div>
                <div class="relative mt-10">
                    <div class="absolute -bottom-12 ltr:-right-12 rtl:-left-12 w-24 h-24">
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            class="text-success opacity-20 w-full h-full"
                        >
                            <circle opacity="0.5" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" />
                            <path
                                d="M8.5 12.5L10.5 14.5L15.5 9.5"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div>
                            <div class="text-primary">espèces</div>
                            <div class="mt-2 font-semibold text-2xl">{{payments.cash}}</div>
                        </div>
                        <div>
                            <div class="text-primary">chèque</div>
                            <div class="mt-2 font-semibold text-2xl">{{payments.bank}}</div>
                        </div>
                        <div>
                            <div class="text-primary">virement</div>
                            <div class="mt-2 font-semibold text-2xl">{{payments.virement}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Statement -->
            <div class="panel overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-lg font-bold">Les dépenses des enseignants</div>
                    </div>
                </div>
                <div class="relative mt-10">
                    <div class="absolute -bottom-12 ltr:-right-12 rtl:-left-12 w-24 h-24">
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            class="text-danger opacity-20 w-24 h-full"
                        >
                            <circle opacity="0.5" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" />
                            <path d="M12 7V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <circle cx="12" cy="16" r="1" fill="currentColor" />
                        </svg>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div>
                            <div class="text-primary">Montant total</div>
                            <div class="mt-2 font-semibold text-2xl">{{teacherExpans.total}}</div>
                        </div>
                        <div>
                            <div class="text-primary">Montant payé</div>
                            <div class="mt-2 font-semibold text-2xl">{{teacherExpans.amount}}</div>
                        </div>
                        <div>
                            <div class="text-primary">Reste</div>
                            <div class="mt-2 font-semibold text-2xl">{{teacherExpans.rest}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Statement -->
            <div class="panel overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-lg font-bold">les dépenses générales</div>
                    </div>
                </div>
                <div class="relative mt-10">
                    <div class="absolute -bottom-12 ltr:-right-12 rtl:-left-12 w-24 h-24">
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            class="text-danger opacity-20 w-24 h-full"
                        >
                            <circle opacity="0.5" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" />
                            <path d="M12 7V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <circle cx="12" cy="16" r="1" fill="currentColor" />
                        </svg>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div>
                            <div class="text-primary">espèces</div>
                            <div class="mt-2 font-semibold text-2xl">{{expanses.cash}}</div>
                        </div>
                        <div>
                            <div class="text-primary">chèque</div>
                            <div class="mt-2 font-semibold text-2xl">{{expanses.bank}}</div>
                        </div>
                        <div>
                            <div class="text-primary">virement</div>
                            <div class="mt-2 font-semibold text-2xl">{{expanses.virement}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="panel">
            <div class="mb-5 text-lg font-bold">Transactions Récents</div>
            <div class="table-responsive">
                
                <div class="panel h-full p-0 border-0 overflow-hidden">
                    <div class="p-6 bg-gradient-to-r from-[#4361ee] to-[#160f6b] min-h-[190px]">
                        <div class="flex justify-between items-center mb-6">
                            <div class="bg-black/50 rounded-full p-1 ltr:p-3 rtl:p-3 flex items-center text-white font-semibold">
                                
                                AVS
                            </div>
                        </div>
                        <div class="text-white flex justify-between items-center">
                            <p class="text-xl">Solde</p>
                            <h5 class="ltr:ml-auto rtl:mr-auto text-2xl"><span class="text-white-light"></span>{{payments.cash+payments.bank+payments.virement-teacherExpans.amount-expanses.cash-expanses.bank-expanses.virement}}</h5>
                        </div>
                    </div>
                    <div class="-mt-12 px-8 grid grid-cols-2 gap-2">
                        <div class="bg-white rounded-md shadow px-4 py-2.5 dark:bg-[#060818]">
                            <span class="flex justify-between items-center mb-4 dark:text-white"
                                >Reçu
                                <svg class="w-4 h-4 text-success" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 15L12 9L5 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div class="btn w-full py-1 text-base shadow-none border-0 bg-[#ebedf2] dark:bg-black text-[#515365] dark:text-[#bfc9d4]">
                                {{payments.cash+payments.bank+payments.virement}}
                            </div>
                        </div>
                        <div class="bg-white rounded-md shadow px-4 py-2.5 dark:bg-[#060818]">
                            <span class="flex justify-between items-center mb-4 dark:text-white"
                                >Dépensé
                                <svg class="w-4 h-4 text-danger" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 9L12 15L5 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div class="btn w-full py-1 text-base shadow-none border-0 bg-[#ebedf2] dark:bg-black text-[#515365] dark:text-[#bfc9d4]">
                                {{teacherExpans.amount+expanses.cash+expanses.bank+expanses.virement}}
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <!-- <div class="mb-5">
                            <span
                                class="bg-[#1b2e4b] text-white text-xs rounded-full px-4 py-1.5 before:bg-white before:w-1.5 before:h-1.5 before:rounded-full ltr:before:mr-2 rtl:before:ml-2 before:inline-block"
                                >Pending</span
                            >
                        </div>
                        <div class="mb-5 space-y-1">
                            <div class="flex items-center justify-between">
                                <p class="text-[#515365] font-semibold">Netflix</p>
                                <p class="text-base"><span>$</span> <span class="font-semibold">13.85</span></p>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-[#515365] font-semibold">BlueHost VPN</p>
                                <p class="text-base"><span>$</span> <span class="font-semibold">15.66</span></p>
                            </div>
                        </div>
                        <div class="text-center px-2 flex justify-around">
                            <button type="button" class="btn btn-secondary ltr:mr-2 rtl:ml-2">View Details</button>
                            <button type="button" class="btn btn-success">Pay Now $29.51</button>
                        </div> -->
                    </div>
                </div>
            <!-- <div class="datatable">
                <vue3-datatable
                    :rows="rows"
                    :columns="cols"
                    :totalRows="rows?.length"
                    :sortable="true"
                    :search="params.search"
                    :loading="isloading"
                    :pageSize="params.pagesize"
                    :showPageSize="false"
                    :selectRowOnClick="true"
                    :paginationInfo="'{0} à {1} de {2}'"
                    skin="whitespace-nowrap bh-table-hover"
                    firstArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    lastArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg> '
                    previousArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    nextArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                >
                    <template #amount="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.amount }}MAD</p>
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
                    <template #paid="data">
                        <div class="flex justify-center w-full">
                            <div v-if="data.value.paid == 1 && data.value.total == data.value.amount_paid">
                                <div class="px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 w-[120px] text-center text-sm">
                                    Payé
                                </div>
                            </div>
                            <div v-else-if="data.value.paid == 1">
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
                </vue3-datatable>
            </div> -->
            </div>
        </div>
    </div>
</template>


<script setup>
    import { ref, computed, onMounted, reactive } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import { useAppStore } from '@/stores/index';
    import { usePaymentsStore } from '@/stores/payments.js';
    import { useFeesStore } from '@/stores/fees.js';
    import { useTeacherExpansesStore } from '@/stores/teacherExpanse.js';
    import { useExpansesStore } from '@/stores/expanses.js';
    import IconComponent from '@/components/icons/IconComponent.vue'


    const store = useAppStore();
    const paymentsStore = usePaymentsStore();
    const feesStore = useFeesStore();
    const teacherExpanseStore = useTeacherExpansesStore();
    const expansesStore = useExpansesStore();

    const search = ref({
        from: '',
        to: ''
    })
    const payments = ref({
        cash: 0,
        bank: 0,
        virement: 0
    })
    const expanses = ref({
        cash: 0,
        bank: 0,
        virement: 0
    })
    const teacherExpans = ref({
        total: 0,
        amount: 0,
        rest: 0
    })
    const isloading = ref(false);
    const params = reactive({
        current_page: 1,
        search: '',
        pagesize: 6,
        sort_column: 'id',
        sort_direction: 'asc',
    });
    const cols =
        ref([
            // { field: 'id', title: 'ID', isUnique: true, headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'fullName', title: 'Nom', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'paid', title: 'Etat', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'total', title: "montant", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'rest', title: "Reste", headerClass: '!text-center flex justify-center', width: 'full' },
            // { field: 'user_id', title: "Auteur", headerClass: '!text-center flex justify-center', width: 'full' },
            // { field: 'actions', title: 'Actions', headerClass: '!text-center flex justify-center', width: 'full' },
        ]) || [];
    const rows = ref([]);
    const searchPayments = async () => {
        payments.value = {
            cash: 0,
            bank: 0,
            virement: 0
        }
        expanses.value = {
            cash: 0,
            bank: 0,
            virement: 0
        }
        teacherExpans.value = {
            total: 0,
            amount: 0,
            rest: 0
        }
        isloading.value = true
        await paymentsStore.fetchFinance(search.value)
        await feesStore.fetchFinance(search.value)
        await teacherExpanseStore.fetchFinance(search.value)
        await expansesStore.fetchFinance(search.value)
        paymentsStore.financePayments.forEach(res => {
            switch (res.type) {
                case 'espèces':
                    payments.value.cash += Number(res.amount_paid);
                    break;
                case 'chèque':
                    payments.value.bank += Number(res.amount_paid);
                    break;
                case 'virement':
                    payments.value.virement += Number(res.amount_paid);
                    break;
            }
        });
        feesStore.financeFees.forEach(res => {
            switch (res.type) {
                case 'espèces':
                    payments.value.cash += Number(res.amount_paid);
                    break;
                case 'chèque':
                    payments.value.bank += Number(res.amount_paid);
                    break;
                case 'virement':
                    payments.value.virement += Number(res.amount_paid);
                    break;
            }
        });
        expansesStore.financeExpanses.forEach(res => {
            switch (res.type) {
                case 'espèces':
                    expanses.value.cash += Number(res.amount);
                    break;
                case 'chèque':
                    expanses.value.bank += Number(res.amount);
                    break;
                case 'virement':
                    expanses.value.virement += Number(res.amount);
                    break;
            }
        });
        teacherExpanseStore.financeteachersExpanses.forEach(res => {
            teacherExpans.value.total += Number(res.total);
            teacherExpans.value.amount += Number(res.amount);
            teacherExpans.value.rest += Number(res.rest);
        });
        // rows.value = paymentsStore.financePayments
        isloading.value = false
    }
    
</script>
