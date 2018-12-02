import Vue from 'vue';
import App from './App.vue';
import {router} from './helpers/router';
import {store} from './store';
import './registerServiceWorker';
import i18n from './i18n';

Vue.config.productionTip = false;

new Vue({ // eslint-disable-line no-new
    el: '#app',
    router,
    store,
    i18n,
    render: h => h(App)
});
