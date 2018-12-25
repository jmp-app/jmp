import {registrationStateService} from '../services';

export const registrationStates = {
    namespaced: true,
    state: {
        all: {}
    },
    actions: {
        getAll({commit}) {
            commit('getAllRequest');

            registrationStateService.getAll()
                .then(
                    registrationStates => commit('getAllSuccess', registrationStates),
                    error => commit('getAllFailure', error)
                );
        }
    },
    mutations: {
        getAllRequest(state) {
            state.all = {loading: true};
        },
        getAllSuccess(state, registrationStates) {
            state.all = {registrationStates};
            state.all.loading = false;
        },
        getAllFailure(state, error) {
            state.all = {error};
            state.all.loading = false;
        }
    }
};
