import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL)

export const usePaymentsStore = defineStore("payments", () => {
    // Define the global state for payments
    const studentPayments = ref([]);  // This will hold the payments globally
    const payments = ref([]);  // This will hold the payments globally
    const allPayments = ref([]);  // This will hold the payments globally
    const groupPayments = ref([]);  // This will hold the payments globally
    const financePayments = ref([]);  // This will hold the payments globally

    // Fetch all payments and update the state
    const index = async () => {
        try {
            const response = await api.get('api/payments');
            payments.value = response.data.data;   // Update the payments state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch payments:", error);
            return error
        }
    };
    const all = async () => {
        try {
            const response = await api.get('api/all/paymens');
            allPayments.value = response.data.data;   // Update the payments state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch payments:", error);
            return error
        }
    };
    const fetchFinance = async (payload) => {
        try {
            const response = await api.post('api/finance/payments',payload);
            financePayments.value = response.data.data;   // Update the payments state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch payments:", error);
            return error
        }
    };
    // Fetch all payments and update the state
    const show = async (id) => {
        try {
            const response = await api.get(`api/payments/${id}`);
            studentPayments.value = response.data.data;  // Update the payments state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch payments:", error);
            return error
        }
    };
    // Fetch all payments and update the state
    const fetchGroupPayments = async (id,month,year) => {
        try {
            const response = await api.get(`api/group/${id}/payments/${month}/${year}`);
            groupPayments.value = response.data.data;  // Update the payments state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch payments:", error);
            return error
        }
    };

    // Store a new user and update the state
    const store = async (payload) => {
        const response = await api.post('api/payments', payload);
        payments.value.push(response.data.data);  // Add the new user to the payments array
        payments.value = [...payments.value]; // Reassign to force reactivity
        studentPayments.value.push(response.data.data);  // Add the new user to the studentPayments array
        studentPayments.value = [...studentPayments.value]; // Reassign to force reactivity
        return response
    };

    // Update an existing user and update the state
    const update = async (payload, id) => {
        const response = await api.put(`api/payments/${id}`, payload);
        const index = payments.value.findIndex(user => user.id === id);
        const secondIndex = studentPayments.value.findIndex(user => user.id === id);
        
        if (index !== -1) {
            payments.value[index] = response.data.data;
            payments.value = [...payments.value]; // Reassign to force reactivity
        }
        if (secondIndex !== -1) {
            studentPayments.value[secondIndex] = response.data.data;
            studentPayments.value = [...studentPayments.value]; // Reassign to force reactivity
        }

        return response
    };

    // Delete a user and update the state
    const destroy = async (id) => {
        await api.delete(`api/payments/${id}`);
        payments.value = payments.value.filter(user => user.id !== id);  // Remove the deleted user from the array
        studentPayments.value = studentPayments.value.filter(user => user.id !== id);  // Remove the deleted user from the array
        return response
    };

    // Expose the payments state and actions
    return { payments, index, store, update, destroy, studentPayments, show, fetchGroupPayments, groupPayments, allPayments, all, financePayments, fetchFinance };
});
