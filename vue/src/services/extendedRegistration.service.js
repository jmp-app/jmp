import Vue from 'vue';

export const extendedRegistrationService = {
    getRegistrationsFromEvent
};

function getRegistrationsFromEvent(id) {
    return Vue.axios.get(`/events/${id}/registrations`)
        .then(response => {
            return response.data;
        });
}
