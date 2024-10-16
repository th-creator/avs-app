import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL)

export const useGroupsStore = defineStore("groups", () => {
    // Define the global state for groups
    const groups = ref([]);  // This will hold the groups globally
    const group = ref([]);  // This will hold the groups globally
    const allPayments = ref([]);  // This will hold the groups globally
    const studentGroups = ref([]);  // This will hold the groups globally
    const totalPayments = ref(0);  // This will hold the groups globally

    // Fetch all groups and update the state
    const index = async () => {
        try {
            const response = await api.get('api/groups');
            groups.value = response.data.data;  // Update the groups state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch groups:", error);
            return error
        }
    };

    const fetchStudentGroups = async (id) => {
        try {
            const response = await api.get(`api/student/${id}/groups`);
            studentGroups.value = response.data.data;  // Update the groups state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch groups:", error);
            return error
        }
    };

    const fetchPayments = async (month) => {
        try {
            const response = await api.get(`api/group/Payments/${month}`);
            allPayments.value = response.data.data.map(res => ({
                id: res.id,
                intitule: res.intitule,
                teacher: res.teacher.firstName + ' ' + res.teacher.lastName,
                total: res.payments.reduce((total, payment) => {
                    let amount = payment.total !== null ? payment.total : Number(payment.amount)*((100-Number(payment.reduction))/100)
                    return total + Number(amount)
                }, 0),
                payments: res.payments,
            }));;  // Update the groups state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch groups:", error);
            return error
        }
    };

    const fetchgroupPayments = async (month,id) => {
        try {
            const response = await api.get(`api/group/Payments/${month}/single/${id}`);
            totalPayments.value = response.data.data.payments ? response.data.data.payments.reduce((total, payment) => {
                    let amount = payment.total !== null ? payment.total : Number(payment.amount)*((100-Number(payment.reduction))/100)
                    return total + Number(amount)
                }, 0) : 0
            return response
        } catch (error) {
            console.error("Failed to fetch groups:", error);
            return error
        }
    };

    const show = async (id) => {
        try {
            const response = await api.get(`api/groups/${id}`);
            group.value = response.data.data;  // Update the students state with the fetched data
            return response
        } catch (error) {
            console.error("Failed to fetch groups:", error);
            return error
        }
    };

    // Store a new user and update the state
    const store = async (payload) => {
        const response = await api.post('api/groups', payload);
        groups.value.push(response.data.data);  // Add the new user to the groups array
        groups.value = [...groups.value]; // Reassign to force reactivity
        return response
    };

    // Update an existing user and update the state
    const update = async (payload, id) => {
        const response = await api.put(`api/groups/${id}`, payload);
        const index = groups.value.findIndex(user => user.id === id);
        if (index !== -1) {
            groups.value[index] = response.data.data;
            groups.value = [...groups.value]; // Reassign to force reactivity
        }

        return response
    };

    // Delete a user and update the state
    const destroy = async (id) => {
        await api.delete(`api/groups/${id}`);
        groups.value = groups.value.filter(user => user.id !== id);  // Remove the deleted user from the array
        return response
    };

    // Expose the groups state and actions
    return { groups, index, store, update, destroy, show, group, allPayments, fetchPayments, fetchStudentGroups, studentGroups, totalPayments,fetchgroupPayments };
});
