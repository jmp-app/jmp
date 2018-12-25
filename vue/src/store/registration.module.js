import {registrationService} from '../services';

export const registration = {
    namespaced: true,
    state: {
        detail: {}
    },
    actions: {
        getRegistrationByEventIdAndUserId({commit}, {eventId, userId}) {
            commit('getRegistrationByEventIdAndUserIdRequest');

            registrationService.getRegistrationByEventIdAndUserId(eventId, userId)
                .then(
                    registration => commit('getRegistrationByEventIdAndUserIdSuccess', registration),
                    error => commit('getRegistrationByEventIdAndUserIdFailure', error)
                );
        }
    },
    mutations: {
        getRegistrationByEventIdAndUserIdRequest(state) {
            state.detail = {loading: true};
        },
        getRegistrationByEventIdAndUserIdSuccess(state, registration) {
            state.detail = {registration};
            state.detail.loading = false;
        },
        getRegistrationByEventIdAndUserIdFailure(state, error) {
            state.detail = {error};
            state.detail.loading = false;
        }
    }
};
