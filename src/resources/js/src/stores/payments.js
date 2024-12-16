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
    const checkPayments = ref([]);  // This will hold the payments globally

    // Fetch all payments and update the state
    const undandled = async (month, year) => {
        try {
            const response = await api.get(`api/unhandled/paymens/${month}/${year}`);
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
    const fetchFacturation = async (payload) => {
        try {
            const response = await api.post('api/facturation/payments',payload);
            financePayments.value = response.data.data;   // Update the payments state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch payments:", error);
            return error
        }
    };
    const registrantPayment = async (id,group_id) => {
        try {
            const response = await api.get(`api/payment/registrant/${id}/${group_id}`);
            return response
        } catch (error) {
            console.error("Failed to fetch payments:", error);
            return error
        }
    };
    // Fetch all payments and update the state
    const show = async (id,month,year) => {
        try {
            const response = await api.get(`api/student/payments/${id}/${month}/${year}`);
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

    // Fetch all payments and update the state
    const fetchchecks = async (from,to) => {
        try {
            const response = await api.get(`api/check/payment/${from}/${to}`);
            checkPayments.value = response.data.data;  // Update the payments state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch payments:", error);
            return error
        }
    };

    // Store a new user and update the state
    const store = async (payload,group = null) => {
        if (group) {
            studentPayments.value = studentPayments.value.filter(user => user.group !== group);
        }
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
    return { payments, undandled, store, update, destroy, studentPayments, show, fetchGroupPayments, groupPayments, allPayments, all, financePayments, fetchFinance, fetchFacturation, registrantPayment, checkPayments, fetchchecks };
});
