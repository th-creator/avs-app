<template>
    <div class="panel  my-6">
        <TabGroup as="div" class="mb-5">
            <h3 class="font-semibold text-lg dark:text-white-light mb-5">Le Groupe: {{ groupsStore.group && groupsStore.group.intitule }}</h3>
    <TabList class="flex flex-wrap mt-3 border-b border-white-light dark:border-[#191e3a]">
        <Tab as="template" v-slot="{ selected }">
            <a
                href="javascript:;"
                class="p-3.5 py-2 -mb-[1px] flex items-center border border-transparent hover:text-info !outline-none transition duration-300"
                :class="{ '!border-white-light !border-b-white text-info dark:!border-[#191e3a] dark:!border-b-black': selected }"
            >
                les étudiants
            </a>
        </Tab>
        <Tab as="template" v-slot="{ selected }">
            <a
                href="javascript:;"
                class="p-3.5 py-2 -mb-[1px] flex items-center border border-transparent hover:text-info !outline-none transition duration-300"
                :class="{ '!border-white-light !border-b-white text-info dark:!border-[#191e3a] dark:!border-b-black': selected }"
            >
                Les Paiements
            </a>
        </Tab>
    </TabList>
    <TabPanels class="p-4 flex-1 text-sm border border-white-light border-t-0 dark:border-[#191e3a]">
        <TabPanel class="active">
            <registrantsTable />
        </TabPanel>
        <TabPanel>
            <paymentsTable />
        </TabPanel>
    </TabPanels>
</TabGroup>
        
    </div>
</template>
<script setup>
    import registrantsTable from '@/components/app/groups/registrants/Table.vue';
    import paymentsTable from '@/components/app/groups/payments/Table.vue';
    import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
    import { computed, onMounted } from 'vue';
    import { useGroupsStore } from '@/stores/groups.js';
    import { useRoute } from 'vue-router';

    const groupsStore = useGroupsStore();
    const route = useRoute();

    onMounted(() => {
        groupsStore.show(route.params.id)
    })
</script>