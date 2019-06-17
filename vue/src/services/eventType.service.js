import Vue from 'vue';

export const eventTypeService = {
    getAll,
    get,
    update,
    create,
    deleteEventType
};

function getAll() {
    return Vue.axios.get('/event-types')
        .then(response => {
            return response.data;
        });
}

function get(id) {
    return Vue.axios.get(`/event-types/${id}`)
        .then(response => {
            return response.data;
        });
}

function update(eventType) {
    return Vue.axios.put(`/event-types/${eventType.id}`, eventType)
        .then(response => {
            return response.data;
        });
}

function create(eventType) {
    return Vue.axios.post('/event-types', eventType)
        .then(response => {
            return response.data;
        });
}

function deleteEventType(eventType) {
    return Vue.axios.delete(`/event-types/${eventType.id}`);
}
