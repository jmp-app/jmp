import Vue from 'vue';

export const passwordService = {
    changePassword
};

function changePassword(currentPassword, newPassword) {
    return Vue.axios.put('/user/change-password', {
        password: currentPassword,
        newPassword: newPassword
    }).then(response => {
        return response.data;
    });
}
