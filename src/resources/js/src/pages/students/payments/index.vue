<template>
    <div class="panel  my-6">
        <TabGroup as="div" class="mb-5">
            <h3 class="font-semibold text-lg dark:text-white-light mb-5">L'etudient: {{ studentsStore.student.firstName + ' ' + studentsStore.student.lastName }}</h3>
    <TabList class="flex flex-wrap mt-3 border-b border-white-light dark:border-[#191e3a]">
        <Tab as="template" v-slot="{ selected }">
            <a
                href="javascript:;"
                class="p-3.5 py-2 -mb-[1px] flex items-center border border-transparent hover:text-info !outline-none transition duration-300"
                :class="{ '!border-white-light !border-b-white text-info dark:!border-[#191e3a] dark:!border-b-black': selected }"
            >
                Inscriptions
            </a>
        </Tab>
        <Tab as="template" v-slot="{ selected }">
            <a
                href="javascript:;"
                class="p-3.5 py-2 -mb-[1px] flex items-center border border-transparent hover:text-info !outline-none transition duration-300"
                :class="{ '!border-white-light !border-b-white text-info dark:!border-[#191e3a] dark:!border-b-black': selected }"
            >
                Frais
            </a>
        </Tab>
    </TabList>
    <TabPanels class="p-4 flex-1 text-sm border border-white-light border-t-0 dark:border-[#191e3a]">
        <TabPanel class="active">
            <paymentTable />
        </TabPanel>
        <TabPanel>
            <feesTable />
        </TabPanel>
    </TabPanels>
</TabGroup>
        
    </div>
</template>
<script setup>
    import paymentTable from '@/components/app/students/payments/Table.vue';
    import feesTable from '@/components/app/students/fees/Table.vue';
    import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
    import { computed, onMounted } from 'vue';
    import { useStudentsStore } from '@/stores/students.js';
    import { useRoute } from 'vue-router';

    const studentsStore = useStudentsStore();
    const route = useRoute();

    const student = computed(async() => {
        let data = await studentsStore.student ? studentsStore.student : []
        return data;
        });

    onMounted(() => {
        studentsStore.show(route.params.id)
    })
</script>