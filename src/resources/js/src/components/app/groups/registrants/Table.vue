<template>
    <div>
        <div class="panel pb-0 mt-6">
            <!-- <h5 class="font-semibold text-lg dark:text-white-light mb-5">Les Payements et Inscriptions</h5> -->
            <div class="flex justify-between my-4">    
                <input v-model="params.search" type="text" class="form-input max-w-xs" placeholder="Rechercher..." />
                <div class="flex flex-row gap-4">
                    <div class="flex gap-2">
                        <multiselect
                            v-model="choosenAY"
                            :options="AYs"
                            class="custom-multiselect  max-w-xs"
                            :searchable="true"
                            placeholder="Le mois"
                            selected-label=""
                            select-label=""
                            deselect-label=""
                        ></multiselect>    
                    </div>
                    <button type="button" class="btn btn-warning" @click="exportToExcel">Exporter</button>
                </div>
            </div>
            <div class="datatable">
                <vue3-datatable
                    :rows="registrantsStore.groupRegistrants"
                    :columns="cols"
                    :totalRows="rows?.length"
                    :sortable="true"
                    :search="params.search"
                    :loading="isloading"
                    :paginationInfo="'{0} à {1} de {2}'"
                    skin="whitespace-nowrap bh-table-hover"
                    firstArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    lastArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg> '
                    previousArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    nextArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                >
                    <template #id="data">
                        <div class="flex justify-around w-full">
                            <h5 class="text-primary hover:underline">{{ data.value.student.id }}</h5>
                        </div>
                    </template>
                    <template #firstName="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.firstName }}</p>
                        </div>
                    </template>
                    <template #lastName="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.lastName  }}</p>
                        </div>
                    </template>
                    <template #email="data">
                        <div class="flex justify-around w-full">
                            <a class="font-semibold text-center" >{{ data.value.email }}</a>
                        </div>
                    </template>
                    <template #phone="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.phone }}</p>
                        </div>
                    </template>
                    <template #parent_phone="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.parent_phone }}</p>
                        </div>
                    </template>
                    <template #date="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.date }}</p>
                        </div>
                    </template>
                    <template #rest="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.rest }}</p>
                        </div>
                    </template>
                    <template #level="data">
                        <div class="flex justify-around w-full items-center gap-2">
                            <p class="font-semibold text-center">{{ data.value.level }}</p>
                        </div>
                    </template>
                </vue3-datatable>
            </div>
        </div>
    </div>
