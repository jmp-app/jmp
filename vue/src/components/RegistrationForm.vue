<template>
    <v-form
            lazy-validation
            ref="form"
            v-if="registrationStates"
            v-model="valid"
    >
        <v-select
                :items="registrationStates"
                :label="$t('registration.state')"
                :rules="rules.registrationState"
                @change="handleChange()"
                item-text="name"
                return-object
                v-model="selected"
        ></v-select>
        <v-text-field
                :label="$t('registration.reason')"
                :rules="rules.reason"
                v-if="reasonRequired()"
                v-model="reason"
        ></v-text-field>
        <v-btn :disabled="!valid" @click="validate()" color="error" v-show="hasChanges">
            {{ $t('submit') }}
        </v-btn>
    </v-form>
</template>

<script>
    export default {
        name: 'RegistrationForm',
        props: {
            defaultRegistrationState: {},
            event: {},
            user: {}
        },
        data: function () {
            return {
                selected: this.$store.state.registration.detail.registration.registrationState,
                reason: this.$store.state.registration.detail.registration.reason,
                hasChanges: false,
                valid: true,
                rules: {
                    registrationState: [
                        v => (!!v && !!v.id) || `${this.$t('fieldIsRequired', {fieldname: this.$t('registration.state')})}`
                    ],
                    reason: [
                        v => !!v || `${this.$t('fieldIsRequired', {fieldname: this.$t('registration.reason')})}`
                    ]
                }
            };
        },
        computed: {
            registrationStates() {
                return this.$store.state.registrationStates.all.registrationStates;
            },
            registration() {
                return this.$store.state.registration.detail.registration;
            }
        },
        methods: {
            handleChange: function () {
                this.submitted = false;
                if (this.selected.id !== this.registration.registrationState.id) {
                    this.hasChanges = true;
                    this.reason = '';
                } else {
                    this.hasChanges = false;
                    this.reason = this.registration.reason;
                }
            },
            validate: function () {
                if (this.$refs.form.validate()) {
                    this.handleSubmit();
                }
            },
            handleSubmit: function () {
                let eventId = this.event ? this.event.id : this.$store.state.events.detail.event.id;
                let userId = this.user ? this.user.id : JSON.parse(window.localStorage.getItem('user')).id;
                let registrationStateId = this.selected.id;
                let reason = this.reason.trim();
                if (this.reasonRequired()) {
                    if (this.reason.replace(/\s/g, '').length > 0) {
                        this.$store.dispatch('registration/updateRegistration', {
                            eventId,
                            userId,
                            registrationStateId,
                            reason
                        });
                    }
                } else {
                    if (this.selected.reasonRequired) {
                        reason = this.selected.name;
                    }
                    this.$store.dispatch('registration/updateRegistration', {
                        eventId,
                        userId,
                        registrationStateId,
                        reason
                    });
                }
            },
            reasonRequired: function () {
                if (this.defaultRegistrationState.id === this.selected.id) {
                    return false;
                } else {
                    return this.selected.reasonRequired;
                }
            }
        },
        watch: {
            reason: function () {
                this.hasChanges = this.reason !== this.registration.reason;
            }
        },
        beforeMount() {
            this.$store.dispatch('registrationStates/getAll');
        }
    };
</script>

<style scoped>

</style>
