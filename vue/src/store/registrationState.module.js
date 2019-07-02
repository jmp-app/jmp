import {registrationStateService} from '../services';

const initialState = {
    registrationState: {
        name: '',
        reasonRequired: true
    }
};

export const registrationState = {
    namespaced: true,
    state: {
        data: {
            registrationState: initialState.registrationState
        }
    },
    actions: {
        get({commit}, {id}) {
            commit('request');

            registrationStateService.get(id)
                .then(registrationState => commit('getSuccess', registrationState))
                .catch(error => commit('requestFailure', error));
        },
        update({commit}, {registrationState}) {
            commit('request');

            registrationStateService.update(registrationState)
                .then(registrationState => commit('updateSuccess', registrationState))
                .catch(error => commit('requestFailure', error));
        },
        create({commit, dispatch}, {registrationState}) {
            commit('request');

            registrationStateService.create(registrationState)
                .then(registrationState => commit('createSuccess', registrationState))
                .catch(error => {
                    commit('requestFailure', error.response.data.errors);
                    let message = '';
                    const errors = error.response.data.errors;
                    for (const error in errors) {
                        if (errors.hasOwnProperty(error)) {
                            message += `${error}: ${errors[error]}`;
                        }
                    }
                    dispatch('alert/error', message, {root: true});
                    commit('reset');
                });
        },
        delete({commit, dispatch}, {registrationState}) {
            commit('request');

            registrationStateService.deleteEventType(registrationState)
                .then(commit('deleteSuccess'))
                .catch(error => {
                    commit('requestFailure', error.response.data.errors);
                    let message = '';
                    const errors = error.response.data.errors;
                    for (const error in errors) {
                        if (errors.hasOwnProperty(error)) {
                            message += `${error}: ${errors[error]}`;
                        }
                    }
                    dispatch('alert/error', message, {root: true});
                    commit('reset');
                });
        }
    },
    mutations: {
        request(state) {
            state.data = {loading: true};
        },
        getSuccess(state, registrationState) {
            state.data = {registrationState};
            state.data.loading = false;
        },
        updateSuccess(state, registrationState) {
            state.data = {registrationState};
            state.data.loading = false;
        },
        createSuccess(state, registrationState) {
            state.data = {registrationState};
            state.data.loading = false;
        },
        deleteSuccess(state) {
            state.data.loading = false;
        },
        requestFailure(state, error) {
            state.data = {error};
            state.data.loading = false;
        }
    }
};
