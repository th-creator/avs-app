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
                    Suivi des Frais
                  </div>
  
                  <div class="p-5">
                    <form>
                      <!-- Amount to Pay (original rest) -->
                      <div class="relative mb-4">
                        <label class="text-sm">Montant à payer:</label>
                        <input
                          v-model="form.amount"
                          type="number"
                          class="form-input"
                          disabled
                        />
                        <span v-if="errors.amount" class="text-red-600 text-sm">
                          {{ errors.amount[0] }}
                        </span>
                      </div>
  
                      <!-- Amount Paid Now -->
                      <div class="relative mb-4">
                        <label class="text-sm">Montant reçu :</label>
                        <input
                          v-model="form.amount_paid"
                          @keyup="calculateRest"
                          type="number"
                          class="form-input"
                        />
                        <span
                          v-if="errors.amount_paid"
                          class="text-red-600 text-sm"
                        >
                          {{ errors.amount_paid[0] }}
                        </span>
                      </div>
  
                      <!-- Remaining -->
                      <div class="relative mb-4">
                        <label class="text-sm">Reste à payer :</label>
                        <input
                          v-model="form.rest"
                          type="number"
                          class="form-input"
                        />
                        <span v-if="errors.rest" class="text-red-600 text-sm">
                          {{ errors.rest[0] }}
                        </span>
                      </div>
  
                      <!-- Date -->
                      <div class="relative mb-4">
                        <label class="text-sm">Date :</label>
                        <input
                          v-model="form.date"
                          type="date"
                          class="form-input"
                        />
                        <span v-if="errors.date" class="text-red-600 text-sm">
                          {{ errors.date[0] }}
                        </span>
                      </div>
  
                      <!-- Payment Type -->
                      <div class="relative mb-4">
                        <label class="text-sm">Type de paiement :</label>
                        <multiselect
                          v-model="form.type"
                          :options="options"
                          class="custom-multiselect"
                          :searchable="true"
                          placeholder="Type"
                        ></multiselect>
                        <span v-if="errors.type" class="text-red-600 text-sm">
                          {{ errors.type[0] }}
                        </span>
                      </div>
  
                      <!-- Bank (only if cheque) -->
                      <div v-if="form.type === 'chèque'" class="relative mb-4">
                        <label class="text-sm">Banque :</label>
                        <input
                          v-model="form.bank"
                          type="text"
                          placeholder="Banque"
                          class="form-input"
                        />
                      </div>
  
                      <div v-if="form.type === 'chèque'" class="relative mb-4">
                        <label class="text-sm">Numéro de chèque :</label>
                        <input
                          v-model="form.bank_receipt"
                          type="text"
                          placeholder="Numéro"
                          class="form-input"
                        />
                      </div>
  
                      <!-- Receipt -->
                      <div class="relative mb-4">
                        <label class="text-sm">Reçu :</label>
                        <input
                          v-model="form.receipt"
                          type="text"
                          placeholder="Reçu"
                          class="form-input"
                        />
                      </div>
  
                      <!-- Submit -->
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
  import { ref, defineProps, watch } from "vue";
  import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogOverlay,
  } from "@headlessui/vue";
  import Multiselect from "@suadelabs/vue3-multiselect";
  import IconComponent from "@/components/icons/IconComponent.vue";
  
  import { useFeesStore } from "@/stores/fees";
  import { useAuthStore } from "@/stores/auth";
  import { useAlert } from "@/composables/useAlert";
  
  const props = defineProps({
    showPopup: Boolean,
    close: Function,
    refresh: Function,
    parentFee: Object,
  });
  
  const feesStore = useFeesStore();
  const authStore = useAuthStore();
  
  const options = ref(["espèces", "chèque", "virement"]);
  const isLoading = ref(false);
  const errors = ref({});
  
  const form = ref({
    parent_id: null,
    fullName: "",
    date: "",
    amount: 0,
    reduction: 0,
    total: 0,
    amount_paid: 0,
    rest: 0,
    type: "espèces",
    bank: "",
    bank_receipt: "",
    receipt: "",
    student_id: "",
    user_id: "",
  });
  
  watch(
    () => props.parentFee,
    (newVal) => {
      if (!newVal) return;
  
      form.value = {
        parent_id: newVal.id,
        fullName: newVal.fullName,
        date: new Date().toISOString().slice(0, 10),
        amount: newVal.rest || 0,
        reduction: newVal.reduction || 0,
        total: newVal.rest || 0,
        amount_paid: 0,
        rest: newVal.rest || 0,
        type: "espèces",
        bank: "",
        bank_receipt: "",
        receipt: "",
        student_id: newVal.student_id,
        user_id: authStore.user?.id,
      };
    }
  );
  
  const calculateRest = () => {
    form.value.rest = form.value.total - Number(form.value.amount_paid || 0);
  };
  
  const CreateFollowUp = () => {
    if (isLoading.value) return;
    isLoading.value = true;
    errors.value = {};
  
    feesStore
      .followUpStore(form.value)
      .then(() => {
        isLoading.value = false;
        useAlert("success", "Suivi des frais créé avec succès !");
        props.refresh();
        props.close();
      })
      .catch((err) => {
        isLoading.value = false;
  
        if (err.status === 422) {
          errors.value = err.response.data.errors;
          useAlert("warning", "Vérifiez les champs obligatoires !");
        } else {
          useAlert("warning", "Une erreur s'est produite !");
        }
      });
  };
  </script>
  