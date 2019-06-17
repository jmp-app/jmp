import {eventTypeService} from '../services';

const initialState = {
    eventType: {
        title: '',
        color: '#FFFFFF'
    }
};

export const eventType = {
    namespaced: true,
    state: {
        data: {
            eventType: initialState.eventType
        }
    },
    actions: {
        get({commit}, {id}) {
            commit('request');

            eventTypeService.get(id)
                .then(eventType => commit('getSuccess', eventType))
                .catch(error => commit('requestFailure', error));
        },
        update({commit}, {eventType}) {
            commit('request');

            eventTypeService.update(eventType)
                .then(eventType => commit('updateSuccess', eventType))
                .catch(error => commit('requestFailure', error));
        },
        create({commit, dispatch}, {eventType}) {
            commit('request');

            eventTypeService.create(eventType)
                .then(eventType => commit('createSuccess', eventType))
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
        delete({commit, dispatch}, {eventType}) {
            commit('request');

            eventTypeService.deleteEventType(eventType)
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
        getSuccess(state, eventType) {
            state.data = {eventType};
            state.data.loading = false;
        },
        updateSuccess(state, eventType) {
            state.data = {eventType};
            state.data.loading = false;
        },
        createSuccess(state, eventType) {
            state.data = {eventType};
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
