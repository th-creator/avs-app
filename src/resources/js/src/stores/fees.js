import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL)

export const useFeesStore = defineStore("fees", () => {
    // Define the global state for payments
    const studentfees = ref([]);  // This will hold the payments globally
    const fees = ref([]);  // This will hold the fees globally
    const financeFees = ref([]);  // This will hold the fees globally

    // Fetch all fees and update the state
    const index = async () => {
        try {
            const response = await api.get('api/fees');
            fees.value = response.data.data;   // Update the fees state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch fees:", error);
            return error
        }
    };
    const fetchFinance = async (payload) => {
        try {
            const response = await api.post('api/finance/fees',payload);
            financeFees.value = response.data.data;   // Update the payments state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch payments:", error);
            return error
        }
    };
    // Fetch all fees and update the state
    const show = async (id,ay) => {
        try {
            const response = await api.get(`api/fees/${id}?ay=${ay}`);
            studentfees.value = response.data.data;  // Update the fees state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch fees:", error);
            return error
        }
    };
    // Fetch all fees and update the state
    const undandledFess = async (ay) => {
        try {
            const response = await api.get(`api/undandledFees?ay=${ay}`);
            fees.value = response.data.data;  // Update the fees state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch fees:", error);
            return error
        }
    };

    // Store a new user and update the state
    const store = async (payload) => {
        const response = await api.post('api/fees', payload);
        studentfees.value.push(response.data.data);  // Add the new user to the studentfees array
        studentfees.value = [...studentfees.value]; // Reassign to force reactivity
        return response
    };

    // Update an existing user and update the state
    const update = async (payload, id) => {
        const response = await api.put(`api/fees/${id}`, payload);
        console.log(response);
        const index = fees.value.findIndex(user => user.id === id);
        const secondIndex = studentfees.value.findIndex(user => user.id === id);
        
        if (index !== -1) {
            studentfees.value[index] = response.data.data;
            studentfees.value = [...studentfees.value]; // Reassign to force reactivity
        }
        
        if (secondIndex !== -1) {
            studentfees.value[secondIndex] = response.data.data;
            studentfees.value = [...studentfees.value]; // Reassign to force reactivity
        }

        return response
    };

    // Delete a user and update the state
    const destroy = async (id) => {
        await api.delete(`api/fees/${id}`);
        fees.value = fees.value.filter(user => user.id !== id);  // Remove the deleted user from the array
        studentfees.value = studentfees.value.filter(user => user.id !== id);  // Remove the deleted user from the array
        return response
    };

    // Expose the fees state and actions
    return { fees, index, store, update, destroy, studentfees, show, undandledFess, fetchFinance, financeFees };
});
