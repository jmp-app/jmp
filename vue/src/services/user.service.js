import Vue from 'vue';

export const userService = {
    login,
    logout,
    getAll
};

function getAll() {
    return Vue.axios.get('/users')
        .then(response => {
            // TODO: Check for no permission (403)
            return response.data;
        });
}

function login(username, password) {
    return Vue.$http.post('/login', {
        username,
        password
    }).then(response => {
        const user = response.data;

        // login successful if there's a jwt token in the response
        if (user.token) {
            // store user details and jwt token in local storage to keep user logged in
            localStorage.setItem('user', user);
            // add token to authorization header as default
            Vue.$http.defaults.headers.common['Authorization'] = user.token;
        }

        return user;
    });
}

function logout() {
    // remove user from local storage to log user out
    localStorage.removeItem('user');
}
