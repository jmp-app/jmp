import Vue from 'vue';
import * as configJson from '../../jmp.config.js';

export const config = function() {
    Vue.mixin({
        created: function () {
            this.$config = configJson;
        }
    });
};
