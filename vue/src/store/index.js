import Vue from 'vue';
import Vuex from 'vuex';

import {alert} from './alert.module';
import {authentication} from './authentication.module';
import {users} from './users.module';
import {user} from './user.module';
import {events} from './event.module';
import {registrationStates} from './registrationState.module';
import {registration} from './registration.module';

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        alert,
        authentication,
        user,
        users,
        events,
        registrationStates,
        registration
    }
});
