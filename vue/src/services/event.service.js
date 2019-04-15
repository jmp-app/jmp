import Vue from 'vue';

export const eventService = {
    getAll,
    getInitialOverview,
    getNextEvents,
    getEventById,
    update
};

function getAll() {
    return Vue.axios.get('/events?all=1')
        .then(response => {
            return response.data.json();
        });
}

function getInitialOverview(showAll) {
    let url = '/events?limit=5';
    if (showAll) {
        url += '&all=1';
    }
    return Vue.axios.get(url).then(response => {
        return response.data;
    });
}

function getNextEvents(offset, showAll) {
    let url = `/events?limit=5&offset=${offset}`;
    if (showAll) {
        url += '&all=1';
    }
    return Vue.axios.get(url).then(response => {
        return response.data;
    });
}

function getEventById(eventId) {
    return Vue.axios.get(`/events/${eventId}`).then(response => {
        return response.data;
    });
}

function update(event) {
    return Vue.axios.put(`/events/${event.id}`, event).then(response => {
        return response.data;
    });
}
