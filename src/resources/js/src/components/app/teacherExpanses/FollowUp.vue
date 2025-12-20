<template>
    <TransitionRoot appear :show="show" as="template">
      <Dialog as="div" class="relative z-50">
        <!-- Overlay -->
        <TransitionChild
          as="template"
          enter="duration-300 ease-out"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="duration-200 ease-in"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <DialogOverlay class="fixed inset-0 bg-black/60" />
        </TransitionChild>
  
        <!-- Modal -->
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
                class="panel border-0 rounded-lg w-full max-w-lg text-black dark:text-white-dark"
              >
                <!-- Close -->
                <button
                  type="button"
                  class="absolute top-6 right-6 text-white-dark hover:text-dark"
                  @click="close"
                >
                  ✕
                </button>
  
                <!-- Title -->
                <div class="text-lg font-semibold text-center py-4">
                  Validation de paiement enseignant
                </div>
  
                <!-- Form -->
                <div class="p-6 space-y-4">
  
                  <!-- Teacher -->
                  <!-- <div>
                    <label class="text-sm">Enseignant</label>
                    <input
                      type="text"
                      class="form-input bg-gray-100 cursor-not-allowed"
                      :value="form.teacher"
                      disabled
                    />
                  </div> -->
  
                  <!-- Group (LOCKED) -->
                  <div>
                    <label class="text-sm">Groupe</label>
                    <input
                      type="text"
                      class="form-input bg-gray-100 cursor-not-allowed"
                      :value="form.group"
                      disabled
                    />
                  </div>
                    <div class="relative mb-4 flex gap-4">
                        <div class="w-[70%]">
                    <label class="text-sm">Montant total du groupe</label>
                    <input
                      type="number"
                      class="form-input cursor-not-allowed"
                      :value="form.total"
                    />
                        </div>
                        <div class="w-[30%]">
                    <label class="text-sm">Pourcentage</label>
                    <input
                      type="number"
                      class="form-input cursor-not-allowed"
                      :value="form.percentage"
                    />
                        </div>
                    </div>
  
                  <!-- Amount -->
                  <div>
                    <label class="text-sm">Montant à payer</label>
                    <input
                      type="number"
                      class="form-input cursor-not-allowed"
                      :value="form.amount"
                    />
                  </div>
  
                  <!-- Rest -->
                  <div>
                    <label class="text-sm">Reste</label>
                    <input
                      type="number"
                      class="form-input cursor-not-allowed"
                      :value="form.rest"
                    />
                  </div>
  
                  <!-- Month / Year -->
                  <div class="flex gap-4">
                    <div class="w-1/2">
                      <label class="text-sm">Mois</label>
                      <input
                        type="text"
                        class="form-input bg-gray-100 cursor-not-allowed"
                        :value="form.month"
                        disabled
                      />
                    </div>
                    <div class="w-1/2">
                      <label class="text-sm">Année</label>
                      <input
                        type="number"
                        class="form-input bg-gray-100 cursor-not-allowed"
                        :value="form.year"
                        disabled
                      />
                    </div>
                  </div>
  
                  <!-- Date -->
                  <div>
                    <label class="text-sm">Date de validation</label>
                    <input
                      v-model="form.date"
                      type="date"
                      class="form-input"
                    />
                  </div>
  
                  <!-- Submit -->
                  <button
                    type="button"
                    class="btn btn-success w-full h-10 mt-4"
                    :disabled="isLoading"
                    @click="submit"
                  >
                    <IconComponent
                      v-if="isLoading"
                      name="loading"
                      class="mx-auto"
                    />
                    <span v-else>Valider le paiement</span>
                  </button>
  
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue'
  import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogOverlay,
  } from '@headlessui/vue'
  import IconComponent from '@/components/icons/IconComponent.vue'
  import { useTeacherExpansesStore } from '@/stores/teacherExpanse'
  import { useAuthStore } from '@/stores/auth'
  import { useAlert } from '@/composables/useAlert'
  
  /* =========================
     PROPS
  ========================= */
  const props = defineProps({
    show: Boolean,
    close: Function,
    refresh: Function,
    expanse: {
      type: Object,
      required: true, // must come from table
    },
  })
  
  /* =========================
     STATE
  ========================= */
  const isLoading = ref(false)
  const teacherExpansesStore = useTeacherExpansesStore()
  const authStore = useAuthStore()
  
  const form = ref({
    teacher: '',
    teacher_id: null,
    group: '',
    total: 0,
    percentage: 70,
    amount: 0,
    rest: 0,
    month: '',
    year: '',
    date: '',
    user_id: null,
  })
  
  /* =========================
     PREFILL (IMPORTANT)
  ========================= */
  watch(
    () => props.expanse,
    (e) => {
      if (!e) return
  
      form.value = {
        teacher: e.teacher,
        teacher_id: e.teacher_id,
        group: e.group,
        total: e.total,
        percentage: e.percentage ?? 70,
        amount: e.amount,
        rest: e.rest,
        month: e.month,
        year: e.year,
        date: new Date().toISOString().slice(0, 10),
        user_id: authStore.user?.id,
      }
    },
    { immediate: true }
  )
  
  /* =========================
     SUBMIT
  ========================= */
  const submit = async () => {
    if (isLoading.value) return
  
    isLoading.value = true
  
    try {
      await teacherExpansesStore.store(form.value)
      useAlert('success', 'Paiement validé avec succès')
      props.refresh()
      props.close()
    } catch (err) {
      useAlert('error', 'Erreur lors de la validation')
    } finally {
      isLoading.value = false
    }
  }
  </script>
  