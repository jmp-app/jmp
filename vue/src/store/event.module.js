import {eventService} from '../services';

export const events = {
    namespaced: true,
    state: {
        all: {},
        overview: {},
        detail: {}
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
        getInitialOverview({commit}, {showAll}) {
            commit('getInitialOverviewRequest');

            eventService.getInitialOverview(showAll)
                .then(
                    events => commit('getInitialOverviewSuccess', events),
                    error => commit('getInitialOverviewFailure', error)
                );
        },
        getNextEvents({commit}, {offset, showAll}) {
            commit('getNextEventsRequest');

            eventService.getNextEvents(offset, showAll)
                .then(
                    events => commit('getNextEventsSuccess', events),
                    error => commit('getNextEventsFailure', error)
                );
        },
        getEventById({commit}, {eventId}) {
            commit('getEventByIdRequest');

            eventService.getEventById(eventId)
                .then(
                    event => commit('getEventByIdSuccess', event),
                    error => commit('getEventByIdFailure', error)
                );
        }
    },
    mutations: {
        getAllRequest(state) {
            state.all = {loading: true};
        },
        getAllSuccess(state, events) {
            state.all = {items: events};
            state.all.loading = false;
        },
        getAllFailure(state, error) {
            state.all = {error};
            state.all.loading = false;
        },
        getInitialOverviewRequest(state) {
            state.overview = {loading: true};
        },
        getInitialOverviewSuccess(state, events) {
            state.overview = {events};
            state.overview.loading = false;
        },
        getInitialOverviewFailure(state, error) {
            state.overview = {error};
            state.overview.loading = false;
        },
        getNextEventsRequest(state) {
            state.overview.loading = true;
        },
        getNextEventsSuccess(state, events) {
            if (state.overview.events) {
                state.overview.events.push(...events);
            } else {
                state.overview = {events};
            }
            state.overview.loading = false;
        },
        getNextEventsFailure(state, error) {
            state.overview.error = error;
            state.overview.loading = false;
        },
        getEventByIdRequest(state) {
            state.detail = {loading: true};
        },
        getEventByIdSuccess(state, event) {
            state.detail = {event};
            state.detail.loading = false;
        },
        getEventByIdFailure(state, error) {
            state.detail = {error};
            state.detail.loading = false;
        }
    }
};
