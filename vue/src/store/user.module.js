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
        update({commit}, {user}) {
            commit('userRequest');

            userService.updadteUser(user)
                .then(user => commit('updateUserSuccess', user))
                .catch(error => commit('userRequestFailure', error));
        },
        deleteUser({commit}, {user}) {
            commit('userRequest');

            userService.deleteUser(user.id)
                .then(() => commit('reset'))
                .catch(error => commit('userRequestFailure', error));
        },
        create({commit}, {user}) {
            commit('userRequest');

            userService.createUser(user)
                .then(user => commit('createUserSuccess', user))
                .catch(error => commit('userRequestFailure', error));
        },
        reset({commit}) {
            console.log('reset commit');
            commit('reset');
        }
    },
    mutations: {
        userRequest(state) {
            state.data = {loading: true};
        },
        userRequestFailure(state, error) {
            state.data = {error};
        },
        getUserSuccess(state, user) {
            state.data = {user};
        },
        updateUserSuccess(state, user) {
            state.data = {user};
        },
        createUserSuccess(state, user) {
            state.data = {user};
        },
        reset(state) {
            Vue.set(state.data, 'user', initialState.user);
        }
    }
};
