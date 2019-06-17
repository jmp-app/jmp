import {eventTypeService} from '../services';

export const eventTypes = {
    namespaced: true,
    state: {
        all: {}
    },
    actions: {
        getAll({commit}) {
            commit('getAllRequest');

            eventTypeService.getAll()
                .then(
                    eventTypes => commit('getAllSuccess', eventTypes),
                    error => commit('getAllFailure', error)
                )
                .catch(error => commit('getAllFailure', error));
        }
    },
    mutations: {
        getAllRequest(state) {
            state.all = {loading: true};
        },
        getAllSuccess(state, eventTypes) {
            state.all = {eventTypes};
            state.all.loading = false;
        },
        getAllFailure(state, error) {
            state.all = {error};
            state.all.loading = false;
        }
    }
};
