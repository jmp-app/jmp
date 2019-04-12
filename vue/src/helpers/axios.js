import axios from 'axios';
import {userService} from '../services';
import * as config from '../../jmp.config.js';

export const http = axios.create({
    baseURL: `${config.api}/v1`
});

http.interceptors.request.use(function (request) {
    const token = JSON.parse(localStorage.getItem('token'));
    if (token) {
        request.headers['Authorization'] = `Bearer ${token}`;
    }
    return request;
});

http.interceptors.response.use(undefined, function (error) {
    if (error.response.status === 401) {
        // auto logout if 401 response returned from api
        userService.logout();
        location.reload(true);

        const errorData = (error.response.data && error.response.data.errors) || error;
        return Promise.reject(errorData);
    }
    return Promise.reject(error);
});
