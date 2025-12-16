<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex gap-3 justify-end items-center">
                <div class="flex flex-col gap-3 justify-between items-center">
                <button type="button" class="btn btn-success w-40 h-9" @click="exportToExcel()">Excel</button>
                <button type="button" class="btn btn-warning w-40 h-9" @click="exportToPDF()">PDF</button>
            </div>
        </div>
            <div class="panel pb-0 mt-6">
                <div class="flex justify-between items-end mb-4 ">  
                    <div class="flex justify-between items-center"> 
                        <input v-model="params.search" type="text" class="form-input max-w-xs h-10" placeholder="Rechercher..." />
                    </div>
                    <!-- <div class="flex gap-10 items-end">
                        <label for="">Du:</label>
                        <input v-model="search.from" type="date" class="form-input max-w-xs" placeholder="Du..." />
                        <label for="">A:</label>
                        <input v-model="search.to" type="date" class="form-input max-w-xs" placeholder="A..." />
                    </div>
                    <div class="flex flex-col gap-4">  
                        <button type="button" class="btn btn-info w-36 h-9" @click="searchPayments">
                            <IconComponent v-if="isloading" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" name="loading" />
                            <span v-else>Rechercher</span>
                        </button>
                        
                    </div> -->
                    
                    <div class="flex gap-2">
                        <multiselect
                            v-model="choosenMonth"
                            :options="months"
                            class="custom-multiselect  max-w-xs"
                            :searchable="true"
                            placeholder="Le mois"
                            selected-label=""
                            select-label=""
                            deselect-label=""
                        ></multiselect>    
                        <multiselect
                            v-model="choosenYear"
                            :options="years"
                            class="custom-multiselect  max-w-xs"
                            :searchable="true"
                            placeholder="L'année"
                            selected-label=""
                            select-label=""
                            deselect-label=""
                        ></multiselect>   
                    </div>
                </div>
                <div class="datatable">
                    <vue3-datatable
                        :rows="choosenData"
                        :columns="cols"
                        :totalRows="choosenData?.length"
                        :sortable="true"
                        :loading="isloading"
                        :sortColumn="params.sort_column"
                        :sortDirection="params.sort_direction"
                        :hasCheckbox="false"
                        :search="params.search"
                        ref="datatable"
                        :paginationInfo="'{0} à {1} de {2}'"
                        skin="whitespace-nowrap bh-table-hover"
                        firstArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                        lastArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg> '
                        previousArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                        nextArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    >
                        <template #month="data">
                            <div class="flex justify-around w-full items-center gap-2">
                                <p class="font-semibold text-center">{{ data.value.month }}</p>
                            </div>
                        </template>
                        <template #amount="data">
                            <div class="flex justify-around w-full items-center gap-2">
                                <p class="font-semibold text-center">{{ data.value.amount }}MAD</p>
                            </div>
                        </template>
                        <template #fullName="data">
                            <div class="flex justify-around w-full items-center gap-2">
                                <p class="font-semibold text-center">{{ data.value.fullName }}</p>
                            </div>
                        </template>
                        <template #group="data">
                            <div class="flex justify-around w-full items-center gap-2">
                                <p class="font-semibold text-center">{{ data.value.group }}</p>
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
                        <template #paid="data">
                            <div class="flex justify-center w-full">
                                <div v-if="data.value.reduction == 100 && data.value.paid != -1">
                                    <div class="px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 w-[120px] text-center text-sm">
                                        Gratuit
                                    </div>
                                </div>
                                <div v-else-if="(data.value.paid == 1 && data.value.total == data.value.amount_paid)">
                                    <div class="px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 w-[120px] text-center text-sm">
                                        Payé
                                    </div>
                                </div>
                                <div v-else-if="data.value.paid == 1 && data.value.amount_paid > 0">
                                    <div class="px-4 py-2 rounded-full bg-orange-100 text-orange-600 w-[120px] text-center text-sm">
                                        En cours
                                    </div>
                                </div>
                                <div v-else-if="data.value.paid == -1">
                                    <div class="px-4 py-2 rounded-full bg-blue-100 text-blue-600 w-[120px] text-center text-sm">
                                        Remboursé
                                    </div>
                                </div>
                                <div v-else-if="data.value.paid == -2">
                                    <div class="px-4 py-2 rounded-full bg-blue-300 text-blue-800 w-[120px] text-center text-sm">
                                        Stationaire
                                    </div>
                                </div>
                                <div v-else>
                                    <div class="px-4 py-2 rounded-full bg-rose-100 text-rose-600 w-[120px] text-center text-sm">
                                        Non payé
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template #actions="data">
                            <div class="flex w-fit mx-auto justify-around gap-5">
                                <IconComponent v-if="authStore?.user && authStore?.user?.roles[0]?.name == 'admin'" name="delete" @click="deleteData(data.value)" />
                            </div>
                        </template>
                    </vue3-datatable>
                </div>
            </div>
            <div class="flex justify-end items-center my-4">
                <p class="font-semibold text-lg pb-4" v-if="total">Total: {{ total }} MAD</p>
                <!-- <p class="font-semibold text-lg">revenu: {{ choosenData.reduce((total, payment) => total + Number(payment.amount)*((100-Number(payment.reduction))/100), 0) }} MAD</p> -->
            </div>
        </div>
    </div>
