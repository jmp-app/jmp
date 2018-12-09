import {authHeader} from '../helpers';

export const eventService = {
    getAll
};

function getAll() {
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };

    return fetch('https://0145d5b8-ca05-4ec5-a442-bbdb0c585ef4.mock.pstmn.io/events', requestOptions)
        .then(response => {
            return response.json();
        })
        .catch(error => {
            return Promise.reject(error);
        });
}
