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
        },
        getInitialOverview({commit}) {
            commit('getInitialOverviewRequest');

            eventService.getInitialOverview()
                .then(
                    events => commit('getInitialOverviewSuccess', events),
                    error => commit('getInitialOverviewFailure', error)
                );
        },
        getNextEvents({commit}, {offset}) {
            commit('getNextEventsRequest', {offset});

            eventService.getNextEvents(offset)
                .then(
                    events => commit('getNextEventsSuccess', events),
                    error => commit('getNextEventsFailure', error)
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
        },
        getInitialOverviewRequest(state) {
            state.all = {loading: true};
        },
        getInitialOverviewSuccess(state, events) {
            state.all = {items: events};
        },
        getInitialOverviewFailure(state, error) {
            state.all = {error};
        },
        getNextEventsRequest(state) {
            state.all.loading = true;
        },
        getNextEventsSuccess(state, events) {
            if (state.all.items) {
                state.all.items.push(...events);
            } else {
                console.log('new Array');
                state.all = {items: events};
            }
        },
        getNextEventsFailure(state, error) {
            state.all.error = error;
        }
    }
};
