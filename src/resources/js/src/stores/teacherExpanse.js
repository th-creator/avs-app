import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL)

export const useTeacherExpansesStore = defineStore("teacherExpanses", () => {
    // Define the global state for teacherExpanses
    const teacherExpanses = ref([]);  // This will hold the teacherExpanses globally
    const financeteachersExpanses = ref([]);  // This will hold the teacherExpanses globally
    const paidExpanses = ref([]);
    const unpaidExpanses = ref([]);
    const allExpanses = ref([]);


    // Fetch all teacherExpanses and update the state
    const index = async (month, year) => {
        try {
          const response = await api.get(`api/all/teacher/expanse/${month}/${year}`);
      
          /* ======================
           * 1️⃣ PAID EXPENSES
           * ====================== */
          paidExpanses.value = response.data.data.map(e => ({
            ...e,
            status: "Payé"
          }));
      
          /* ======================
           * 2️⃣ UNPAID (GROUPS)
           * ====================== */
          unpaidExpanses.value = response.data.groups.map(group => {
            const total = group.payments.reduce((sum, payment) => {
              const value =
                payment.total !== null
                  ? Number(payment.total)
                  : Number(payment.amount) * ((100 - Number(payment.reduction)) / 100);
      
              return sum + value;
            }, 0);
      
            const percentage = 70;
            const amount = (total * percentage) / 100;
            const rest = total - amount;
      
            return {
              id: null,
              teacher: `${group.teacher?.firstName} ${group.teacher?.lastName}`,
              teacher_id: group.teacher?.id ?? null,
              group: group.intitule,
      
              total,
              percentage,
              amount,
              rest,
      
              month,
              year,
              payments: group.payments,
              status: 'En attente'
            };
          })
          .filter(expanse => expanse.total > 0);
      
          /* ======================
           * 3️⃣ ALL EXPENSES
           * ====================== */
          allExpanses.value = [
            ...paidExpanses.value,
            ...unpaidExpanses.value
          ];
      
          console.log('PAID', paidExpanses.value.length);
          console.log('UNPAID', unpaidExpanses.value.length);
          console.log('ALL', allExpanses.value.length);
      
          return true;
      
        } catch (error) {
          console.error("Failed to fetch teacherExpanses:", error);
          throw error;
        }
      };
      

    const fetchFinance = async (payload) => {
        try {
            const response = await api.post('api/finance/teacherExpanses',payload);
            financeteachersExpanses.value = response.data.data;   // Update the payments state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch payments:", error);
            return error
        }
    };
    // Store a new user and update the state
    const store = async (payload) => {
        const response = await api.post('api/teacherExpanses', payload);
        teacherExpanses.value.push(response.data.data);  // Add the new user to the teacherExpanses array
        teacherExpanses.value = [...teacherExpanses.value]; // Reassign to force reactivity
        return response
    };

    // Update an existing user and update the state
    const update = async (payload, id) => {
        const response = await api.put(`api/teacherExpanses/${id}`, payload);
        const index = teacherExpanses.value.findIndex(user => user.id === id);
        if (index !== -1) {
            teacherExpanses.value[index] = response.data.data;
            teacherExpanses.value = [...teacherExpanses.value]; // Reassign to force reactivity
        }

        return response
    };

    // Delete a user and update the state
    const destroy = async (id) => {
        await api.delete(`api/teacherExpanses/${id}`);
        teacherExpanses.value = teacherExpanses.value.filter(user => user.id !== id);  // Remove the deleted user from the array
        return response
    };

    // Expose the teacherExpanses state and actions
    return { teacherExpanses, index, store, update, destroy, financeteachersExpanses, fetchFinance, paidExpanses, unpaidExpanses, allExpanses };
});
