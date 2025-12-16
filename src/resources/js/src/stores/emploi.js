import { defineStore } from 'pinia';
import { ref } from 'vue';
import server from "../server/index";
import { BACKEND_URL } from "../server/app";

let api = server(BACKEND_URL);

export const useEmploiStore = defineStore("emplois", () => {

    // ------------------------
    // State
    // ------------------------
    const emplois = ref([]);      // All emplois
    const emploi = ref(null);     // Single emploi
    const activeTimetable = ref([]);  // Active emploi for group
    const timetableByDate = ref([]);  // Timetable for a given date

    // ------------------------
    // Get all emplois
    // ------------------------
    const index = async (level = null, subject = null) => {
        try {
            let url = 'api/emplois';
    
            const params = [];
    
            if (level) params.push(`level=${encodeURIComponent(level)}`);
            if (subject) params.push(`subject=${encodeURIComponent(subject)}`);
    
            if (params.length > 0) {
                url += `?${params.join('&')}`;
            }
    
            const response = await api.get(url);
            emplois.value = response.data;  
            return response;
    
        } catch (error) {
            console.error("Failed to fetch emplois:", error);
            return error;
        }
    };

    // ------------------------
    // Show single emploi
    // ------------------------
    const show = async (id) => {
        try {
            const response = await api.get(`api/emplois/${id}`);
            emploi.value = response.data;
            return response;
        } catch (error) {
            console.error("Failed to fetch emploi:", error);
            return error;
        }
    };

    // ------------------------
    // Store new emploi
    // ------------------------
    const store = async (payload) => {
        try {
            const response = await api.post('api/emplois', payload);
            emplois.value.push(response.data);
            emplois.value = [...emplois.value]; // force reactivity
            return response;
        } catch (error) {
            console.error("Failed to store emploi:", error);
            return error;
        }
    };

    // ------------------------
    // Update emploi
    // ------------------------
    const update = async (payload, id) => {
        try {
            const response = await api.put(`api/emplois/${id}`, payload);
            const idx = emplois.value.findIndex(item => item.id === id);
            if (idx !== -1) {
                emplois.value[idx] = response.data;
                emplois.value = [...emplois.value]; // force reactivity
            }
            return response;
        } catch (error) {
            console.error("Failed to update emploi:", error);
            return error;
        }
    };

    // ------------------------
    // Delete emploi
    // ------------------------
    const destroy = async (id) => {
        try {
            await api.delete(`api/emplois/${id}`);
            emplois.value = emplois.value.filter(item => item.id !== id);
            return true;
        } catch (error) {
            console.error("Failed to delete emploi:", error);
            return error;
        }
    };

    // ------------------------
    // Fetch active timetable for a group
    // ------------------------
    const fetchActiveForGroup = async (groupId) => {
        try {
            const response = await api.get(`api/emplois/active/${groupId}`);
            activeTimetable.value = response.data;
            return response;
        } catch (error) {
            console.error("Failed to fetch active timetable:", error);
            return error;
        }
    };

    // ------------------------
    // Fetch timetable for a given date
    // ------------------------
    const fetchTimetableForDate = async (date) => {
        try {
            const response = await api.post('api/emplois/timetable/date', { date });
            timetableByDate.value = response.data;
            return response;
        } catch (error) {
            console.error("Failed to fetch timetable for date:", error);
            return error;
        }
    };

    // ------------------------
    // RETURN STATE + ACTIONS
    // ------------------------
    return {
        emplois,
        emploi,
        activeTimetable,
        timetableByDate,
        index,
        show,
        store,
        update,
        destroy,
        fetchActiveForGroup,
        fetchTimetableForDate
    };
});
