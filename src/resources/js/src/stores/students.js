import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL)

export const useStudentsStore = defineStore("students", () => {
    // Define the global state for students
    const students = ref([]);  // This will hold the students globally

    // Fetch all students and update the state
    const index = async () => {
        try {
            const response = await api.get('api/students');
            students.value = response.data.data;  // Update the students state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch students:", error);
            return error
        }
    };

    // Store a new user and update the state
    const store = async (payload) => {
        const response = await api.post('api/students', payload);
        students.value.push(response.data.data);  // Add the new user to the students array
        students.value = [...students.value]; // Reassign to force reactivity
        return response
    };

    // Update an existing user and update the state
    const update = async (payload, id) => {
        const response = await api.put(`api/students/${id}`, payload);
        const index = students.value.findIndex(user => user.id === id);
        if (index !== -1) {
            students.value[index] = response.data.data;
            students.value = [...students.value]; // Reassign to force reactivity
        }

        return response
    };

    // Delete a user and update the state
    const destroy = async (id) => {
        await api.delete(`api/students/${id}`);
        students.value = students.value.filter(user => user.id !== id);  // Remove the deleted user from the array
        return response
    };

    // Expose the students state and actions
    return { students, index, store, update, destroy };
});