</template>
<script setup>
    import { ref, reactive, computed, onMounted, watch } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import { useRegistrantsStore } from '@/stores/registrants.js';
    import { useRoute } from 'vue-router';
    import Multiselect from '@suadelabs/vue3-multiselect';
    import * as XLSX from 'xlsx';
    import { useGroupsStore } from '@/stores/groups.js';

    const groupsStore = useGroupsStore();
    
    const params = reactive({
        current_page: 1,
        search: '',
        pagesize: 10,
        sort_column: 'id',
        sort_direction: 'asc',
    });

    const AYs = ref(['2024/2025','2025/2026','2026/2027','2027/2028','2028/2029','2029/2030','2030/2031','2031/2032','2032/2033', '2033/2034']);
    const choosenAY = ref(getCurrentAY())

    const registrantsStore = useRegistrantsStore();
    const route = useRoute();

    const studyDates = ref([]);
    const isloading = ref(true);
    function getCurrentAY() {
        const now = new Date()
        const y = now.getFullYear()
        const m = now.getMonth() + 1 // JS months are 0-based

        if (m >= 9) {
            return `${y}/${y + 1}`   // e.g., Sept 2025 → "2025/2026"
        } else {
            return `${y - 1}/${y}`   // e.g., Feb 2025 → "2024/2025"
        }
    }
    const cols =
        ref([
            { field: 'id', title: 'ID', isUnique: true, headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'lastName', title: 'Nom', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'firstName', title: 'Prenom', headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'phone', title: "Mobile", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'parent_phone', title: "Mobile du parent", headerClass: '!text-center flex justify-center', width: 'full' },
            // { field: 'rest', title: "Reste", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'date', title: "Date d'incription", headerClass: '!text-center flex justify-center', width: 'full' },
        ]) || [];

    const rows = computed(async() => {
        let data = await registrantsStore.groupRegistrants.length > 0 ? registrantsStore.groupRegistrants : []
        return data;
        });

    watch(choosenAY, async () => {
        isloading.value = true
        await registrantsStore.fetchGroupRegistrants(route.params.id,choosenAY.value)
        isloading.value = false
    });

    onMounted(async () => {
        await registrantsStore.fetchGroupRegistrants(route.params.id,choosenAY.value)
        isloading.value =false
        setTimeout(getStudyDatesForCurrentMonth, 1000);
    })
    const exportToExcel = () => {
        // Get the attendance data from Vuex
        const attendanceData = registrantsStore.groupRegistrants.map(res => ({no: res.student.id, nom: res.lastName, prenom: res.firstName, Mobile: res.phone,...studyDates.value}))
        
        // Create a worksheet from the attendance data
        const worksheet = XLSX.utils.json_to_sheet(attendanceData, {cellStyles: true});

        // Create a new workbook and append the worksheet
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Attendance');

        // Apply some cell styling
        const ws = workbook.Sheets['Attendance'];
        const range = XLSX.utils.decode_range(ws['!ref']);
        for(let R = range.s.r; R <= range.e.r; ++R) {
            for(let C = range.s.c; C <= range.e.c; ++C) {
                const cell_address = {c:C, r:R};
                const cell_ref = XLSX.utils.encode_cell(cell_address);
                if(ws[cell_ref]) {
                    ws[cell_ref].s = {
                        fill: {
                            fgColor: {rgb: "FFFFAA00"}
                        },
                        font: {
                            color: {rgb: "FF000000"},
                            sz: 14,
                            bold: true,
                            underline: true
                        }
                    };
                }
            }
        }

        // Export the workbook to an Excel file
        XLSX.writeFile(workbook, 'liste de présence - '+groupsStore.group.intitule+'.xlsx');
    };
    // Mapping from French days to JavaScript days
    const dayMap = {
        "Lundi": 2,    // Monday
        "Mardi": 3,    // Tuesday
        "Mercredi": 4, // Wednesday
        "Jeudi": 5,    // Thursday
        "Vendredi": 6, // Friday
        "Samedi": 7,   // Saturday
        "Dimanche": 1  // Sunday (this will be excluded)
        };

// Function to get all dates for the specified days in the current month, excluding Sundays
    const getStudyDatesForCurrentMonth = () => {
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth(); // Current month (0-based)
        const currentYear = currentDate.getFullYear();
        const datesToReturn = [];

        // Helper function to get all days of the current month
        function getDaysInMonth(month, year) {
            const date = new Date(year, month, 1);
            const days = [];

            while (date.getMonth() === month) {
            days.push(new Date(date));
            date.setDate(date.getDate() + 1);
            }
            return days;
        }

        // Get all the days in the current month
        const allDays = getDaysInMonth(currentMonth, currentYear);
        // Loop through the studyData and find all matching dates
        JSON.parse(groupsStore.group.timing).forEach(item => {
            console.log(allDays);
            
            const targetDay = dayMap[item.day]; // Get the day number (0-6) from French day

            // Loop through all the days of the current month
            allDays.forEach(date => {
                console.log(date.getDay(), (targetDay));
                
                if (date.getDay() === (targetDay)) {
                    datesToReturn.push(date.toISOString().slice(5, 10)); // Format MM-DD
                }
                if (7 === (targetDay) && date.getDay() == 6) {
                    date.setDate(date.getDate() + 1);
                    console.log(date.toISOString().slice(5, 10));
                    
                    datesToReturn.push(date.toISOString().slice(5, 10)); // Format MM-DD
                }
                // console.log(datesToReturn);
            });
        });

        //    = datesToReturn.sort();
        datesToReturn.sort().forEach(date => {
            studyDates.value[date] = ''; // Initialize each date with an empty string for now
        });
        console.log(studyDates.value);
    };
</script>
