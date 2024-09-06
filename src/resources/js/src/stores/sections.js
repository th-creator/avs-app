import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL)

export const useSectionsStore = defineStore("sections", () => {
    // Define the global state for sections
    const sections = ref([]);  // This will hold the sections globally

    // Fetch all sections and update the state
    const index = async () => {
        try {
            const response = await api.get('api/sections');
            sections.value = response.data.data;  // Update the sections state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch sections:", error);
            return error
        }
    };

    // Store a new user and update the state
    const store = async (payload) => {
        const response = await api.post('api/sections', payload);
        sections.value.push(response.data.data);  // Add the new user to the sections array
        sections.value = [...sections.value]; // Reassign to force reactivity
        return response
    };

    // Update an existing user and update the state
    const update = async (payload, id) => {
        const response = await api.put(`api/sections/${id}`, payload);
        const index = sections.value.findIndex(user => user.id === id);
        if (index !== -1) {
            sections.value[index] = response.data.data;
            sections.value = [...sections.value]; // Reassign to force reactivity
        }

        return response
    };

    // Delete a user and update the state
    const destroy = async (id) => {
        await api.delete(`api/sections/${id}`);
        sections.value = sections.value.filter(user => user.id !== id);  // Remove the deleted user from the array
        return response
    };

    // Expose the sections state and actions
    return { sections, index, store, update, destroy };
});
