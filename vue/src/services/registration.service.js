import Vue from 'vue';

export const registrationService = {
    getRegistrationByEventIdAndUserId,
    updateRegistration
};

function getRegistrationByEventIdAndUserId(eventId, userId) {
    return Vue.axios.post(`/registration`, {
        'eventId': eventId,
        'userId': userId
    })
        .then(response => {
            return response.data;
        });
}

function updateRegistration(eventId, userId, registrationStateId, reason) {
    return Vue.axios.put(`/registration/${eventId}/${userId}`, {
        'registrationState': registrationStateId,
        'reason': reason
    })
        .then(response => {
            return response.data;
        });
}
