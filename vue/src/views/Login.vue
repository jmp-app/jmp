<template>
    <div>
        <form @submit.prevent="handleSubmit" class="mb-3">
            <div class="text-center">
                <img alt="Logo" class="img-fluid" src="../assets/jmp.svg" style="width: 30%;">
            </div>
            <div class="row">
                <h1 class="col-sm-10">{{ $t("login") }}</h1>
                <LocalChanger class="col-sm-2"/>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="username">{{ $t("user.username") }}</label>
                <div class="col-sm-9">
                    <input class="form-control" :class="{ 'is-invalid': submitted && !username }" id="username"
                           type="text" v-bind:placeholder="$t('user.username')" v-model="username"/>
                    <div v-show="submitted && !username" class="invalid-feedback">{{ $t("login.usernameRequired") }}</div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="password">{{ $t("user.password") }}</label>
                <div class="col-sm-9">
                    <input class="form-control" :class="{ 'is-invalid': submitted && !password }" id="password"
                           type="password" v-model="password"
                           v-bind:placeholder="$t('user.password')"/>
                    <div v-show="submitted && !password" class="invalid-feedback">{{ $t("login.passwordRequired") }}</div>

                </div>
            </div>
            <button :disabled="loggingIn" class="btn btn-primary">
                {{ $t("login") }}
                <img v-show="loggingIn"
                     src="data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA=="/>
            </button>
        </form>
    </div>
</template>

<script>
    import LocalChanger from '@/components/local-changer';

    export default {
        name: 'Login',
        components: {LocalChanger},
        data() {
            return {
                username: '',
                password: '',
                submitted: false
            };
        },
        computed: {
            loggingIn() {
                return this.$store.state.authentication.status.loggingIn;
            }
        },
        created() {
            // reset login status
            this.$store.dispatch('authentication/logout');
        },
        methods: {
            handleSubmit(e) {
                this.submitted = true;
                const {username, password} = this;
                const {dispatch} = this.$store;
                if (username && password) {
                    dispatch('authentication/login', {username, password});
                }
            }
        }
    };
</script>

<style scoped>

</style>
