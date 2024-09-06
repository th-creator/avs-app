import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL)

export const useTeachersStore = defineStore("teachers", () => {
    // Define the global state for teachers
    const teachers = ref([]);  // This will hold the teachers globally

    // Fetch all teachers and update the state
    const index = async () => {
        try {
            const response = await api.get('api/teachers');
            teachers.value = response.data.data;  // Update the teachers state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch teachers:", error);
            return error
        }
    };

    // Store a new user and update the state
    const store = async (payload) => {
        const response = await api.post('api/teachers', payload);
        teachers.value.push(response.data.data);  // Add the new user to the teachers array
        teachers.value = [...teachers.value]; // Reassign to force reactivity
        return response
    };

    // Update an existing user and update the state
    const update = async (payload, id) => {
        const response = await api.put(`api/teachers/${id}`, payload);
        const index = teachers.value.findIndex(user => user.id === id);
        if (index !== -1) {
            teachers.value[index] = response.data.data;
            teachers.value = [...teachers.value]; // Reassign to force reactivity
        }

        return response
    };

    // Delete a user and update the state
    const destroy = async (id) => {
        await api.delete(`api/teachers/${id}`);
        teachers.value = teachers.value.filter(user => user.id !== id);  // Remove the deleted user from the array
        return response
    };

    // Expose the teachers state and actions
    return { teachers, index, store, update, destroy };
});
