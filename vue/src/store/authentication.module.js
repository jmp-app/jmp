import {userService} from '../services';
import {router} from '../helpers';

const user = JSON.parse(localStorage.getItem('user'));
const initialState = user
    ? {status: {loggedIn: true}, user}
    : {status: {}, user: null};

export const authentication = {
    namespaced: true,
    state: initialState,
    actions: {
        login({dispatch, commit}, {username, password}) {
            commit('loginRequest', {username});

            userService.login(username, password)
                .then(
                    user => {
                        commit('loginSuccess', user);
                        if (user.user.passwordChange) {
                            // if password change is true then redirect user to change password
                            router.push('/change-password');
                        } else {
                            router.push('/');
                        }
                    },
                    error => {
                        commit('loginFailure', error.response.data.errors.authentication.toString());
                        dispatch('alert/error', error.response.data.errors.authentication.toString(), {root: true});
                    }
                );
        },
        logout({commit}) {
            userService.logout();
            commit('logout');
        }
    },
    mutations: {
        loginRequest(state, user) {
            state.status = {loggingIn: true};
            state.user = user;
        },
        loginSuccess(state, user) {
            state.status = {loggedIn: true};
            state.user = user;
        },
        loginFailure(state) {
            state.status = {};
            state.user = null;
        },
        logout(state) {
            state.status = {};
            state.user = null;
        }
    }
};
