import Vue from 'vue';

export const registrationStateService = {
    getAll,
    get,
    update,
    create,
    deleteEventType
};

function getAll() {
    return Vue.axios.get('/registration-state')
        .then(response => {
            return response.data;
        });
}

function get(id) {
    return Vue.axios.get(`/registration-state/${id}`)
        .then(response => {
            return response.data;
        });
}

function update(registrationState) {
    return Vue.axios.put(`/registration-state/${registrationState.id}`, registrationState)
        .then(response => {
            return response.data;
        });
}

function create(registrationState) {
    return Vue.axios.post('/registration-state', registrationState)
        .then(response => {
            return response.data;
        });
}

function deleteEventType(registrationState) {
    return Vue.axios.delete(`/registration-state/${registrationState.id}`);
}
