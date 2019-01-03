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
                    <div class="invalid-feedback" v-show="submitted && !username">{{ $t("login.usernameRequired") }}
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="password">{{ $t("user.password") }}</label>
                <div class="col-sm-9">
                    <input
                            :class="{ 'is-invalid': submitted && (!password || password.length < 6)}"
                            class="form-control"
                            id="password"
                            type="password"
                            v-bind:placeholder="$t('user.password')"
                            v-model="password"
                    />
                    <div class="invalid-feedback" v-show="submitted && !password">
                        {{ $t("login.passwordRequired") }}
                    </div>
                    <div class="invalid-feedback" v-show="submitted && password.length < 6">
                        {{ $t("login.passwordToShort") }}
                    </div>

                </div>
            </div>
            <button :disabled="loggingIn" class="btn btn-primary">
                {{ $t("login") }}
                <img height="24px" src="../assets/Rolling-1s-200px.gif" v-show="loggingIn"/>
            </button>
        </form>
    </div>
</template>

<script>
    import LocalChanger from '@/components/LocalChanger';

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
                if (username && password && password.length >= 6) {
                    dispatch('authentication/login', {username, password});
                }
            }
        }
    };
</script>

<style scoped>

</style>
