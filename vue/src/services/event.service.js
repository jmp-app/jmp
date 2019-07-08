import Vue from 'vue';

export const eventService = {
    getAll,
    getInitialOverview,
    getNextEvents,
    getEventById,
    update,
    create
};

function getAll(showAll, showElapsed) {
    let url = '/events?';
    if (showAll) {
        url += '&all=1';
    }
    if (showElapsed) {
        url += '&elapsed=1';
    }
    return Vue.axios.get(url).then(response => {
        return response.data;
    });

}

function getInitialOverview(showAll, showElapsed) {
    let url = '/events?limit=5';
    if (showAll) {
        url += '&all=1';
    }
    if (showElapsed) {
        url += '&elapsed=1';
    }
    return Vue.axios.get(url).then(response => {
        return response.data;
    });
}

function getNextEvents(offset, showAll, showElapsed) {
    let url = `/events?limit=5&offset=${offset}`;
    if (showAll) {
        url += '&all=1';
    }
    if (showElapsed) {
        url += '&elapsed=1';
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

function create(event) {
    return Vue.axios.post('/events', event).then(response => {
        return response.data;
    });
}
