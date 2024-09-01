import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL)

export const useUsersStore = defineStore("users", () => {
    // Define the global state for users
    const users = ref([]);  // This will hold the users globally

    // Fetch all users and update the state
    const index = async () => {
        try {
            const response = await api.get('api/users');
            users.value = response.data.data;  // Update the users state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch users:", error);
            return error
        }
    };

    // Store a new user and update the state
    const store = async (payload) => {
        const response = await api.post('api/users', payload);
        users.value.push(response.data.data);  // Add the new user to the users array
        users.value = [...users.value]; // Reassign to force reactivity
        return response
    };

    // Update an existing user and update the state
    const update = async (payload, id) => {
        const response = await api.put(`api/users/${id}`, payload);
        const index = users.value.findIndex(user => user.id === id);
        if (index !== -1) {
            users.value[index] = response.data.data;
            users.value = [...users.value]; // Reassign to force reactivity
        }

        return response
    };
    const toggle = async (payload, id) => {
        const response = await api.put(`api/users/${id}/toggle`, payload);
        const index = users.value.findIndex(user => user.id === id);
        if (index !== -1) {
            users.value[index] = response.data.data;
            users.value = [...users.value]; // Reassign to force reactivity
        }

        return response
    };

    // Delete a user and update the state
    const destroy = async (id) => {
        await api.delete(`api/users/${id}`);
        users.value = users.value.filter(user => user.id !== id);  // Remove the deleted user from the array
        return response
    };

    // Expose the users state and actions
    return { users, index, store, update, destroy, toggle };
});
