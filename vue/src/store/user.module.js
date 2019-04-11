import Vue from 'vue';
import {userService} from '../services';

const initialState = {
    user: {
        username: '',
        lastname: '',
        firstname: '',
        email: '',
        isAdmin: false,
        changePassword: false
    }
};

export const user = {
    namespaced: true,
    state: {
        data: {
            user: initialState.user
        }
    },
    actions: {
        get({commit, getters, state}, {id}) {
            commit('userRequest');

            userService.getUser(id)
                .then(user => commit('getUserSuccess', user))
                .catch(error => commit('userRequestFailure', error));
        },
        update({dispatch, commit}, {user}) {
            commit('userRequest');

            userService.updateUser(user)
                .then(user => commit('updateUserSuccess', user))
                .catch(error => dispatch('alert/error', error.response.data.errors, {root: true}));
        },
        deleteUser({commit}, {user}) {
            commit('userRequest');

            userService.deleteUser(user.id)
                .then(() => commit('reset'))
                .catch(error => commit('userRequestFailure', error));
        },
        create({dispatch, commit}, {user}) {
            commit('userRequest');

            if (user.isAdmin) {
                user.isAdmin = '1';
            } else {
                user.isAdmin = '0';
            }
            if (user.passwordChange) {
                user.passwordChange = '1';
            } else {
                user.passwordChange = '0';
            }

            userService.createUser(user)
                .then(user => commit('createUserSuccess', user))
                .catch(
                    data => {
                        commit('createUserFailure', data.response.data);
                        dispatch('alert/error', data.response.data.errors, {root: true});
                    });
        },
        reset({commit}) {
            commit('reset');
        }
    },
    mutations: {
        userRequest(state) {
            state.errors = {};
            state.data = {loading: true};
        },
        userRequestFailure(state, error) {
            state.data = {error};
        },
        getUserSuccess(state, user) {
            // eslint-disable-next-line
            user.passwordChange = user.passwordChange == 1;
            // eslint-disable-next-line
            user.isAdmin = user.isAdmin == 1;
            state.data = {user};
        },
        updateUserSuccess(state, user) {
            // eslint-disable-next-line
            user.passwordChange = user.passwordChange == 1;
            state.data = {user};
        },
        createUserSuccess(state, user) {
            // eslint-disable-next-line
            user.passwordChange = user.passwordChange == 1;
            state.data = {user};
        },
        createUserFailure(state, data) {
            // eslint-disable-next-line
            const user = data.request;
            const errors = data.errors;
            state.data = {user};
            state.errors = errors;
        },
        reset(state) {
            Vue.set(state.data, 'user', initialState.user);
        }
    }
};
