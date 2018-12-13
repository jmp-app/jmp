import axios from 'axios';
import { userService } from '../services';
import * as config from '../../jmp.config.js';

export const http = axios.create({
    baseURL: `http://${config.api}/v1`
});

http.interceptors.response.use(undefined, function(error) {
    if (error.status === 401) {
        // auto logout if 401 response returned from api
        userService.logout();
        location.reload(true);

        const errorData = (error.data && error.data.errors) || error.statusText;
        return Promise.reject(errorData);
    }
    return error;
});
