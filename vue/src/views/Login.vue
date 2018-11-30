<template>
    <form @submit.prevent="handleSubmit">
        <div class="text-center">
            <img alt="Logo" class="img-fluid" src="../assets/jmp.svg" style="width: 30%;">
        </div>
        <div class="row">
            <h1 class="col-sm-11">{{ $t("login") }}</h1>
            <LocalChanger class="col-sm-1"/>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="username">{{ $t("user.username") }}</label>
            <div class="col-sm-9">
                <input class="form-control" id="username" type="text" v-bind:placeholder="$t('user.username')"
                       v-model="username">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="password">{{ $t("user.password") }}</label>
            <div class="col-sm-9">
                <input class="form-control" id="password" type="password" v-model="password"
                       v-bind:placeholder="$t('user.password')">
            </div>
        </div>
        <button :disabled="loggingIn" class="btn btn-primary">{{ $t("login") }}</button>
    </form>
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
                submited: false
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
                this.submited = true;
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
