import Vue from 'vue';
import Vuetify, {VApp, VBtn} from 'vuetify/lib';
import 'vuetify/src/stylus/app.styl';
import de from 'vuetify/es5/locale/de';

Vue.use(Vuetify, {
    iconfont: 'md',
    lang: {
        locales: {de},
        current: 'de'
    },
    components: {
        VApp,
        VBtn
    }
});
