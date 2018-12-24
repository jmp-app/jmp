import Vue from 'vue';

export const registrationStateService = {
    getAll
};

function getAll() {
    return Vue.axios.get('/registration-state')
        .then(response => {
            return response.data.json();
        });
}
