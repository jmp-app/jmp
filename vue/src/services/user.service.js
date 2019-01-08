import Vue from 'vue';

export const userService = {
    login,
    logout,
    getAll,
    getUser,
    updateUser,
    createUser,
    deleteUser,
    getAllOfGroup
};

function getAll() {
    return Vue.axios.get('/users').then(response => {
        return response.data;
    });
}

function getAllOfGroup(groupId) {
    return Vue.axios.get(`/users?group=${groupId}`).then(response => {
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
            localStorage.setItem('token', JSON.stringify(user.token));
            localStorage.setItem('user', JSON.stringify(user.user));
        }

        return user;
    });
}

function logout() {
    // remove user and token from local storage to log user out
    localStorage.removeItem('user');
    localStorage.removeItem('token');
}

function createUser(user) {
    return Vue.axios.post('/users', user).then(response => {
        return response.data;
    });
}

function updateUser(user) {
    return Vue.axios.put(`/users/${user.id}`, user).then(response => {
        return response.data;
    });
}

function deleteUser(id) {
    return Vue.axios.delete(`/users/${id}`);
}
