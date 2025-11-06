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
                    class="absolute top-7 ltr:right-9 rtl:left-9 text-white-dark hover:text-dark"
                    @click="close()"
                  >
                    X
                  </button>
  
                  <div class="text-lg text-center font-semibold py-5">
                    Suivi du Paiement
                  </div>
  
                  <div class="p-5">
                    <form>
                        
                        <div class="relative mb-4">
                                    <label class="text-sm">Montant:</label>
                                    <input v-model="data.amount" type="number" placeholder="Montant" class="form-input" disabled/>
                                    <span v-if="errors.amount" class="text-red-600 text-sm">{{ errors.amount[0] }}</span>
                                </div>
                      <div class="relative mb-4">
                        <label class="text-sm">Montant reçu :</label>
                        <input
                          v-model="data.amount_paid"
                          @keyup="calculateRest"
                          type="number"
                          placeholder="Montant reçu"
                          class="form-input"
                        />
                        <span
                          v-if="errors.amount_paid"
                          class="text-red-600 text-sm"
                          >{{ errors.amount_paid[0] }}</span
                        >
                      </div>
  
                      <div class="relative mb-4">
                        <label class="text-sm">Reste à payer :</label>
                        <input
                          v-model="data.rest"
                          type="number"
                          placeholder="Reste à payer"
                          class="form-input"
                        />
                        <span
                          v-if="errors.rest"
                          class="text-red-600 text-sm"
                          >{{ errors.rest[0] }}</span
                        >
                      </div>
  
                      <div class="relative mb-4">
                        <label class="text-sm">Date de paiement :</label>
                        <input
                          v-model="data.date"
                          type="date"
                          placeholder="Date"
                          class="form-input"
                        />
                        <span
                          v-if="errors.date"
                          class="text-red-600 text-sm"
                          >{{ errors.date[0] }}</span
                        >
                      </div>
  
                      <div class="relative mb-4">
                        <label class="text-sm">Type de paiement :</label>
                        <multiselect
                          v-model="data.type"
                          :options="options"
                          class="custom-multiselect"
                          :searchable="true"
                          placeholder="Type"
                        ></multiselect>
                        <span
                          v-if="errors.type"
                          class="text-red-600 text-sm"
                          >{{ errors.type[0] }}</span
                        >
                      </div>
  
                      <div v-if="data.type == 'chèque'" class="relative mb-4">
                        <label class="text-sm">Banque :</label>
                        <input
                          v-model="data.bank"
                          type="text"
                          placeholder="Banque"
                          class="form-input"
                        />
                      </div>
  
                      <div v-if="data.type == 'chèque'" class="relative mb-4">
                        <label class="text-sm">Chèque :</label>
                        <input
                          v-model="data.bank_receipt"
                          type="text"
                          placeholder="Numéro de chèque"
                          class="form-input"
                        />
                      </div>
  
                      <div class="relative mb-4">
                        <label class="text-sm">Reçu :</label>
                        <input
                          v-model="data.receipt"
                          type="text"
                          placeholder="Reçu"
                          class="form-input"
                        />
                      </div>
  
                      <button
                        type="button"
                        class="btn btn-primary w-full h-10"
                        @click="CreateFollowUp"
                      >
                        <IconComponent
                          v-if="isLoading"
                          class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
                          name="loading"
                        />
                        <span v-else>Soumettre</span>
                      </button>
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
  import { ref, defineProps, watch } from 'vue'
  import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogOverlay
  } from '@headlessui/vue'
  import Multiselect from '@suadelabs/vue3-multiselect'
  import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css'
  import IconComponent from '@/components/icons/IconComponent.vue'
  import { usePaymentsStore } from '@/stores/payments.js'
  import { useAuthStore } from '@/stores/auth.js'
  import { useAlert } from '@/composables/useAlert'
  
  const props = defineProps({
    showPopup: Boolean,
    close: Function,
    refresh: Function,
    parentPayment: Object // 👈 the parent payment passed when user clicks "Follow-up"
  })
  watch(() => props.parentPayment, (newVal, oldVal) => {
    data.value = {
    parent_id: props.parentPayment?.id,
    date: new Date().toISOString().slice(0, 10),
    fullName: props.parentPayment?.fullName,
    amount: props.parentPayment?.rest || 0,
    reduction: props.parentPayment?.reduction || 0,
    rest: props.parentPayment?.rest || 0,
    amount_paid: 0,
    total: props.parentPayment?.rest || 0,
    type: 'espèces',
    bank: '',
    bank_receipt: '',
    receipt: '',
    month: props.parentPayment?.month,
    year: props.parentPayment?.year,
    group: props.parentPayment?.group,
    user_id: authStore?.user?.id,
    student_id: props.parentPayment?.student_id,
    group_id: props.parentPayment?.group_id
  }
});
  const paymentsStore = usePaymentsStore()
  const authStore = useAuthStore()
  
  const isLoading = ref(false)
  const errors = ref({})
  
  const options = ref(['espèces', 'chèque', 'virement'])
  
  const data = ref({
    parent_id: props.parentPayment?.id,
    date: new Date().toISOString().slice(0, 10),
    fullName: props.parentPayment?.fullName,
    amount: props.parentPayment?.rest || 0,
    reduction: props.parentPayment?.reduction || 0,
    rest:  props.parentPayment?.rest || 0,
    amount_paid: 0,
    total: props.parentPayment?.rest || 0,
    type: 'espèces',
    bank: '',
    bank_receipt: '',
    receipt: '',
    month: props.parentPayment?.month,
    year: props.parentPayment?.year,
    group: props.parentPayment?.group,
    user_id: authStore?.user?.id,
    student_id: props.parentPayment?.student_id,
    group_id: props.parentPayment?.group_id
  })
  
  const calculateRest = () => {
    console.log(props.parentPayment);
    
    data.value.rest = data.value.total - data.value.amount_paid
  }
  
  const CreateFollowUp = () => {
    if (isLoading.value) return
    isLoading.value = true
    errors.value = {}
  
    paymentsStore
      .followUpStore(data.value)
      .then(() => {
        isLoading.value = false
        useAlert('success', 'Suivi de paiement créé avec succès !')
        props.refresh()
        props.close()
      })
      .catch(err => {
        isLoading.value = false
        if (err.status === 422) {
          errors.value = err.response.data.errors
          useAlert('warning', 'Vérifiez les champs obligatoires !')
        } else useAlert('warning', "Une erreur s'est produite !")
      })
  }
</script>
  