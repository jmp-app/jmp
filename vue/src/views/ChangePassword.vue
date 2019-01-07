<template>
    <div class="d-flex align-items-center flex-column my-5">
        <form  @submit.prevent="changePassword" ref="form" class="card" id="change-password">
            <div class="card-header">
                <span class="h5">Change your password</span>
            </div>
            <div class="card-body">
                <input type="password" name="current-password" class="form-control mb-1" placeholder="Current Password" minlength="8" v-model="currentPassword">
                <hr>
                <input type="password" name="new-password" class="form-control mb-2" placeholder="New Password" minlength="8" v-model="newPassword">
                <input type="password" name="repeat-password" class="form-control" placeholder="Repeat Password" minlength="8" v-model="repeatPassword" ref="field">
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-block btn-primary">Change Password</button>
            </div>
        </form>
    </div>
</template>

<script>
    import {passwordService} from '../services';

    export default {
        name: 'ChangePassword',
        data() {
            return {
                currentPassword: '',
                newPassword: '',
                repeatPassword: ''
            };
        },
        methods: {
            changePassword: async function () {
                if (this.newPassword !== this.repeatPassword) {
                    this.$refs.field.setCustomValidity('The two passwords must match!');
                } else {
                    this.$refs.field.setCustomValidity('');
                    try {
                        const response = await passwordService.changePassword(this.currentPassword, this.newPassword);
                        if (response.success) {
                            if (window.history.length > 1) {
                                this.$router.go(-1);
                            } else {
                                this.$router.push('/');
                            }
                        }
                    } catch (error) {
                        // clear inputs
                        this.$refs.form.reset();

                        // show error alert
                        let message = '';
                        const errors = error.response.data.errors;
                        for (const error in errors) {
                            if (errors.hasOwnProperty(error)) {
                                message += `${error}: ${errors[error]}`;
                            }
                        }
                        this.$store.dispatch('alert/error', message, {root: true});
                    }
                }
            }
        }
    };
</script>

<style scoped>
    #change-password {
        max-width: 40rem;
    }
</style>
