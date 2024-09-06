import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL)

export const useRegistrantsStore = defineStore("registrants", () => {
    // Define the global state for registrants
    const registrants = ref([]);  // This will hold the registrants globally

    // Fetch all registrants and update the state
    const index = async () => {
        try {
            const response = await api.get('api/registrants');
            registrants.value = response.data.data.map(res => ({
                center: res.center,
                name: res.student.firstName,
                lastName: res.student.lastName,
                firstName: res.student.firstName,
                email: res.student.email,
                date: res.date,
                phone: res.student.phone,
                parent_phone: res.student.parent_phone,
                field: res.student.field,
                level: res.student.level
            }));  // Update the registrants state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch registrants:", error);
            return error
        }
    };

    // Store a new user and update the state
    const store = async (payload) => {
        const response = await api.post('api/registrants', payload);
        registrants.value.push({
            center: response.data.data.center,
            name: response.data.data.student.firstName,
            lastName: response.data.data.student.lastName,
            firstName: response.data.data.student.firstName,
            email: response.data.data.student.email,
            date: response.data.data.date,
            phone: response.data.data.student.phone,
            parent_phone: response.data.data.student.parent_phone,
            field: response.data.data.student.field,
            level: response.data.data.student.level
        });  // Add the new user to the registrants array
        registrants.value = [...registrants.value]; // Reassign to force reactivity
        return response
    };

    // Update an existing user and update the state
    const update = async (payload, id) => {
        const response = await api.put(`api/registrants/${id}`, payload);
        const index = registrants.value.findIndex(user => user.id === id);
        if (index !== -1) {
            registrants.value[index] = {
                center: response.data.data.center,
                name: response.data.data.student.firstName,
                lastName: response.data.data.student.lastName,
                firstName: response.data.data.student.firstName,
                email: response.data.data.student.email,
                date: response.data.data.date,
                phone: response.data.data.student.phone,
                parent_phone: response.data.data.student.parent_phone,
                field: response.data.data.student.field,
                level: response.data.data.student.level
            };
            registrants.value = [...registrants.value]; // Reassign to force reactivity
        }

        return response
    };

    // Delete a user and update the state
    const destroy = async (id) => {
        await api.delete(`api/registrants/${id}`);
        registrants.value = registrants.value.filter(user => user.id !== id);  // Remove the deleted user from the array
        return response
    };

    // Expose the registrants state and actions
    return { registrants, index, store, update, destroy };
});