</template>
<script setup>
    import { ref, reactive, computed, watch, onMounted } from 'vue';
    import Vue3Datatable from '@bhplugin/vue3-datatable';
    import { usePaymentsStore } from '@/stores/payments.js';
    import IconComponent from '@/components/icons/IconComponent.vue'
    import Swal from 'sweetalert2';
    import {useAuthStore} from '@/stores/auth.js';
    import * as XLSX from 'xlsx';
    import Multiselect from '@suadelabs/vue3-multiselect';
    import jsPDF from "jspdf";
    import autoTable from "jspdf-autotable";
    import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

    const authStore = useAuthStore();
    
    const choosenData = ref([]);
    const isloading = ref(false);

    const total = ref();

    const params = reactive({
        current_page: 1,
        search: '',
        pagesize: 10,
        sort_column: 'id',
        sort_direction: 'desc',
    });
    const months = ref(['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre', 'Octobre','Novembre','Décembre']);
    const choosenMonth = ref('Septembre');
    const years = ref([2024,2025,2026,2027,2028,2029,2030]);
    const choosenYear = ref(2024);


    const paymentsStore = usePaymentsStore();

    
    const search = ref({
        from: '',
        to: ''
    })

    const cols =
        ref([
            { field: 'receipt', title: "Recu", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'total', title: "Montant à payer", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'amount_paid', title: "Montant reçu", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'rest', title: "Reste", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'type', title: "Type", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'bank', title: "Bank", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'bank_receipt', title: "Chèque", headerClass: '!text-center flex justify-center', width: 'full' },
            { field: 'date', title: "Date", headerClass: '!text-center flex justify-center', width: 'full' },
        ]) || [];
    const rows = ref([])
    onMounted(async () => {
        const currentMonth = new Date().getMonth();
        choosenYear.value = new Date().getFullYear();
        choosenMonth.value = months.value[currentMonth];
    })
    watch(choosenMonth, async (newVal, oldVal) => {
        isloading.value =true
        await paymentsStore.fetchReceipt(choosenMonth.value, choosenYear.value)
        isloading.value =false
        console.log(paymentsStore.receiptPayments);
        
        const groupedByReceipt = paymentsStore.receiptPayments.reduce((map, current) => {
            const receipt = current.receipt;

            if (!map.has(receipt)) {
                map.set(receipt, {
                receipt,
                total: Number(current.total) || 0,
                amount_paid: Number(current.amount_paid) || 0,
                rest: Number(current.rest) || 0,
                groups: current.group ? new Set([current.group]) : new Set(),
                students: current.fullName ? new Set([current.fullName]) : new Set(), // 👈 NEW
                hasFee: !current.group,
                type: current.type,
                check: current.check,
                bank: current.bank,
                date: current.date,
                });
            } else {
                const item = map.get(receipt);

                item.total += Number(current.total) || 0;
                item.amount_paid += Number(current.amount_paid) || 0;
                item.rest += Number(current.rest) || 0;

                if (current.group) {
                    item.groups.add(current.group);
                } else {
                    item.hasFee = true;
                }

                if (current.fullName) {
                    item.students.add(current.fullName); // 👈 NEW
                }
            }

            return map;
            }, new Map());


        choosenData.value = Array.from(groupedByReceipt.values()).map(item => {
            // 🏷️ Label
            const labels = [];

            if (item.groups.size > 0) {
                labels.push(
                ...Array.from(item.groups).map(g => `| ${g}`)
                );
            }

            if (item.hasFee) {
                labels.push("| Frais d'inscriptions");
            }

            // 👤 Student name(s)
            const studentName =
                item.students.size === 1
                ? Array.from(item.students)[0]
                : Array.from(item.students).join(" | ");
            let finalLabels = labels.join(" ")

            return {
                receipt: item.receipt,
                total: item.total,
                rest: item.rest,
                amount_paid: item.amount_paid,
                type: item.type,
                bank: item.bank,
                check: item.check,
                name: finalLabels.slice(2,-1),
                studentName, // 👈 FINAL RESULT
                date: item.date,
            };
        });
        total.value = choosenData.value.reduce((total, payment) => {
            // let amount = payment.total !== null ? payment.total : Number(payment.amount)*((100-Number(payment.reduction))/100)
            return total + Number(payment.amount_paid)
        }, 0)
    });
    watch(choosenYear, async (newVal, oldVal) => {
        isloading.value =true
        await paymentsStore.fetchReceipt(choosenMonth.value,choosenYear.value)
        isloading.value =false
        
        const groupedByReceipt = paymentsStore.receiptPayments.reduce((map, current) => {
            const receipt = current.receipt;

            if (!map.has(receipt)) {
                map.set(receipt, {
                receipt,
                total: Number(current.total) || 0,
                amount_paid: Number(current.amount_paid) || 0,
                rest: Number(current.rest) || 0,
                groups: current.group ? new Set([current.group]) : new Set(),
                students: current.fullName ? new Set([current.fullName]) : new Set(), // 👈 NEW
                hasFee: !current.group,
                type: current.type,
                check: current.check,
                bank: current.bank,
                date: current.date,
                });
            } else {
                const item = map.get(receipt);

                item.total += Number(current.total) || 0;
                item.amount_paid += Number(current.amount_paid) || 0;
                item.rest += Number(current.rest) || 0;

                if (current.group) {
                item.groups.add(current.group);
                } else {
                item.hasFee = true;
                }

                if (current.fullName) {
                item.students.add(current.fullName); // 👈 NEW
                }
            }

            return map;
            }, new Map());


        choosenData.value = Array.from(groupedByReceipt.values()).map(item => {
            // 🏷️ Label
            const labels = [];

            if (item.groups.size > 0) {
                labels.push(
                ...Array.from(item.groups).map(g => `| ${g}`)
                );
            }

            if (item.hasFee) {
                labels.push("| Frais d'inscriptions");
            }

            // 👤 Student name(s)
            const studentName =
                item.students.size === 1
                ? Array.from(item.students)[0]
                : Array.from(item.students).join(" | ");
            let finalLabels = labels.join(" ")
            console.log('item',item);
            
            return {
                receipt: item.receipt,
                total: item.total,
                amount_paid: item.amount_paid,
                type: item.type,
                bank: item.bank,
                check: item.check,
                name: finalLabels.slice(2,-1),
                studentName, // 👈 FINAL RESULT
                date: item.date,
            };
        });
        total.value = choosenData.value.reduce((total, payment) => {
            // let amount = payment.total !== null ? payment.total : Number(payment.amount)*((100-Number(payment.reduction))/100)
            return total + Number(payment.amount_paid)
        }, 0)
    });

    // const searchPayments = async () => {
    //     isloading.value =true
    //     await paymentsStore.fetchReceipt(search.value.from,search.value.to)
    //     isloading.value =false
        
    //     choosenData.value = paymentsStore.receiptPayments.reduce((accumulator, current) => {
    //         let duplicate = accumulator.find(item => item.receipt === current.receipt);
    //         if (duplicate) {
    //             duplicate.total = Number(duplicate.total) + Number(current.total);
    //             duplicate.amount_paid = Number(duplicate.amount_paid) + Number(current.amount_paid);
    //         } else {
    //             accumulator.push(current);
    //         }
    //         return accumulator;
    //     }, []);
    //     total.value = choosenData.value.reduce((total, payment) => {
    //         // let amount = payment.total !== null ? payment.total : Number(payment.amount)*((100-Number(payment.reduction))/100)
    //         return total + Number(payment.amount_paid)
    //     }, 0)
    //     console.log('total', choosenData.value);
    // }

    const deleteData = (data) => {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                popup: 'sweet-alerts',
                confirmButton: 'btn btn-secondary',
                cancelButton: 'btn btn-dark ltr:mr-3 rtl:ml-3',
            },
            buttonsStyling: false,
        });
        swalWithBootstrapButtons
        .fire({
            title: 'Es-tu sûr?',
            text: "Vous ne pourrez pas revenir en arrière!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Non, Annuler!',
            reverseButtons: true,
            padding: '2em',
        })
        .then((result) => {
            if (result.value) {
                paymentsStore.destroy(data.id).then(res => {
                    swalWithBootstrapButtons.fire('supprimé!', 'il a été supprimé.', 'success');
                    rows.value = res.data.data
                }).catch(err => {
                    swalWithBootstrapButtons.fire('supprimé!', "il a été supprimé.", 'success');
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire('Annulé', "aucune mesure n'a été prise:)", 'error');
            }
        });
    }
    const getTodayDate = () => {
        const d = new Date();
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, "0");
        const day = String(d.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    };
    const exportToPDF = () => {
        const doc = new jsPDF({
            orientation: "portrait",
            unit: "mm",
            format: "a4",
        });

        doc.setFont("helvetica", "normal");

        // Title
        doc.setFontSize(12);
        doc.text(
            `Registre des paiements – ${choosenMonth.value} ${choosenYear.value}`,
            105,
            12,
            { align: "center" }
        );

        const columns = [
            { header: "Reçu", dataKey: "receipt" },
            { header: "Nom", dataKey: "student" },
            { header: "Groupe et FI", dataKey: "label" },
            { header: "Montant à payer", dataKey: "total" },
            { header: "Montant reçu", dataKey: "paid" },
            { header: "Reste", dataKey: "rest" },
        ];

        const rows = choosenData.value.map(res => ({
            receipt: res.receipt,

            student: res.studentName
            ? res.studentName.split(" / ").join("\n")
            : "",

            label: res.name
            ? res.name
                .split(" - ")
                .filter(Boolean)
                .map(v => `- ${v.trim()}`)
                .join("\n")
            : "",

            total: `${res.total ?? res.amount_paid} MAD`,
            paid: `${res.amount_paid} MAD`,
            rest: `${(Number(res.total ?? res.amount_paid) - Number(res.amount_paid))} MAD`,
        }));

        autoTable(doc, {
            columns,
            body: rows,
            startY: 18,

            theme: "grid", // 👈 borders ON

            styles: {
            fontSize: 9,
            cellPadding: 2,
            valign: "top",
            halign: "left",
            overflow: "linebreak",
            lineColor: [0, 0, 0],
            lineWidth: 0.1, // 👈 thin borders
            },

            headStyles: {
            fillColor: [64, 64, 64], // dark grey
            textColor: 255,          // white text
            fontStyle: "bold",
            halign: "center",
            valign: "middle",
            lineColor: [0, 0, 0],
            lineWidth: 0.2,          // slightly stronger header border
            },

            columnStyles: {
            receipt: { cellWidth: 18, halign: "center" },
            student: { cellWidth: 32 },
            label: { cellWidth: 60 },
            total: { cellWidth: 25, halign: "right" },
            paid: { cellWidth: 25, halign: "right" },
            rest: { cellWidth: 20, halign: "right" },
            },

            margin: { left: 10, right: 10 },
            pageBreak: "auto",
        });

        doc.save(`registre-${getTodayDate()}.pdf`);
    };


    const exportToExcel = () => {
        const rows = choosenData.value.map(res => {
            // Students stacked vertically
            const students = res.studentName
            ? res.studentName.split(" | ").map(s => ` - ${s.trim()}`).join("\n")
            : '';

            // Groups + fees stacked vertically
            const groups = res.name
            ? res.name
                .split(" | ")
                .filter(Boolean)
                .map(g => `- ${g.trim()}`)
                .join("\n")
            : '';

            return {
            'Reçu': res.receipt,
            'Nom': students,
            'Groupe et FI': groups,
            'Montant à payer': res.total,
            'Montant reçu': res.amount_paid,
            'Reste': res.rest,
            };
        });

        const worksheet = XLSX.utils.json_to_sheet(rows);

        // Enable wrap text for ALL cells
        const range = XLSX.utils.decode_range(worksheet['!ref']);
        for (let R = range.s.r; R <= range.e.r; ++R) {
            for (let C = range.s.c; C <= range.e.c; ++C) {
            const cell = worksheet[XLSX.utils.encode_cell({ r: R, c: C })];
            if (cell) {
                cell.s = {
                alignment: {
                    wrapText: true,
                    vertical: 'top',
                },
                };
            }
            }
        }

        // Column widths (important for readability)
        worksheet['!cols'] = [
            { wch: 15 },
            { wch: 30 },
            { wch: 55 },
            { wch: 18 },
        ];

        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'groupes');

        XLSX.writeFile(workbook, `registre-${getTodayDate()}.xlsx`);
    };

</script>


<style scoped>
.hidden {
  display: none;
}

.receipt-container {
  padding: 5px;
}
.reciept-wrapper {
padding: 10px 20px;
  border: 1px solid #000;
  /* width: 700px; */
  margin: 30px 20px;

}
.receipt-container table {
  width: 100%;
  border-collapse: collapse;
  margin: 15px 0;
}

.receipt-container table th,
.receipt-container table td {
  border: 1px solid #000;
  padding: 10px;
  text-align: left;
}

.receipt-container table th {
  background-color: #f2f2f2;
}

</style>