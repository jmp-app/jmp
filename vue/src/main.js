import Vue from 'vue';
import App from './App.vue';
import {router, config} from './helpers';
import {store} from './store';
import './registerServiceWorker';
import i18n from './i18n';

Vue.config.productionTip = false;

new Vue({ // eslint-disable-line no-new
    el: '#app',
    router,
    store,
    i18n,
    config,
    render: h => h(App)
});
