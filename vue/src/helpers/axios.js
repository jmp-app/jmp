import Vue from 'vue';
import axios from 'axios';
import VueAxios from 'vue-axios';
import { userService } from '../services';

Vue.use(VueAxios, axios);
axios.defaults.baseURL = `http://localhost/api/v1`;

axios.interceptors.response.use(undefined, function(error) {
    if (error.status === 401) {
        // auto logout if 401 response returned from api
        userService.logout();
        location.reload(true);

        const errorData = (error.data && error.data.errors) || error.statusText;
        return Promise.reject(errorData);
    }
    return error;
});
