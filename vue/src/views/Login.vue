<template>
    <v-container fill-height>
        <v-layout align-center justify-center>
            <v-flex class="login-form text-xs-center">
                <v-card>
                    <v-img :src="require('@/assets/jmp.svg')" alt="Logo" aspect-ratio="1.7"></v-img>
                    <v-card-text>
                        <v-form
                                lazy-validation
                                ref="form"
                                v-model="valid"
                        >
                            <v-text-field
                                    :label="$t('user.username')"
                                    :rules="rules.username"
                                    required
                                    v-model="username"
                            ></v-text-field>
                            <v-text-field
                                    :append-icon="showPassword ? 'visibility' : 'visibility_off'"
                                    :label="$t('user.password')"
                                    :rules="rules.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    @click:append="showPassword = !showPassword"
                                    required
                                    v-model="password"
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn
                                :disabled="!valid"
                                @click="validate"
                                color="#111a8e"
                                style="color: white;"
                        >
                            {{$t('login')}}
                        </v-btn>
                        <v-spacer></v-spacer>
                        <LocalChanger></LocalChanger>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
    <!--        <form @submit.prevent="handleSubmit" class="mb-3">-->
    <!--            <div class="text-center">-->
    <!--                <img alt="Logo" class="img-fluid" src="../assets/jmp.svg" style="width: 30%;">-->
    <!--            </div>-->
    <!--            <div class="row">-->
    <!--                <h1 class="col-sm-10">{{ $t("login") }}</h1>-->
    <!--                <LocalChanger class="col-sm-2"/>-->
    <!--            </div>-->
    <!--            <div class="form-group row">-->
    <!--                <label class="col-sm-3 col-form-label" for="username">{{ $t("user.username") }}</label>-->
    <!--                <div class="col-sm-9">-->
    <!--                    <input-->
    <!--                            :class="{ 'is-invalid': submitted && !username }"-->
    <!--                            class="form-control"-->
    <!--                            id="username"-->
    <!--                            type="text"-->
    <!--                            v-bind:placeholder="$t('user.username')"-->
    <!--                            v-model="username"-->
    <!--                    />-->
    <!--                    <div class="invalid-feedback" v-show="submitted && !username">-->
    <!--                        {{ $t("login.usernameRequired") }}-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            <div class="form-group row">-->
    <!--                <label class="col-sm-3 col-form-label" for="password">{{ $t("user.password") }}</label>-->
    <!--                <div class="col-sm-9">-->
    <!--                    <input-->
    <!--                            :class="{ 'is-invalid': submitted && (!password || password.length < 8)}"-->
    <!--                            class="form-control"-->
    <!--                            id="password"-->
    <!--                            type="password"-->
    <!--                            v-bind:placeholder="$t('user.password')"-->
    <!--                            v-model="password"-->
    <!--                    />-->
    <!--                    <div class="invalid-feedback" v-show="submitted && !password">-->
    <!--                        {{ $t("login.passwordRequired") }}-->
    <!--                    </div>-->
    <!--                    <div class="invalid-feedback" v-show="submitted && password && password.length < 8">-->
    <!--                        {{ $t("login.passwordToShort") }}-->
    <!--                    </div>-->

    <!--                </div>-->
    <!--            </div>-->
    <!--            <button :disabled="loggingIn" class="btn btn-primary">-->
    <!--                {{ $t("login") }}-->
    <!--                <img height="24px" src="../assets/Rolling-1s-200px.gif" v-show="loggingIn"/>-->
    <!--            </button>-->
    <!--        </form>-->
</template>

<script>
    import LocalChanger from '@/components/LocalChanger';

    export default {
        name: 'Login',
        components: {LocalChanger},
        data() {
            return {
                valid: true,
                username: '',
                password: '',
                showPassword: false,
                rules: {
                    username: [
                        v => !!v || `${this.$t('fieldIsRequired', {fieldname: this.$t('user.username')})}`
                    ],
                    password: [
                        v => !!v || `${this.$t('fieldIsRequired', {fieldname: this.$t('user.password')})}`,
                        v => (v && v.length >= 8) || `${this.$t('fieldValueToShort', {
                            fieldname: this.$t('user.password'),
                            lenght: 8
                        })}`
                    ]
                },
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
            handleSubmit() {
                const {username, password} = this;
                this.$store.dispatch('authentication/login', {username, password});
            },
            validate: function () {
                if (this.$refs.form.validate()) {
                    this.handleSubmit();
                }
            }
        }
    };
</script>

<style scoped>
    .login-form {
        max-width: 500px;
    }
</style>
