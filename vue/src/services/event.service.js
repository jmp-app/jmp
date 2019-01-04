import Vue from 'vue';

export const eventService = {
    getAll,
    getInitialOverview,
    getNextEvents,
    getEventById
};

function getAll() {
    return Vue.axios.get('/events')
        .then(response => {
            return response.data.json();
        });
}

function getInitialOverview() {
    return Vue.axios.get('/events?limit=5').then(response => {
        return response.data;
    });
}

function getNextEvents(offset) {
    return Vue.axios.get(`/events?limit=5&offset=${offset}`).then(response => {
        return response.data;
    });
}

function getEventById(eventId) {
    return Vue.axios.get(`/events/${eventId}`).then(response => {
        return response.data;
    });
}
