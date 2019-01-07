<template>
    <form @submit.prevent="handleSubmit">
        <div class="form-group">
            <label for="regState">{{ $t("registration.state") }}</label>
            <select @change="handleChange()" class="form-control" id="regState" v-model="selected">
                <option :key="regState.id" v-bind:value="regState" v-for="regState in registrationStates">
                    {{regState.name}}
                </option>
            </select>
        </div>
        <div class="form-group" v-if="selected.reasonRequired">
            <label for="reason">{{ $t("registration.reason") }}</label>
            <input :class="{ 'is-invalid': submitted && !reason.replace(/\s/g, '').length > 0 }" class="form-control"
                   id="reason" type="text"
                   v-bind:placeholder="$t('registration.reasonPlaceholder')" v-model="reason">
            <div class="invalid-feedback" v-show="submitted && !reason.replace(/\s/g, '').length > 0">{{
                $t("registration.reasonRequired") }}
            </div>
        </div>
        <button class="btn btn-danger" v-show="hasChanges">{{ $t("submit") }}</button>
    </form>
</template>

<script>
    export default {
        name: 'RegistrationForm',
        data: function () {
            return {
                selected: this.$store.state.registration.detail.registration.registrationState,
                reason: this.$store.state.registration.detail.registration.reason,
                hasChanges: false,
                submitted: false
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
            handleSubmit: function () {
                this.submitted = true;
                let eventId = this.$store.state.events.detail.event.id;
                let userId = JSON.parse(window.localStorage.getItem('user')).id;
                let registrationStateId = this.selected.id;
                let reason = this.reason.trim();
                if (this.selected.reasonRequired) {
                    if (this.reason.replace(/\s/g, '').length > 0) {
                        this.$store.dispatch('registration/updateRegistration', {
                            eventId,
                            userId,
                            registrationStateId,
                            reason
                        });
                    }
                } else {
                    this.$store.dispatch('registration/updateRegistration', {
                        eventId,
                        userId,
                        registrationStateId,
                        reason
                    });
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
