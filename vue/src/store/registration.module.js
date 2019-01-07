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
        },
        updateRegistration({dispatch, commit}, {eventId, userId, registrationStateId, reason}) {
            commit('updateRegistrationRequest');

            registrationService.updateRegistration(eventId, userId, registrationStateId, reason)
                .then(
                    registration => commit('updateRegistrationSuccess', registration),
                    error => {
                        dispatch('alert/error', error.response.data.errors, {root: true});
                    }
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
        },
        updateRegistrationRequest(state) {
            state.detail = {loading: true};
        },
        updateRegistrationSuccess(state, registration) {
            state.detail = {registration};
            state.detail.loading = false;
        },
        updateRegistrationFailure(state, error) {
            state.detail = {error};
            state.detail.loading = false;
        }
    }
};
