import Vue from 'vue';

export const eventService = {
    getAll,
    getInitialOverview,
    getNextEvents
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
