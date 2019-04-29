<template>
    <v-container fill-height>
        <v-layout align-center justify-center>
            <v-flex class="login-form text-xs-center">
                <v-card>
                    <v-img :src="require('@/assets/jmp.svg')" @click="eggCounter++" alt="Logo" aspect-ratio="1.7"
                           v-if="!showEgg"></v-img>
                    <EggBoard v-if="showEgg"></EggBoard>
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
                                    v-on:keyup.enter="validate"
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
</template>

<script>
    import LocalChanger from '@/components/LocalChanger';
    import EggBoard from '@/components/EggBoard';

    export default {
        name: 'Login',
        components: {EggBoard, LocalChanger},
        data() {
            return {
                valid: true,
                username: '',
                password: '',
                showPassword: false,
                showEgg: false,
                eggCounter: 0,
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
        },
        watch: {
            eggCounter() {
                if (this.eggCounter >= 10) {
                    this.showEgg = true;
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
