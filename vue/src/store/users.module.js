import {userService} from '../services';

export const users = {
    namespaced: true,
    state: {
        all: {}
    },
    actions: {
        getAll({commit}) {
            commit('getAllRequest');

            userService.getAll()
                .then(users => commit('getAllSuccess', users))
                .catch(error => commit('getAllFailure', error));
        }
    },
    mutations: {
        getAllRequest(state) {
            state.all = {loading: true};
        },
        getAllSuccess(state, users) {
            state.all = {items: users};
            state.all.loading = false;
        },
        getAllFailure(state, error) {
            state.all = {error};
            state.all.loading = false;
        }
    },
    getters: {
        getUsersFiltered: (state) => (filter) => {
            if (!state.all.items) {
                return {};
            }
            return state.all.items.filter(filter);
        }
    }
};
