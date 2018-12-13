import Vue from 'vue';

export const userService = {
    login,
    logout,
    getAll,
    getUser,
    updateUser,
    createUser,
    deleteUser
};

function getAll() {
    return Vue.axios.get('/users').then(response => {
        return response.data;
    });
}

function getUser(id) {
    return Vue.axios.get(`/users/${id}`).then(response => {
        return response.data;
    });
}

function login(username, password) {
    return Vue.axios.post('/login', {
        username,
        password
    }).then(response => {
        const user = response.data;

        // login successful if there's a jwt token in the response
        if (user.token) {
            // store user details and jwt token in local storage to keep user logged in
            localStorage.setItem('user', JSON.stringify(user));
            // add token to authorization header as default
            Vue.axios.defaults.headers.common['Authorization'] = 'Bearer ' + user.token;
        }

        return user;
    });
}

function logout() {
    // remove user from local storage to log user out
    localStorage.removeItem('user');
    Vue.axios.defaults.headers.common['Authorization'] = '';
}

function createUser(user) {
    return Vue.axios.post('/users', user).then(response => {
        return response.data;
    });
}

function updateUser(user) {
    Vue.axios.put(`/users/${user.id}`, user).then(response => {
        return response.data;
    });
}

function deleteUser(id) {
    Vue.axios.delete(`/users/${id}`);
}
