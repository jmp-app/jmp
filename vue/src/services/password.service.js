import Vue from 'vue';

export const passwordService = {
    changePassword
};

function changePassword(currentPassword, newPassword) {
    return Vue.axios.put('/user/change-password', {
        password: currentPassword,
        newPassword: newPassword
    }).then(response => {
        // eslint-disable-next-line
        if (response.status == 204) {
            return true;
        } else {
            return response.data;
        }
    });
}
