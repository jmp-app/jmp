import {groupService} from '../services';
import Vue from 'vue';

const initialState = {
    group: {
        name: '',
        users: []
    }
};

export const group = {
    namespaced: true,
    state: {
        data: {
            group: initialState.group
        }
    },
    actions: {
        get({commit, getters, state}, {id}) {
            commit('groupRequest');

            groupService.getGroup(id)
                .then(group => commit('getGroupSuccess', group))
                .catch(error => commit('groupRequestFailure', error));
        },
        update({commit}, {group}) {
            commit('groupRequest');

            groupService.updateGroup(group)
                .then(group => commit('updateGroupSuccess', group))
                .catch(error => commit('groupRequestFailure', error));
        },
        delete({commit}, {group}) {
            commit('groupRequest');

            groupService.deleteGroup(group.id)
                .then(() => commit('reset'))
                .catch(error => commit('groupRequestFailure', error));
        },
        create({commit, dispatch}, {group}) {
            commit('groupRequest');

            groupService.createGroup(group)
                .then(group => commit('createGroupSuccess', group))
                .catch(error => {
                    commit('groupRequestFailure', error.response.data.errors);
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
        reset({commit}) {
            commit('reset');
        },
        join({dispatch, commit}, {groupId, userIdsToAdd}) {
            commit('groupRequest');

            groupService.joinGroup(groupId, userIdsToAdd)
                .then(
                    data => commit('getGroupSuccess', data.group),
                    error => commit(dispatch, 'join_leave_GroupFailure', error.errors)
                );
        },
        leave({dispatch, commit}, {groupId, userIdsToRemove}) {
            commit('groupRequest');
            groupService.leaveGroup(groupId, userIdsToRemove)
                .then(
                    data => commit('getGroupSuccess', data),
                    error => commit(dispatch, 'join_leave_GroupFailure', error)
                );
        }
    },
    mutations: {
        groupRequest(state) {
            state.data = {loading: true};
        },
        groupRequestFailure(state, error) {
            state.data = {error};
        },
        getGroupSuccess(state, group) {
            state.data = {group};
        },
        updateGroupSuccess(state, group) {
            state.data = {group};
        },
        createGroupSuccess(state, group) {
            state.data = {group};
        },
        join_leave_GroupFailure(dispatch, state, errors) {
            dispatch('alert/error', errors, {root: true});
        },
        reset(state) {
            Vue.set(state.data, 'group', initialState.group);
        }
    },
    getters: {
        getUsersFiltered: (state) => (filter) => {
            if (!state.data.group) {
                return {};
            } else if (!state.data.group) {
                return {};
            } else {
                return state.data.group.users.filter(filter);
            }
        }
    }
};
