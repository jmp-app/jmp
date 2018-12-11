import {authHeader} from '../helpers';

export const eventService = {
    getAll,
    getInitialOverview,
    getNextEvents
};

function getAll() {
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };

    return fetch('api/v1/events', requestOptions)
        .then(response => {
            return response.json();
        })
        .catch(error => {
            return Promise.reject(error);
        });
}

function getInitialOverview() {
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };

    return fetch('api/v1/events?limit=5', requestOptions)
        .then(response => {
            return response.json();
        })
        .catch(error => {
            return Promise.reject(error);
        });
}

function getNextEvents(offset) {
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };

    return fetch('api/v1/events?limit=5&offset=' + offset, requestOptions)
        .then(response => {
            return response.json();
        })
        .catch(error => {
            return Promise.reject(error);
        });
}
