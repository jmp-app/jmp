import {eventService} from '../services';

export const events = {
    namespaced: true,
    state: {
        all: {}
    },
    actions: {
        getAll({commit}) {
            commit('getAllRequest');

            eventService.getAll()
                .then(
                    events => commit('getAllSuccess', events),
                    error => commit('getAllFailure', error)
                );
        }
    },
    mutations: {
        getAllRequest(state) {
            state.all = {loading: true};
        },
        getAllSuccess(state, events) {
            state.all = {items: events};
        },
        getAllFailure(state, error) {
            state.all = {error};
        }
    }
};
