import {registrationService} from '../services';

export const registration = {
    namespaced: true,
    state: {
        all: []
    },
    actions: {
        getRegistrationByEventIdAndUserId({commit}, {eventId, userId}) {
            commit('getRegistrationByEventIdAndUserIdRequest');

            registrationService.getRegistrationByEventIdAndUserId(eventId, userId)
                .then(
                    registrationState => commit('getRegistrationByEventIdAndUserIdSuccess', registrationState),
                    error => commit('getRegistrationByEventIdAndUserIdFailure', error)
                );
        }
    },
    mutations: {
        getRegistrationByEventIdAndUserIdRequest(state) {
            state = {loading: true};
        },
        getRegistrationByEventIdAndUserIdSuccess(state, registrationState) {
            state = {registrationStates: registrationState};
            state.loading = false;
        },
        getRegistrationByEventIdAndUserIdFailure(state, error) {
            state = {error};
            state.loading = false;
        }
    }
};
