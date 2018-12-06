import Vue from 'vue';
import axios from 'axios';
import VueAxios from 'vue-axios';
import authHeader from 'auth-header';
import config from 'config';

const instance = axios.create({
    baseURL: config.api,
    headers: authHeader()
});

Vue.use(VueAxios, axios);