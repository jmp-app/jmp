import {extendedRegistrationService} from '../services/extendedRegistration.service';

export const extendedRegistration = {
    namespaced: true,
    state: {
        all: {
            registration: {
                event: {},
                registrations: []
            }
        }
    },
    actions: {
        getRegistrationsFromEvent({commit}, {id}) {
            commit('getRegistrationsFromEventRequest');

            extendedRegistrationService.getRegistrationsFromEvent(id)
                .then(
                    registration => commit('getRegistrationsFromEventSuccess', registration),
                    error => commit('getRegistrationsFromEventFailure', error)
                );
        }
    },
    mutations: {
        getRegistrationsFromEventRequest(state) {
            state.all = {loading: true};
        },
        getRegistrationsFromEventSuccess(state, registration) {
            state.all = {registration};
            state.all.loading = false;
        },
        getRegistrationsFromEventFailure(state, error) {
            state.all = {error};
            state.all.loading = false;
        }
    }
};
