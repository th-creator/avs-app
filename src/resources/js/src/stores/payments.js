import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL)

export const usePaymentsStore = defineStore("payments", () => {
    // Define the global state for payments
    const studentPayments = ref([]);  // This will hold the payments globally
    const payments = ref([]);  // This will hold the payments globally

    // Fetch all payments and update the state
    const index = async () => {
        try {
            const response = await api.get('api/payments');
            payments.value = response.data.data.map(res => ({
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
            }));  // Update the payments state with the fetched data
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

    // Store a new user and update the state
    const store = async (payload) => {
        const response = await api.post('api/payments', payload);
        payments.value.push({
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
        });  // Add the new user to the payments array
        payments.value = [...payments.value]; // Reassign to force reactivity
        return response
    };

    // Update an existing user and update the state
    const update = async (payload, id) => {
        const response = await api.put(`api/payments/${id}`, payload);
        const index = payments.value.findIndex(user => user.id === id);
        if (index !== -1) {
            payments.value[index] = {
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
            payments.value = [...payments.value]; // Reassign to force reactivity
        }

        return response
    };

    // Delete a user and update the state
    const destroy = async (id) => {
        await api.delete(`api/payments/${id}`);
        payments.value = payments.value.filter(user => user.id !== id);  // Remove the deleted user from the array
        return response
    };

    // Expose the payments state and actions
    return { payments, index, store, update, destroy, studentPayments, show };
});
