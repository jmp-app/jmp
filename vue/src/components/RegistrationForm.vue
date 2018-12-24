<template>
    <form>
        <div class="form-group">
            <label for="regState">Registration-State</label>
            <select :key="regState.id" class="form-control" id="regState" v-for="regState in registrationStates">
                <option>{{regState.title}}</option>
            </select>
        </div>
        <div class="form-group">
            <label for="reason">Reason</label>
            <input class="form-control" id="reason" placeholder="Enter reason" type="text">
        </div>
        <button class="btn btn-danger" type="button">Submit</button>
    </form>
</template>

<script>
    export default {
        name: 'RegistrationForm',
        props: {
            eventId: Number
        },
        computed: {
            registration() {
                return this.$store.state.registration;
            },
            registrationStates() {
                return this.$store.state.registrationStates.all.registrationStates;
            }
        },
        created() {
            let eventId = this.eventId;
            let userId = this.$store.state.authentication.user.id;
            this.$store.dispatch('registration/getRegistrationByEventIdAndUserId', {eventId, userId});
            this.$store.dispatch('registrationStates/getAll');
        }
    };
</script>

<style scoped>

</style>
