import Vue from 'vue';

export const registrationService = {
    getRegistrationByEventIdAndUserId
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
