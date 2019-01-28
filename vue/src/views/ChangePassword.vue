<template>
    <div class="d-flex align-items-center flex-column my-5">
        <form @submit.prevent="changePassword" class="card" id="change-password" ref="form">
            <div class="card-header">
                <span class="h5">{{ $t('password.changeYourPw') }}</span>
            </div>
            <div class="card-body">
                <input :placeholder="$t('password.current')" class="form-control mb-1" minlength="8"
                       name="current-password" type="password" v-model="currentPassword">
                <hr>
                <input :placeholder="$t('password.new')" class="form-control mb-2" minlength="8" name="new-password"
                       type="password" v-model="newPassword">
                <input :placeholder="$t('password.repeat')" class="form-control" minlength="8" name="repeat-password"
                       ref="field" type="password" v-model="repeatPassword">
            </div>
            <div class="card-footer">
                <button class="btn btn-block btn-primary" type="submit">{{ $t('password.change') }}</button>
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
