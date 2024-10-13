import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL)

export const useTeacherExpansesStore = defineStore("teacherExpanses", () => {
    // Define the global state for teacherExpanses
    const teacherExpanses = ref([]);  // This will hold the teacherExpanses globally
    const financeteachersExpanses = ref([]);  // This will hold the teacherExpanses globally

    // Fetch all teacherExpanses and update the state
    const index = async () => {
        try {
            const response = await api.get('api/teacherExpanses');
            teacherExpanses.value = response.data.data;  // Update the teacherExpanses state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch teacherExpanses:", error);
            return error
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
    return { teacherExpanses, index, store, update, destroy, financeteachersExpanses, fetchFinance };
});
