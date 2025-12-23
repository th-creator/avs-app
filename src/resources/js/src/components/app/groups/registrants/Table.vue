<template>
    <div>
      <div class="panel pb-0 mt-6">
        <div class="flex justify-between my-4">
          <input
            v-model="params.search"
            type="text"
            class="form-input max-w-xs"
            placeholder="Rechercher..."
          />
          <div class="flex flex-row gap-4">
            <div class="flex gap-2">
              <multiselect
                v-model="choosenAY"
                :options="AYs"
                class="custom-multiselect max-w-xs"
                :searchable="true"
                placeholder="Année scolaire"
                selected-label=""
                select-label=""
                deselect-label=""
              />
            </div>
            <button type="button" class="btn btn-warning" @click="exportToExcel">
              Excel
            </button>
            <button type="button" class="btn btn-primary" @click="exportToPdf">
              PDF
            </button>
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
            :sortColumn="params.sort_column"
            :sortDirection="params.sort_direction"
            :paginationInfo="'{0} à {1} de {2}'"
            skin="whitespace-nowrap bh-table-hover"
          >
            <template #firstName="data">
              <div class="flex justify-around w-full items-center gap-2">
                <p class="font-semibold text-center">
                  {{ data.value.firstName }}
                </p>
              </div>
            </template>
            <template #lastName="data">
              <div class="flex justify-around w-full items-center gap-2">
                <p class="font-semibold text-center">
                  {{ data.value.lastName }}
                </p>
              </div>
            </template>
            <template #date="data">
              <div class="flex justify-around w-full items-center gap-2">
                <p class="font-semibold text-center">
                  {{ data.value.date }}
                </p>
              </div>
            </template>
            <template #phone="data">
              <div class="flex justify-around w-full items-center gap-2">
                <p class="font-semibold text-center">
                  {{ data.value.phone }}
                </p>
              </div>
            </template>
            <template #parent_phone="data">
              <div class="flex justify-around w-full items-center gap-2">
                <p class="font-semibold text-center">
                  {{ data.value.parent_phone }}
                </p>
              </div>
            </template>
            <template #actions="data">
                <div class="flex w-fit mx-auto justify-around gap-5">
                    <IconComponent name="edit" @click="() => toggleEdit(data.value)" />
                    <router-link v-if="data.value.student_id" :to="`/students/${data.value.student_id}/payments`" class="main-logo flex items-center shrink-0">
                        <IconComponent name="view" />
                    </router-link>
                    <IconComponent v-if="authStore?.user && authStore?.user?.roles[0]?.name == 'admin'" name="delete" @click="deleteData(data.value)" />
                </div>
            </template>
          </vue3-datatable>
        </div>
      </div>
    </div>
    <Edit :close="() => {showEditPopup = false;registrantsStore.fetchGroupRegistrants(route.params.id, choosenAY)}" :showEditPopup="showEditPopup" v-bind:editedData="editedData" v-if="showEditPopup"/>
  </template>
  
  <script setup>
  import { ref, reactive, computed, onMounted, watch } from 'vue'
  import Vue3Datatable from '@bhplugin/vue3-datatable'
  import Multiselect from '@suadelabs/vue3-multiselect'
  import * as XLSX from 'xlsx'
  import jsPDF from 'jspdf'
  
  import { useRegistrantsStore } from '@/stores/registrants.js'
  import { useGroupsStore } from '@/stores/groups.js'
  import { useRoute } from 'vue-router'
  import {useAuthStore} from '@/stores/auth.js';
  import Edit from '../../registrants/Edit.vue'
    import IconComponent from '@/components/icons/IconComponent.vue'
  
  const registrantsStore = useRegistrantsStore()
  const groupsStore = useGroupsStore()
  const route = useRoute()
  const authStore = useAuthStore();
  const editedData = ref({})
  
  /* --------------------------------------------------
     BASIC STATE
  -------------------------------------------------- */
  const params = reactive({
    current_page: 1,
    search: '',
    pagesize: 10,
    sort_column: 'firstName',
    sort_direction: 'asc',
  })
  
  const AYs = ref([
    '2024/2025',
    '2025/2026',
    '2026/2027',
    '2027/2028',
    '2028/2029',
    '2029/2030',
    '2030/2031',
    '2031/2032',
    '2032/2033',
    '2033/2034',
  ])
  
  const choosenAY = ref(getCurrentAY())
  const isloading = ref(true)
  const showEditPopup = ref(false);
  
  // will contain { "02": "", "06": "", ... }
  const studyDates = ref({})
  
  const toggleEdit = (data) => {
    editedData.value = data
    showEditPopup.value = true
  }
  function getCurrentAY() {
    const now = new Date()
    const y = now.getFullYear()
    const m = now.getMonth() + 1
    return m >= 9 ? `${y}/${y + 1}` : `${y - 1}/${y}`
  }
  
  const cols = ref([
    { field: 'firstName', title: 'Prénom', headerClass: '!text-center flex justify-center' },
    { field: 'lastName', title: 'Nom', headerClass: '!text-center flex justify-center' },
    { field: 'phone', title: 'Mobile', headerClass: '!text-center flex justify-center' },
    { field: 'parent_phone', title: 'Mobile du parent', headerClass: '!text-center flex justify-center' },
    { field: 'date', title: "Date d'inscription", headerClass: '!text-center flex justify-center' },
    { field: 'actions', title: 'Actions', headerClass: '!text-center flex justify-center', width: 'full' },
  ])
  
  const rows = computed(async () => {
    let data = registrantsStore.groupRegistrants.length
      ? registrantsStore.groupRegistrants
      : []
    return data
  })
  
  const deleteData = (data) => {
        console.log(data);
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
                registrantsStore.destroy(data.id).then(res => {
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
  /* --------------------------------------------------
     WATCH AY
  -------------------------------------------------- */
  watch(choosenAY, async () => {
    isloading.value = true
    await registrantsStore.fetchGroupRegistrants(route.params.id, choosenAY.value)
    isloading.value = false
    setTimeout(buildStudyDatesFromTiming, 100)
  })
  
  /* --------------------------------------------------
     ON MOUNT
  -------------------------------------------------- */
  onMounted(async () => {
    await registrantsStore.fetchGroupRegistrants(route.params.id, choosenAY.value)
    isloading.value = false
    setTimeout(buildStudyDatesFromTiming, 100)
  })
  
  /* --------------------------------------------------
     BUILD DATES from group.timing
  -------------------------------------------------- */
  const dayMap = {
    Lundi: 1,
    Mardi: 2,
    Mercredi: 3,
    Jeudi: 4,
    Vendredi: 5,
    Samedi: 6,
    Dimanche: 0,
  }
  
  function buildStudyDatesFromTiming() {
    const group = groupsStore.group
    const current = new Date()
    const year = current.getFullYear()
    const month = current.getMonth() // 0-based
  
    const dates = {}
  
    if (group?.timing) {
      const days = []
      const d = new Date(year, month, 1)
      while (d.getMonth() === month) {
        days.push(new Date(d))
        d.setDate(d.getDate() + 1)
      }
  
      JSON.parse(group.timing).forEach((item) => {
        const target = dayMap[item.day]
        days.forEach((date) => {
          if (date.getDay() === target) {
            const dayString = String(date.getDate()).padStart(2, '0')
            dates[dayString] = ''
          }
        })
      })
    }
  
    if (!Object.keys(dates).length) {
      ;['02', '06', '09', '13', '16', '20', '23', '27', '30'].forEach((d) => {
        dates[d] = ''
      })
    }
  
    const sorted = {}
    Object.keys(dates)
      .sort((a, b) => Number(a) - Number(b))
      .forEach((k) => {
        sorted[k] = dates[k]
      })
  
    studyDates.value = sorted
  }
  
  /* --------------------------------------------------
     EXCEL (unchanged)
  -------------------------------------------------- */
/* --------------------------------------------------
   EXCEL (fixed column order to match PDF)
-------------------------------------------------- */
const exportToExcel = () => {
  const students = registrantsStore.groupRegistrants || []
  const dates = Object.keys(studyDates.value || {}).sort((a, b) => Number(a) - Number(b))

  // Build Excel rows in same column order as PDF
  const attendanceData = students.map((res, index) => {
    const row = {
      'N°': index + 1,
      NOM: res.lastName?.toUpperCase() || '',
      PRENOM: res.firstName || '',
    }

    // Add all date columns in sorted order
    dates.forEach((d) => {
      row[d] = ''
    })

    return row
  })

  // Ensure headers appear in correct order: static columns first, then dates
  const headers = ['N°', 'NOM', 'PRENOM', ...dates]

  const worksheet = XLSX.utils.json_to_sheet(attendanceData, {
    header: headers,
    skipHeader: false,
  })

  // Style columns width a bit
  const colWidths = [
    { wch: 5 }, // N°
    { wch: 25 }, // NOM
    { wch: 20 }, // PRENOM
    ...dates.map(() => ({ wch: 6 })),
  ]
  worksheet['!cols'] = colWidths

  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Liste de présence')

  const groupName = groupsStore.group?.intitule || 'Groupe'
  XLSX.writeFile(workbook, `Liste de présence - ${groupName}.xlsx`)
}

  
  /* --------------------------------------------------
     PDF
  -------------------------------------------------- */
  const exportToPdf = async () => {
    try {
      const doc = new jsPDF('p', 'mm', 'a4')
      const pageWidth = doc.internal.pageSize.getWidth()
      const pageHeight = doc.internal.pageSize.getHeight()
  
      const marginLeft = 10
      const marginRight = 10
      const usableWidth = pageWidth - marginLeft - marginRight
  
      const group = groupsStore.group || {}
      const students = registrantsStore.groupRegistrants || []
  
      // dates
      let dates = Object.keys(studyDates.value || {})
      if (!dates.length) {
        dates = ['02', '06', '09', '13', '16', '20', '23', '27', '30']
      }  
      // ✅ ensure dates are in ascending order
      dates = dates.sort((a, b) => Number(a) - Number(b))

      const monthNamesFr = [
        'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
        'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
      ]
      const moisLabel = monthNamesFr[new Date().getMonth()]
  
      // parse header
      const parsed = parseIntitule(group?.intitule || '')
  
      // load logo from /public
      const logoImg = await loadPublicImage('/assets/images/avs-logo.png').catch(() => null)
  
      // 1) HEADER
      let y = 12
      y = drawHeaderLikePaper(doc, {
        y,
        x: marginLeft,
        usableWidth,
        parsed,
        group,
        moisLabel,
        dates,
        studentsCount: students.length,
        logoImg,
      })
  
      // 2) TABLE ROWS
      const colNo = 7
      const colNom = 52
      const colPrenom = 36
      const fixedWidth = colNo + colNom + colPrenom
      const dateW = (usableWidth - fixedWidth) / dates.length
  
      let rowY = y
      const rowH = 7
  
      doc.setFontSize(9)
      students.forEach((s, idx) => {
        // page break
        if (rowY + rowH > pageHeight - 10) {
          doc.addPage()
          // redraw header line on top of new page
          rowY = 12
          // we can also redraw the header row (N°, NOM, ...)
          rowY = redrawTableHeader(doc, {
            y: rowY,
            x: marginLeft,
            usableWidth,
            colNo,
            colNom,
            colPrenom,
            dates,
            dateW,
          })
        }
  
        const x = marginLeft
        const no = idx + 1
        const nom = (s.lastName || '').toUpperCase()
        const prenom = (s.firstName || '').toUpperCase()
  
        // cells borders
        doc.setDrawColor(0)
        doc.setLineWidth(0.25)
        // N°
        doc.rect(x, rowY, colNo, rowH)
        // NOM
        doc.rect(x + colNo, rowY, colNom, rowH)
        // PRENOM
        doc.rect(x + colNo + colNom, rowY, colPrenom, rowH)
  
        // dates cells
        let dx = x + fixedWidth
        dates.forEach(() => {
          doc.rect(dx, rowY, dateW, rowH)
          dx += dateW
        })
  
        // text
        doc.text(String(no), x + 2, rowY + 4)
        doc.text(nom, x + colNo + 2, rowY + 4)
        doc.text(prenom, x + colNo + colNom + 2, rowY + 4)
  
        rowY += rowH
      })
  
      // 3) save
      const fileName =
        'presence-' +
        (parsed.groupName || group?.intitule || 'groupe').replace(/\s+/g, '-') +
        '.pdf'
      doc.save(fileName)
    } catch (err) {
      console.error('PDF ERROR:', err)
      alert('Impossible de générer le PDF (voir console).')
    }
  }
  
  /* --------------------------------------------------
     HELPERS
  -------------------------------------------------- */
  
  // parse "GR- ABDELAATI KHELAF-: 2 Bac SM-GA / Math"
  const parseIntitule = (intitule = '') => {
    const res = {
      teacherName: '',
      groupName: '',
      subject: '',
    }
  
    if (!intitule) return res
  
    let s = intitule.trim()
    if (s.startsWith('GR-')) {
      s = s.slice(3).trim()
    }
  
    const parts = s.split('-:')
    if (parts.length >= 2) {
      res.teacherName = parts[0].trim()
      const right = parts[1].trim()
      const subParts = right.split('/')
      res.groupName = subParts[0]?.trim() || ''
      res.subject = subParts[1]?.trim() || ''
    } else {
      res.groupName = s
    }
  
    return res
  }
  
  // load an image that is in /public/... (same origin)
  const loadPublicImage = (relativePath) =>
    new Promise((resolve, reject) => {
      const img = new Image()
      img.src = window.location.origin + relativePath
      img.onload = () => resolve(img)
      img.onerror = (err) => {
        console.warn('image load failed', err)
        reject(err)
      }
    })
  
  // draw the header (logo + 5 lines + big top line + header row)
  const drawHeaderLikePaper = (
    doc,
    { y, x, usableWidth, group, parsed, moisLabel, dates, studentsCount, logoImg }
  ) => {
    const logoH = 18
    const logoW = 40
  
    if (logoImg) {
      doc.addImage(logoImg, 'PNG', x, y - 3, logoW, logoH)
    } else {
      doc.setFontSize(16)
      doc.setFont('helvetica', 'bold')
      doc.text('AVS ma', x, y + 4)
    }
  
    const rightX = x + logoW + 6
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(10)
  
    const prof =
      parsed?.teacherName ||
      `${group?.teacher?.firstName ?? ''} ${group?.teacher?.lastName ?? ''}`.trim()
    const grp = parsed?.groupName || group?.intitule || ''
    const subj = parsed?.subject || group?.section?.title || ''
  
    doc.text(`Professeur : ${prof}`, rightX, y + 1)
    doc.text(`Groupe : ${grp}`, rightX, y + 6)
    doc.text(`Matière : ${subj}`, rightX, y + 11)
    doc.text(`Mois : ${moisLabel}`, rightX, y + 16)
    doc.text(`Nombre : ${studentsCount ?? ''}`, rightX, y + 21)
  
    const lineY = y + 25
    doc.setDrawColor(0)
    doc.setLineWidth(0.6)
    doc.line(x, lineY, x + usableWidth, lineY)
  
    // table header
    const colNo = 7
    const colNom = 52
    const colPrenom = 36
    const fixedWidth = colNo + colNom + colPrenom
    const dateW = (usableWidth - fixedWidth) / dates.length
    const headerY = lineY + 6
  
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(9)
    doc.text('N°', x + 2, headerY)
    doc.text('NOM', x + colNo + 2, headerY)
    doc.text('PRENOM', x + colNo + colNom + 2, headerY)
  
    let dx = x + fixedWidth
    doc.setFont('helvetica', 'normal')
    dates.forEach((d) => {
      doc.text(d, dx + 2, headerY)
      dx += dateW
    })
  
    doc.setDrawColor(0)
    doc.setLineWidth(0.25)
    doc.line(x, headerY + 2, x + usableWidth, headerY + 2)
  
    return headerY + 2
  }
  
  // when we go to a new page we need to redraw ONLY the table header
  const redrawTableHeader = (
    doc,
    { y, x, usableWidth, colNo, colNom, colPrenom, dates, dateW }
  ) => {
    const lineY = y
    doc.setDrawColor(0)
    doc.setLineWidth(0.6)
    doc.line(x, lineY, x + usableWidth, lineY)
  
    const headerY = lineY + 5
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(9)
    doc.text('N°', x + 2, headerY)
    doc.text('NOM', x + colNo + 2, headerY)
    doc.text('PRENOM', x + colNo + colNom + 2, headerY)
  
    let dx = x + (colNo + colNom + colPrenom)
    doc.setFont('helvetica', 'normal')
    dates.forEach((d) => {
      doc.text(d, dx + 2, headerY)
      dx += dateW
    })
  
    doc.setDrawColor(0)
    doc.setLineWidth(0.25)
    doc.line(x, headerY + 2, x + usableWidth, headerY + 2)
  
    return headerY + 2
  }
  </script>
  