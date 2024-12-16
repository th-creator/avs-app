import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL)

export const useExpansesStore = defineStore("expanses", () => {
    // Define the global state for expanses
    const expanses = ref([]);  // This will hold the expanses globally
    const financeExpanses = ref([]);  // This will hold the expanses globally

    // Fetch all expanses and update the state
    const index = async () => {
        try {
            const response = await api.get(`api/expanses`);
            expanses.value = response.data.data;  // Update the expanses state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch expanses:", error);
            return error
        }
    };

    // Fetch all expanses and update the state
    const all = async (from,to) => {
        try {
            const response = await api.get(`api/all/expanse/${from}/${to}`);
            expanses.value = response.data.data;  // Update the expanses state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch expanses:", error);
            return error
        }
    };

    const fetchFinance = async (payload) => {
        try {
            const response = await api.post('api/finance/expanses',payload);
            financeExpanses.value = response.data.data;   // Update the payments state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch payments:", error);
            return error
        }
    };
    // Store a new user and update the state
    const store = async (payload) => {
        const response = await api.post('api/expanses', payload);
        expanses.value.push(response.data.data);  // Add the new user to the expanses array
        expanses.value = [...expanses.value]; // Reassign to force reactivity
        return response
    };

    // Update an existing user and update the state
    const update = async (payload, id) => {
        const response = await api.post(`api/expanse/update/${id}`, payload);
        const index = expanses.value.findIndex(user => user.id === id);
        if (index !== -1) {
            expanses.value[index] = response.data.data;
            expanses.value = [...expanses.value]; // Reassign to force reactivity
        }

        return response
    };

    // Delete a user and update the state
    const destroy = async (id) => {
        await api.delete(`api/expanses/${id}`);
        expanses.value = expanses.value.filter(user => user.id !== id);  // Remove the deleted user from the array
        return response
    };

    // Expose the expanses state and actions
    return { expanses, index, store, update, destroy, financeExpanses, fetchFinance, all };
});
