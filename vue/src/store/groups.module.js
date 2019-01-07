import {groupService} from '../services';

export const groups = {
    namespaced: true,
    state: {
        all: {}
    },
    actions: {
        getAll({commit}) {
            commit('getAllRequest');

            groupService.getAll()
                .then(groups => commit('getAllSuccess', groups))
                .catch(error => commit('getAllFailure', error));
        }
    },
    mutations: {
        getAllRequest(state) {
            state.all = {loading: true};
        },
        getAllSuccess(state, groups) {
            state.all = {items: groups};
        },
        getAllFailure(state, error) {
            state.all = {error};
        }
    },
    getters: {
        getGroupsFiltered: (state) => (filter) => {
            if (!state.all.items) {
                return {};
            } else {
                return state.all.items.filter(filter);
            }
        }
    }
};
