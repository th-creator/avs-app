import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL)

export const useRegistrantsStore = defineStore("registrants", () => {
    // Define the global state for registrants
    const registrants = ref([]);  // This will hold the registrants globally
    const groupRegistrants = ref([]);  // This will hold the students globally

    // Fetch all registrants and update the state
    const index = async () => {
        try {
            const response = await api.get('api/registrants');
            registrants.value = response.data.data.map(res => ({
                id: res.id,
                center: res.center,
                status: res.status,
                name: res.student.firstName,
                group_id: res.group_id,
                student_id: res.student_id,
                lastName: res.student.lastName,
                firstName: res.student.firstName,
                email: res.student.email,
                group: res.group.intitule,
                date: res.date,
                phone: res.student.phone,
                parent_phone: res.student.parent_phone,
                field: res.student.field,
                level: res.student.level,
                user: res.user
            }));  // Update the registrants state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch registrants:", error);
            return error
        }
    };
    const show = async (id) => {
        try {
            const response = await api.get(`api/registrants/${id}`);
            return response.data.data
        } catch (error) {
            console.error("Failed to fetch registrants:", error);
            return error
        }
    };

    // Store a new user and update the state
    const store = async (payload) => {
        const response = await api.post('api/registrants', payload);
        
        registrants.value.push({
            id: response.data.data.id,
            center: response.data.data.center,
            name: response.data.data.student.firstName,
            lastName: response.data.data.student.lastName,
            firstName: response.data.data.student.firstName,
            group_id: response.group_id,
            student_id: response.student_id,
            status: response.data.data.status,
            email: response.data.data.student.email,
            group: response.data.data.group.intitule,
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
                id: response.data.data.id,
                center: response.data.data.center,
                status: response.data.data.status,
                name: response.data.data.student.firstName,
                lastName: response.data.data.student.lastName,
                firstName: response.data.data.student.firstName,
                group_id: response.group_id,
                student_id: response.student_id,
                email: response.data.data.student.email,
                date: response.data.data.date,
                phone: response.data.data.student.phone,
                group: response.data.data.group.intitule,
                parent_phone: response.data.data.student.parent_phone,
                field: response.data.data.student.field,
                level: response.data.data.student.level
            };
            registrants.value = [...registrants.value]; // Reassign to force reactivity
        }

        return response
    };
    // Update an existing user and update the state
    const transfer = async (payload, id) => {
        const response = await api.post(`api/registrant/${id}/transfer`, payload);
        const index = registrants.value.findIndex(user => user.id === id);
        if (index !== -1) {
            registrants.value[index] = {
                id: response.data.data.id,
                center: response.data.data.center,
                status: response.data.data.status,
                name: response.data.data.student.firstName,
                lastName: response.data.data.student.lastName,
                firstName: response.data.data.student.firstName,
                group_id: response.group_id,
                student_id: response.student_id,
                email: response.data.data.student.email,
                date: response.data.data.date,
                phone: response.data.data.student.phone,
                group: response.data.data.group.intitule,
                parent_phone: response.data.data.student.parent_phone,
                field: response.data.data.student.field,
                level: response.data.data.student.level
            };
            registrants.value = [...registrants.value]; // Reassign to force reactivity
        }

        return response
    };
    const fetchGroupRegistrants = async (id) => {
        try {
            const response = await api.get(`api/group/${id}/registrants`);
            groupRegistrants.value = response.data.data.map(res => ({
                id: res.id,
                center: res.center,
                group_id: res.group_id,
                student_id: res.student_id,
                lastName: res.student.lastName,
                firstName: res.student.firstName,
                student: res.student,
                date: res.date,
                phone: res.student.phone,
                rest: res.payments.reduce((total, payment) => total + (payment.paid == 1 ? Number(payment.rest) : Number(payment.amount)*((100-Number(payment.reduction))/100)), 0)
            }));  // Update the students state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch students:", error);
            return error
        }
    };

    const toggle = async (payload, id) => {
        const response = await api.put(`api/registrant/${id}/toggle`, payload);
        const index = registrants.value.findIndex(user => user.id === id);
        if (index !== -1) {
            registrants.value[index] = {
                id: response.data.data.id,
                center: response.data.data.center,
                status: response.data.data.status,
                name: response.data.data.student.firstName,
                lastName: response.data.data.student.lastName,
                firstName: response.data.data.student.firstName,
                group_id: response.group_id,
                student_id: response.student_id,
                email: response.data.data.student.email,
                date: response.data.data.date,
                phone: response.data.data.student.phone,
                group: response.data.data.group.intitule,
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
    return { registrants, index, store, update, destroy, groupRegistrants, fetchGroupRegistrants, toggle, show, transfer };
});
