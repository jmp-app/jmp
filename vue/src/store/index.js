import Vue from 'vue';
import Vuex from 'vuex';

import {alert} from './alert.module';
import {authentication} from './authentication.module';
import {users} from './users.module';
import {user} from './user.module';
import {events} from './event.module';
import {registrationStates} from './registrationState.module';
import {eventTypes} from './eventTypes.module';
import {eventType} from './eventType.module';
import {registration} from './registration.module';
import {groups} from './groups.module';
import {group} from './group.module';

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        alert,
        authentication,
        user,
        users,
        events,
        registrationStates,
        eventTypes,
        eventType,
        registration,
        groups,
        group
    }
});
