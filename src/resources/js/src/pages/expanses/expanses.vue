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
                    Les dépenses générales
                    </a>
                </Tab>
                <Tab as="template" v-slot="{ selected }">
                    <a
                        href="javascript:;"
                        class="p-3.5 py-2 -mb-[1px] flex items-center border border-transparent hover:text-info !outline-none transition duration-300"
                        :class="{ '!border-white-light !border-b-white text-info dark:!border-[#191e3a] dark:!border-b-black': selected }"
                    >
                    Rémunération
                    </a>
                </Tab>
            </TabList>
        <TabPanels class="p-4 flex-1 text-sm border border-white-light border-t-0 dark:border-[#191e3a]">
            <TabPanel class="active">
                <ExpansesTable />
            </TabPanel>
            <TabPanel>
                <TaecherExpansesTable />
            </TabPanel>
        </TabPanels>
    </TabGroup>
    <!-- <div>
        <Table /> --> 
    </div>
</template>
<script setup>
    import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
    import ExpansesTable from '@/components/app/expanses/Table.vue';
    import TaecherExpansesTable from '@/components/app/teacherExpanses/Table.vue';
</script>