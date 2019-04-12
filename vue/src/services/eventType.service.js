import Vue from 'vue';

export const eventTypeService = {
    getAll
};

function getAll() {
    return Vue.axios.get('/event-types')
        .then(response => {
            return response.data;
        });
}
