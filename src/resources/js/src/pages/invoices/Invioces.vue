<template>
    <div class="panel  my-6">
        <TabGroup as="div" class="mb-5">
    <TabList class="flex flex-wrap mt-3 border-b border-white-light dark:border-[#191e3a]">
        <Tab as="template" v-slot="{ selected }">
            <a
                href="javascript:;"
                class="p-3.5 py-2 -mb-[1px] flex items-center border border-transparent hover:text-info !outline-none transition duration-300"
                :class="{ '!border-white-light !border-b-white text-info dark:!border-[#191e3a] dark:!border-b-black': selected }"
            >
                Paiements
            </a>
        </Tab>
        <Tab as="template" v-slot="{ selected }">
            <a
                href="javascript:;"
                class="p-3.5 py-2 -mb-[1px] flex items-center border border-transparent hover:text-info !outline-none transition duration-300"
                :class="{ '!border-white-light !border-b-white text-info dark:!border-[#191e3a] dark:!border-b-black': selected }"
            >
                Registre
            </a>
        </Tab>
        <Tab as="template" v-slot="{ selected }">
            <a
                href="javascript:;"
                class="p-3.5 py-2 -mb-[1px] flex items-center border border-transparent hover:text-info !outline-none transition duration-300"
                :class="{ '!border-white-light !border-b-white text-info dark:!border-[#191e3a] dark:!border-b-black': selected }"
            >
                Groupes
            </a>
        </Tab>
        <Tab as="template" v-slot="{ selected }">
            <a
                href="javascript:;"
                class="p-3.5 py-2 -mb-[1px] flex items-center border border-transparent hover:text-info !outline-none transition duration-300"
                :class="{ '!border-white-light !border-b-white text-info dark:!border-[#191e3a] dark:!border-b-black': selected }"
            >
                Frais d'inscriptions
            </a>
        </Tab>
        <Tab as="template" v-slot="{ selected }">
            <a
                href="javascript:;"
                class="p-3.5 py-2 -mb-[1px] flex items-center border border-transparent hover:text-info !outline-none transition duration-300"
                :class="{ '!border-white-light !border-b-white text-info dark:!border-[#191e3a] dark:!border-b-black': selected }"
            >
                Chèques
            </a>
        </Tab>
    </TabList>
    <TabPanels class="p-4 flex-1 text-sm border border-white-light border-t-0 dark:border-[#191e3a]">
         <TabPanel>
            <paymentTable />
        </TabPanel>
       <TabPanel>
            <ReceiptTable />
        </TabPanel>
        <TabPanel class="active">
            <groupTable />
        </TabPanel>
        <TabPanel>
            <feesTable />
        </TabPanel>
        <TabPanel>
            <checksTable />
        </TabPanel>
    </TabPanels>
</TabGroup>
        
    </div>
</template>
<script setup>
    import paymentTable from '@/components/app/invoices/payments/Table.vue';
    import groupTable from '@/components/app/invoices/groups/Table.vue';
    import feesTable from '@/components/app/invoices/fees/Table.vue';
    import checksTable from '@/components/app/invoices/checks/Table.vue';
    import ReceiptTable from '@/components/app/invoices/receipt/Table.vue';
    import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
</script>