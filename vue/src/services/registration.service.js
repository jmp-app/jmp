import Vue from 'vue';

export const registrationService = {
    getRegistrationByEventIdAndUserId
};

function getRegistrationByEventIdAndUserId(eventId, userId) {
    return Vue.axios.get(`/registration/${eventId}/${userId}`)
        .then(response => {
            return response.data;
        });
}
