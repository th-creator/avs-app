<template>
    <div>
      <TransitionRoot appear :show="showPopup" as="template">
        <Dialog as="div" class="relative z-50">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100"
            leave-to="opacity-0"
          >
            <DialogOverlay class="fixed inset-0 bg-[black]/60" />
          </TransitionChild>
  
          <div class="fixed inset-0 overflow-y-auto">
            <div class="flex min-h-full items-start justify-center px-4 py-8">
              <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0 scale-95"
                enter-to="opacity-100 scale-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100 scale-100"
                leave-to="opacity-0 scale-95"
              >
                <DialogPanel
                  class="panel border-0 px-4 py-1 rounded-lg overflow-hidden w-full max-w-lg text-black dark:text-white-dark"
                >
                  <button
                    type="button"
                    class="absolute top-7 ltr:right-9 rtl:left-9 text-white-dark hover:text-dark outline-none"
                    @click="closeModal"
                  >
                    X
                  </button>
  
                  <div
                    class="text-lg text-center font-semibold ltr:pl-5 rtl:pr-5 py-5 ltr:pr-[50px] rtl:pl-[50px]"
                  >
                    Ajouter
                  </div>
  
                  <div class="p-5">
                    <form>
                      <!-- Groupe -->
                      <div class="relative mb-4">
                        <label class="text-sm">Groupe:</label>
                        <multiselect
                          v-model="data.groupHolder"
                          :options="filteredGroups"
                          class="custom-multiselect"
                          :searchable="true"
                          placeholder="Groupe"
                          selected-label=""
                          select-label=""
                          deselect-label=""
                        ></multiselect>
                        <span v-if="errors.group_id" class="text-red-600 text-sm">
                          {{ errors.group_id[0] }}
                        </span>
                      </div>
  
                      <!-- Montant / Réduction -->
                      <div class="relative mb-4 flex gap-4">
                        <div class="w-[70%]">
                          <label class="text-sm">Montant:</label>
                          <input
                            @keyup="calculateRest"
                            v-model="data.amount"
                            type="number"
                            placeholder="Montant"
                            class="form-input"
                          />
                          <span v-if="errors.amount" class="text-red-600 text-sm">
                            {{ errors.amount[0] }}
                          </span>
                        </div>
                        <div class="w-[30%]">
                          <label class="text-sm">Réduction:</label>
                          <input
                            @keyup="calculateRest"
                            v-model="data.reduction"
                            type="number"
                            placeholder="Réduction"
                            class="form-input"
                          />
                          <span v-if="errors.reduction" class="text-red-600 text-sm">
                            {{ errors.reduction[0] }}
                          </span>
                        </div>
                      </div>
  
                      <!-- Montant à payer -->
                      <div class="relative mb-4">
                        <label class="text-sm">Montant à payer:</label>
                        <input
                          v-model="data.total"
                          type="number"
                          placeholder="Montant à payer"
                          class="form-input"
                        />
                        <span v-if="errors.total" class="text-red-600 text-sm">
                          {{ errors.total[0] }}
                        </span>
                      </div>
  
                      <!-- Montant reçu -->
                      <div class="relative mb-4">
                        <label class="text-sm">Montant reçu:</label>
                        <input
                          v-model="data.amount_paid"
                          @keyup="calculatePayer"
                          type="number"
                          placeholder="Montant reçu"
                          class="form-input"
                        />
                        <span
                          v-if="errors.amount_paid"
                          class="text-red-600 text-sm"
                        >
                          {{ errors.amount_paid[0] }}
                        </span>
                      </div>
  
                      <!-- Reste -->
                      <div class="relative mb-4">
                        <label class="text-sm">Reste à payer:</label>
                        <input
                          v-model="data.rest"
                          type="number"
                          placeholder="Reste à payer"
                          class="form-input"
                        />
                        <span v-if="errors.rest" class="text-red-600 text-sm">
                          {{ errors.rest[0] }}
                        </span>
                      </div>
  
                      <!-- Date -->
                      <div class="relative mb-4">
                        <label class="text-sm">Date de paiement:</label>
                        <input
                          v-model="data.date"
                          type="date"
                          class="form-input"
                        />
                        <span v-if="errors.date" class="text-red-600 text-sm">
                          {{ errors.date[0] }}
                        </span>
                      </div>
  
                      <!-- Mois / Année -->
                      <div class="relative mb-4 flex gap-4">
                        <div class="w-[50%]">
                          <label class="text-sm">Mois:</label>
                          <multiselect
                            v-model="data.month"
                            :options="months"
                            class="custom-multiselect"
                            :searchable="true"
                            placeholder="Mois"
                            selected-label=""
                            select-label=""
                            deselect-label=""
                          ></multiselect>
                          <span v-if="errors.month" class="text-red-600 text-sm">
                            {{ errors.month[0] }}
                          </span>
                        </div>
                        <div class="w-[50%]">
                          <label class="text-sm">Année:</label>
                          <input
                            v-model="data.year"
                            type="number"
                            placeholder="Année"
                            class="form-input"
                          />
                          <span v-if="errors.year" class="text-red-600 text-sm">
                            {{ errors.year[0] }}
                          </span>
                        </div>
                      </div>
  
                      <!-- Type -->
                      <div class="relative mb-4">
                        <label class="text-sm">Type de paiement:</label>
                        <multiselect
                          v-model="data.type"
                          :options="options"
                          class="custom-multiselect"
                          :searchable="true"
                          placeholder="Type de paiement"
                          selected-label=""
                          select-label=""
                          deselect-label=""
                        ></multiselect>
                        <span v-if="errors.type" class="text-red-600 text-sm">
                          {{ errors.type[0] }}
                        </span>
                      </div>
  
                      <!-- Bank / Chèque -->
                      <div v-if="data.type == 'chèque'" class="relative mb-4">
                        <label class="text-sm">Bank:</label>
                        <input
                          v-model="data.bank"
                          type="text"
                          placeholder="Banque"
                          class="form-input"
                        />
                        <span v-if="errors.bank" class="text-red-600 text-sm">
                          {{ errors.bank[0] }}
                        </span>
                      </div>
                      <div v-if="data.type == 'chèque'" class="relative mb-4">
                        <label class="text-sm">Chèque:</label>
                        <input
                          v-model="data.bank_receipt"
                          type="text"
                          placeholder="Chèque"
                          class="form-input"
                        />
                        <span
                          v-if="errors.bank_receipt"
                          class="text-red-600 text-sm"
                        >
                          {{ errors.bank_receipt[0] }}
                        </span>
                      </div>
  
                      <!-- Reçu -->
                      <div class="relative mb-4">
                        <label class="text-sm">Reçu:</label>
                        <input
                          v-model="data.receipt"
                          type="text"
                          placeholder="Receipt"
                          class="form-input"
                        />
                        <span v-if="errors.receipt" class="text-red-600 text-sm">
                          {{ errors.receipt[0] }}
                        </span>
                      </div>
  
                      <!-- Buttons -->
                      <div class="flex gap-3">
                        <button
                          type="button"
                          class="btn btn-primary w-full h-10"
                          @click="Create()"
                        >
                          <IconComponent
                            v-if="isLoading"
                            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
                            name="loading"
                          />
                          <span v-else>Soumettre</span>
                        </button>
  
                        <button
                          type="button"
                          class="btn btn-outline-primary w-full h-10"
                          :disabled="!canGoNext"
                          @click="CreateAndNext()"
                        >
                          Ajouter & suivant
                        </button>
                      </div>
                    </form>
                  </div>
                </DialogPanel>
              </TransitionChild>
            </div>
          </div>
        </Dialog>
      </TransitionRoot>
    </div>
  </template>
  
  <script setup>
  import { ref, defineProps, onMounted, computed, watch } from 'vue'
  import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogOverlay
  } from '@headlessui/vue'
  import Multiselect from '@suadelabs/vue3-multiselect'
  import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css'
  import { usePaymentsStore } from '@/stores/payments.js'
  import { useAlert } from '@/composables/useAlert'
  import { useAuthStore } from '@/stores/auth.js'
  import { useRoute } from 'vue-router'
  import { useStudentsStore } from '@/stores/students.js'
  import { useGroupsStore } from '@/stores/groups.js'
  import IconComponent from '@/components/icons/IconComponent.vue'
  
  const isLoading = ref(false)
  
  const options = ref(['espèces', 'chèque', 'virement'])
  const months = ref([
    'Janvier',
    'Février',
    'Mars',
    'Avril',
    'Mai',
    'Juin',
    'Juillet',
    'Août',
    'Septembre',
    'Octobre',
    'Novembre',
    'Décembre'
  ])
  
  const studentsStore = useStudentsStore()
  const groupsStore = useGroupsStore()
  const paymentsStore = usePaymentsStore()
  const authStore = useAuthStore()
  const route = useRoute()
  
  const props = defineProps({
    showPopup: {
      type: Boolean,
      required: true
    },
    close: {
      type: Function,
      required: true
    },
    refresh: {
      type: Function,
      required: true
    }
  })
  
  const errors = ref({})
  // groups already paid in THIS popup session
  const handledGroups = ref([])
  
  const data = ref({
    date: new Date().toISOString().slice(0, 10),
    fullName: '',
    group: '',
    amount: 0,
    reduction: 0,
    rest: 0,
    amount_paid: 0,
    total: 0,
    type: 'espèces',
    bank: '',
    bank_receipt: '',
    receipt: '',
    month: '',
    year: 0,
    groupHolder: '',
    group_id: '',
    user_id: '',
    student_id: ''
  })
  
  // groups to display = student groups - handledGroups
  const filteredGroups = computed(() => {
    if (!groupsStore.studentGroups.length) return []
    return groupsStore.studentGroups
      .filter(g => !handledGroups.value.includes(g.id))
      .map(res => res.id + ' : ' + res.intitule)
  })
  
  // enable "next" only if there is at least 1 other group to pay
  const canGoNext = computed(() => {
    return filteredGroups.value.length > 0 && data.value.groupHolder !== ''
  })
  
  const calculateRest = () => {
    const reduction = data.value.reduction ? data.value.reduction : 0
    data.value.total = (data.value.amount * (100 - reduction)) / 100
  }
  const calculatePayer = () => {
    data.value.rest = data.value.total - data.value.amount_paid
  }
  
  // when group is chosen → load price and reduction
  watch(
    () => data.value.groupHolder,
    newVal => {
      if (!newVal) return
      const id = Number(newVal.split(':')[0].trim())
      const group = groupsStore.studentGroups.find(res => res.id == id)
      if (!group) return
      data.value.amount = group.section.price
  
      paymentsStore.registrantPayment(route.params.id, id).then(res => {
        if (res.data.data) {
          data.value.reduction = res.data.data.reduction
          const reduction = data.value.reduction ? data.value.reduction : 0
          data.value.total = (data.value.amount * (100 - reduction)) / 100
        } else {
          data.value.reduction = 0
          data.value.total = data.value.amount
        }
      })
    }
  )
  
  onMounted(async () => {
    await groupsStore.fetchStudentGroups(studentsStore.student.id)
    const currentMonth = new Date().getMonth()
    data.value.month = months.value[currentMonth]
    data.value.year = new Date().getFullYear()
  })
  
  const preparePayload = () => {
    return {
      ...data.value,
      user_id: authStore?.user?.id,
      student_id: route.params.id,
      group_id: data.value.groupHolder.split(':')[0].trim(),
      fullName:
        studentsStore.student.firstName + ' ' + studentsStore.student.lastName
    }
  }
  
  const resetGroupFieldsOnly = () => {
    data.value.groupHolder = ''
    data.value.group_id = ''
    data.value.amount = 0
    data.value.reduction = 0
    data.value.total = 0
    data.value.amount_paid = 0
    data.value.rest = 0
  }
  
  const Create = () => {
    if (isLoading.value) return
    isLoading.value = true
    errors.value = {}
    const payload = preparePayload()
  
    paymentsStore
      .store(payload)
      .then(() => {
        isLoading.value = false
        useAlert('success', 'Créé avec succès!')
        props.refresh()
        // close modal after single submit
        closeModal()
      })
      .catch(err => {
        isLoading.value = false
        if (err.status == 422) {
          errors.value = err.response.data.errors
          useAlert('warning', "quelque chose s'est mal passé!")
        } else if (err.status == 400) {
          useAlert('warning', 'Le paiement existe déjà!')
        } else useAlert('warning', "quelque chose s'est mal passé!")
      })
  }
  
  const CreateAndNext = () => {
    if (isLoading.value) return
    if (!data.value.groupHolder) return
    isLoading.value = true
    errors.value = {}
  
    const payload = preparePayload()
    const currentGroupId = Number(payload.group_id)
  
    paymentsStore
      .store(payload)
      .then(() => {
        // mark current group as done
        handledGroups.value.push(currentGroupId)
  
        // reset only group-related fields
        resetGroupFieldsOnly()
  
        // if there is another group, preselect it
        data.value.groupHolder = null  
        isLoading.value = false
        props.refresh()
        useAlert('success', 'Paiement enregistré, passez au suivant.')
      })
      .catch(err => {
        isLoading.value = false
        if (err.status == 422) {
          errors.value = err.response.data.errors
          useAlert('warning', "quelque chose s'est mal passé!")
        } else if (err.status == 400) {
          useAlert('warning', 'Le paiement existe déjà!')
        } else useAlert('warning', "quelque chose s'est mal passé!")
      })
  }
  
  const closeModal = () => {
    // reset everything when closing
    handledGroups.value = []
    props.close()
  }
  </script>
  