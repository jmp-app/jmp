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
                <Board style="margin-top: 10px"></Board>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import LocalChanger from '@/components/LocalChanger';
    import Board from '../components/Board';

    export default {
        name: 'Login',
        components: {Board, LocalChanger},
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
