<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex justify-between mb-4">
                <h5 class="font-semibold text-lg dark:text-white-light mb-5">Emplois du Temps</h5>
                <button class="btn btn-info" @click="exportTimetable">Exporter</button>
            </div>
            <div class="flex justify-between mb-4">
                <input 
                    v-model="params.search" 
                    type="text" 
                    class="form-input max-w-xs" 
                    placeholder="Rechercher..."
                />
                <button type="button" class="btn btn-info" @click="showPopup = true">Ajouter</button>
            </div>
                <div class="flex gap-3 justify-between my-4 items-center"> 
                    <div class="flex gap-2">
                        <multiselect
                            v-model="choosenLevel"
                            :options="levels"
                            class="custom-multiselect   max-w-lg"
                            :searchable="true"
                            placeholder="Niveau"
                            selected-label=""
                            select-label=""
                            deselect-label=""
                        ></multiselect>    
                        <multiselect
                            v-model="choosensubject"
                            :options="subjects"
                            class="custom-multiselect  min-w-xl"
                            :searchable="true"
                            placeholder="Matière"
                            selected-label=""
                            select-label=""
                            deselect-label=""
                        ></multiselect>    
                    </div>
                    <button type="button" class="btn btn-success w-40 h-9" @click="searchEmploi">
                        <span>Rechercher</span>
                    </button>
                </div>

            <div class="datatable">
                <vue3-datatable
                    :rows="emploisStore.emplois"
                    :columns="cols"
                    :search="params.search"
                    :loading="isLoading"
                    :sortable="true"
                    skin="whitespace-nowrap bh-table-hover"
                >

                    <!-- GROUP NAME -->
                    <template #group="data">
                        <div class="text-center font-semibold">
                            {{ data.value.group }}
                        </div>
                    </template>

                    <!-- TEACHER NAME -->
                    <template #teacher_name="data">
                        <div class="text-center font-semibold">
                            {{ data.value.teacher_name }}
                        </div>
                    </template>

                    <!-- SALLE -->
                    <template #salle="data">
                        <div class="text-center font-semibold">
                            {{ data.value.salle }}
                        </div>
                    </template>

                    <!-- TYPE (default or period) -->
                    <template #type="data">
                        <div class="text-center font-semibold">
                            <span 
                                class="px-3 py-1 rounded text-white"
                                :class="data.value.type === 'default' ? 'bg-green-500' : 'bg-orange-500'"
                            >
                                {{ data.value.type === 'default' ? 'Défaut' : 'Période' }}
                            </span>
                        </div>
                    </template>

                    <!-- PERIOD DATES -->
                    <template #period="data">
                        <div class="text-center font-semibold">
                            <span v-if="data.value.type === 'period'">
                                {{ data.value.from.substring(0, 10) }} → {{ data.value.to.substring(0, 10) }}
                            </span>
                            <span v-else>-</span>
                        </div>
                    </template>

                    <!-- TIMING JSON -->
                    <template #timing="data">
                        <div v-if="data.value.timing">
                            <div 
                                v-for="item in data.value.timing"
                                :key="item.day"
                                class="text-center font-semibold"
                            >
                                {{ item.day }} : {{ item.dates.join(', ') }}
                            </div>
                        </div>
                    </template>

                    <!-- ACTIONS -->
                    <template #actions="data">
                        <div class="flex items-center justify-center gap-5">
                            <p class="font-semibold text-2xl text-center cursor-pointer" @click="openAddPeriod(data.value)">+</p>
                            <IconComponent name="edit" @click="openEdit(data.value)" />
                            <IconComponent name="print" @click="printPayment(data.value)" />
                            <IconComponent 
                                v-if="authStore.user?.roles[0]?.name === 'admin'" 
                                name="delete" 
                                @click="deleteData(data.value)" 
                            />
                        </div>
                    </template>

                </vue3-datatable>
            </div>
        </div>
    </div>

    <!-- POPUPS -->
    <Add :showPopup="showPopup" :close="() => showPopup = false" v-if="showPopup" />
    <Edit :showEditPopup="showEditPopup" :editedData="editedData" :close="() => showEditPopup=false" v-if="showEditPopup" />
        <AddPeriod
        :showPopup="showPeriodPopup"
        :close="() => showPeriodPopup = false"
        :groupId="selectedGroupId"
        :groupName="selectedGroupName"
        :salle="selectedsale"
    />
    <!-- Hidden element to use for printing -->
    <div id="receipt" class="receipt-container hidden m-4 z-0">
            <div class="reciept-wrapper flex flex-col justify-between h-full">
                <div>
                    <div class="flex justify-between p-10">
                        <img src="/assets/images/avs-logo.png" alt="Image description" class="w-1/4">
                        <div>
                            <p class="text-3xl font-semibold" style="font-family: 'Courier New', monospace;">Centre AVS.ma</p>
                            <p class="text-lg font-medium mt-3" style="font-family: 'Courier New', monospace;">{{ selectedEmploi[0]?.teacher }}</p>
                            <p class="text-lg font-medium" style="font-family: 'Courier New', monospace;">{{ selectedEmploi[0]?.level }}</p>
                        </div>
                    </div>
                    
                    <table class="border-collapse border-2 border-gray-500 my-4">
                        <thead>
                            <tr>
                                <th class="border-2 border-gray-500">Jour</th>
                                <th class="border-2 border-gray-500">Matière</th>
                                <th class="border-2 border-gray-500">Heure</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(payment, index) in selectedEmploi" :key="index" class="border-2 border-gray-500">
                                <td class="border-2 border-gray-500">{{ payment.day }}</td>
                                <td class="border-2 border-gray-500">{{ payment.subject }}</td>
                                <td class="border-2 border-gray-500">{{ payment.timing }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    <p class="text-center">Centre AVS MA, Avenue Allal Al Fassi - à côté de la boulangerie Alpha 2000 - Marrakech</p>
                    <p class="text-center">Tel: 0524311982 / 0661843332 / 0667635797</p>
                </div>     
            </div>   
    </div>
    <!-- LARGE TIMETABLE EXPORT (A4 Landscape) -->
<div id="big-timetable" class="hidden p-8">

<div class="flex justify-between mb-10">
    <img src="/assets/images/avs-logo.png" class="w-1/6">
    <div class="w-4/6 px-20">
        <p class="text-4xl font-semibold border border-black px-2 py-6" style="font-family: 'Courier New'">Centre des Metier de l'Education et de l'Enseignement</p>
    </div>
    <div class="text-right w-1/6">
        <p class="text-xl font-semibold" style="font-family: 'Courier New'">Cours de soutien</p>
    </div>
</div>

<div class="mb-10">
    <hr>
    <p class="text-xl text-center underline mt-4"><strong>Niveau :</strong> {{ groupedRows[0]?.level }}</p>
</div>

<table class="w-full border-collapse border border-gray-600 text-xl">
    <thead>
        <tr class="!bg-white">
            <th class="border border-gray-600 p-2 w-1/6 bg-black text-white">Jours</th>
            <th class="border border-gray-600 p-2 w-1/6">Matière</th>
            <th class="border border-gray-600 p-2 w-3/6">Professeurs</th>
            <th class="border border-gray-600 p-2 w-1/6">Heures</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="(row, index) in groupedRows" :key="index">
            <td class="border border-gray-600 p-2">
                <div v-for="d in row.days">{{ d }}</div>
            </td>
            <td class="border border-gray-600 p-2">{{ row.subject }}</td>
            <td class="border border-gray-600 p-2">
                <span v-for="t in row.teachers"> -{{ t }}</span>
            </td>
            <td class="border border-gray-600 p-2">
                <div v-for="h in row.hours">{{ h }}</div>
            </td>
        </tr>
    </tbody>
</table>

<div class="mt-10">
    <p class="text-center text-lg">Centre AVS MA, Avenue Allal Al Fassi - à côté de la boulangerie Alpha 2000 - Marrakech</p>
    <p class="text-center text-lg">Tel: 0524311982 / 0661843332 / 0667635797</p>
</div>

</div>

</template>

<script setup>
    import { ref, reactive, onMounted, nextTick } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import IconComponent from '@/components/icons/IconComponent.vue';
    import Swal from 'sweetalert2';
    import Multiselect from '@suadelabs/vue3-multiselect';
    import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';
    import html2pdf from "html2pdf.js";
    
    import { useEmploiStore } from '@/stores/emploi.js';
    import { useAuthStore } from '@/stores/auth.js';
    
    import Add from './Add.vue';
    import AddPeriod from './AddPeriod.vue';
    import Edit from './Edit.vue';
    
    // STORES
    const authStore = useAuthStore();
    const emploisStore = useEmploiStore();
    
    // STATE
    const showPopup = ref(false);
    const showEditPopup = ref(false);
    const editedData = ref({});
    const isLoading = ref(false);
    const showPeriodPopup = ref(false);
    const selectedGroupId = ref(null);
    const selectedGroupName = ref("");
    const selectedsale = ref("");
    const selectedEmploi = ref([]);
    
    const subjects = ref([
        'PC', 'Math', 'SVT', 'Comptabilité', 'Economie générale', 'Gestion d\'entreprise',
        'Philosophie', 'Anglais', 'Francais', 'Arabe', 'Education islamique',
        'Sciences de l\'ingénieurie', 'Allemend', 'Histoire géographie',
        'Anglais communication', 'Français comminication',
        'PC-CI', 'Maths-CI', 'SVT-CI', 'Anglais-CI', 'Philo-CI',
        'FR-CI', 'Arabe-CI', 'H.G-CI','EI-CI'
    ]);
    
    const levels = ref([
        '6em primaire', '1 AC', '2 AC', '3 AC', 'TC',
        '1 Bac SC ECO', '1 Bac SM1', '1 Bac SC EXP', '1Bac SM2',
        '2 Bac SP', '2 Bac SM', '2 Bac SC ECO', 'autre'
    ]);
    
    const choosenLevel = ref('');
    const choosensubject = ref('');
    
    const params = reactive({ search: '' });
    
    const cols = ref([
        { field: 'group', title: 'Groupe', headerClass: '!text-center' },
        { field: 'teacher_name', title: 'Enseignant', headerClass: '!text-center' },
        { field: 'salle', title: 'Salle', headerClass: '!text-center' },
        { field: 'type', title: 'Type', headerClass: '!text-center' },
        { field: 'period', title: 'Période', headerClass: '!text-center' },
        { field: 'timing', title: 'Temps', headerClass: '!text-center' },
        { field: 'actions', title: 'Actions', headerClass: '!text-center' },
    ]);
    
    onMounted(async () => {
        if (emploisStore.emplois.length === 0) {
            isLoading.value = true;
            await emploisStore.index();
            isLoading.value = false;
        }
    });
    
    
    // ---------------------------------------------------------------------------
    //  PERIOD OVERRIDE LOGIC
    // ---------------------------------------------------------------------------
    function getPrintableEmploi(emplois) {
    const today = new Date().toISOString().split("T")[0];

    const defaults = emplois.filter(e => e.type === "default");

    // Collect ONLY active period emplois
    const periods = [];
    emplois.forEach(e => {
        if (
            e.type === "period" &&
            e.to.substring(0, 10) >= today &&
            e.from.substring(0, 10) <= today
        ) {
            periods.push(e);
        }
    });

    // If no active periods → return defaults as-is
    if (periods.length === 0) return defaults;

    // All groups that have active period records
    const overriddenGroups = new Set(periods.map(e => e.group));

    // Remove default emplois for those groups
    const cleanedDefaults = defaults.filter(
        d => !overriddenGroups.has(d.group)
    );

    // Final printable list = period emplois + remaining defaults
    return [...cleanedDefaults, ...periods];
}

    
    function mergePeriodWithDefault(defaultList, periodList) {
    
        const result = JSON.parse(JSON.stringify(defaultList));
    
        periodList.forEach(period => {
            result.forEach((item, index) => {
                if (item.subject === period.subject) {
    
                    // remove overridden days
                    const keepDays = item.timing.filter(t =>
                        !period.timing.some(pt => pt.day === t.day)
                    );
    
                    // merge
                    result[index].timing = [
                        ...keepDays,
                        ...period.timing
                    ];
                }
            });
        });
    
        return result;
    }
    
    
    // ---------------------------------------------------------------------------
    // GROUPING FUNCTION (PERFECT FOR PRINTING)
    // ---------------------------------------------------------------------------
    function groupEmploi(emplois) {
    
        const map = {};
    
        emplois.forEach(emp => {
    
            const signature = emp.timing
                .map(t => `${t.day}|${t.dates[0]}`)
                .sort()
                .join("||");
    
            const key = `${emp.subject}|${emp.level}|${signature}`;
    
            if (!map[key]) {
                map[key] = {
                    subject: emp.subject,
                    level: emp.level,
                    days: emp.timing.map(t => t.day),
                    hours: emp.timing.map(t => t.dates[0]),
                    teachers: []
                };
            }
    
            map[key].teachers.push(emp.teacher_name);
        });
    
        return Object.values(map);
    }
    
    
    // ---------------------------------------------------------------------------
    // SEARCH / FILTER
    // ---------------------------------------------------------------------------
    const searchEmploi = async () => {
        isLoading.value = true;
    
        await emploisStore.index(
            choosenLevel.value || null,
            choosensubject.value || null
        );
    
        isLoading.value = false;
    };
    
    
    // ---------------------------------------------------------------------------
    // EDIT
    // ---------------------------------------------------------------------------
    const openEdit = (row) => {
        editedData.value = row;
        showEditPopup.value = true;
    };
    
    const openAddPeriod = (item) => {
        selectedGroupId.value = item.group_id;
        selectedGroupName.value = item.group;
        selectedsale.value = item.salle;
        showPeriodPopup.value = true;
    };
    
    
    // ---------------------------------------------------------------------------
    // DELETE
    // ---------------------------------------------------------------------------
    const deleteData = (row) => {
        Swal.fire({
            title: 'Es-tu sûr?',
            text: "Vous ne pourrez pas revenir en arrière!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler',
        }).then((result) => {
            if (result.value) {
                emploisStore.destroy(row.id).then(() => {
                    Swal.fire('Supprimé!', 'Emploi supprimé.', 'success');
                });
            }
        });
    };
    
    
    // ---------------------------------------------------------------------------
    // PRINT (SMALL A5 VERSION)
    // ---------------------------------------------------------------------------
    const printPayment = async (emp) => {
    
        selectedEmploi.value = emp.timing.map(t => ({
            day: t.day,
            subject: emp.subject,
            level: emp.level,
            teacher: emp.teacher_name,
            timing: t.dates[0]
        }));
    
        const element = document.getElementById('receipt');
        element.classList.remove('hidden');
    
        await nextTick();
    
        await html2pdf().from(element).set({
            margin: 2,
            filename: `emploi-${emp.group}.pdf`,
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a5', orientation: 'landscape' },
        }).save();
    
        element.classList.add('hidden');
        selectedEmploi.value = [];
    };
    
    
    // ---------------------------------------------------------------------------
    // EXPORT FULL TIMETABLE (A4)
    // ---------------------------------------------------------------------------
    const groupedRows = ref([]);
    
    const exportTimetable = async () => {
    
        if (!choosenLevel.value) {
            return Swal.fire("Erreur", "Veuillez choisir un niveau.", "warning");
        }
    
        const emploisForLevel = emploisStore.emplois.filter(
            e => e.level === choosenLevel.value
        );
    
        if (emploisForLevel.length === 0) {
            return Swal.fire("Aucun résultat", "Aucun emploi trouvé.", "warning");
        }
    
        const printable = getPrintableEmploi(emploisForLevel);
        groupedRows.value = groupEmploi(printable);
    
        const element = document.getElementById("big-timetable");
        element.classList.remove("hidden");
    
        await nextTick();
    
        await html2pdf()
            .from(element)
            .set({
                margin: 5,
                filename: `Emploi-${choosenLevel.value}.pdf`,
                html2canvas: { scale: 2 },
                jsPDF: { unit: "mm", format: "a4", orientation: "landscape" },
            })
            .save();
    
        element.classList.add("hidden");
    };
    
    </script>
    