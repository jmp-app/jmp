<template>
    <v-layout justify-center row>
        <v-card ref="form">
            <v-card-title>
                <span class="headline">{{ $t('password.changeYourPw') }}</span>
            </v-card-title>
            <v-card-text>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex xs12>
                            <v-text-field
                                    :append-icon="showCurrentPassword ? 'visibility' : 'visibility_off'"
                                    :label="$t('password.current')"
                                    :rules="[rules.required]"
                                    :type="showCurrentPassword ? 'text' : 'password'"
                                    @click:append="showCurrentPassword = !showCurrentPassword"
                                    ref="currentPassword"
                                    v-model="currentPassword"
                            ></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field
                                    :append-icon="showNewPassword ? 'visibility' : 'visibility_off'"
                                    :label="$t('password.new')"
                                    :rules="[rules.required, rules.min]"
                                    :type="showNewPassword ? 'text' : 'password'"
                                    @click:append="showNewPassword = !showNewPassword"
                                    ref="newPassword"
                                    v-model="newPassword"
                            ></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field
                                    :append-icon="showRepeatPassword ? 'visibility' : 'visibility_off'"
                                    :label="$t('password.repeat')"
                                    :rules="[rules.required, rules.min, rules.match(repeatPassword, newPassword)]"
                                    :type="showRepeatPassword ? 'text' : 'password'"
                                    @click:append="showRepeatPassword = !showRepeatPassword"
                                    ref="repeatPassword"
                                    v-model="repeatPassword"
                            ></v-text-field>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="changePassword()" color="blue darken-1" flat>{{ $t('password.change') }}</v-btn>
            </v-card-actions>
        </v-card>
    </v-layout>
</template>

<script>
    import {passwordService} from '../services';

    export default {
        name: 'ChangePassword',
        data() {
            return {
                currentPassword: '',
                newPassword: '',
                repeatPassword: '',
                showRepeatPassword: false,
                showCurrentPassword: false,
                showNewPassword: false,
                formHasErrors: false,
                rules: {
                    required: value => !!value || this.$t('required'),
                    min: v => v.length >= 8 || `${this.$t('toShort')}: Min 8`,
                    match: (v1, v2) => v1 === v2 || this.$t('password.noMatch')
                }
            };
        },
        computed: {
            form() {
                return {
                    currentPassword: this.currentPassword,
                    newPassword: this.newPassword,
                    repeatPassword: this.repeatPassword
                };
            }
        },
        methods: {
            changePassword: async function () {
                this.formHasErrors = false;

                Object.keys(this.form).forEach(f => {
                    if (!this.form[f]) this.formHasErrors = true;
                    this.$refs[f].validate(true);
                });
                if (!this.formHasErrors) {
                    try {
                        const response = await passwordService.changePassword(this.currentPassword, this.newPassword);
                        if (response) {
                            if (window.history.length > 1) {
                                this.$router.go(-1);
                            } else {
                                this.$router.push('/');
                            }
                        }
                    } catch (error) {
                        // clear inputs
                        this.resetForm();

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
            },
            resetForm() {
                this.formHasErrors = false;

                Object.keys(this.form).forEach(f => {
                    this.$refs[f].reset();
                });

                this.currentPassword = '';
                this.newPassword = '';
                this.repeatPassword = '';
            }
        }
    };
</script>
