<template>
    <form>
        <div class="form-group">
            <label for="regState">Registration-State</label>
            <select class="form-control" id="regState">
                <option :key="regState.id" v-for="regState in registrationStates">{{regState.name}}</option>
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
            registrationStates() {
                return this.$store.state.registrationStates.all.registrationStates;
            },
            registration() {
                return this.$store.state.registration.detail.registration;
            }
        },
        created() {
            this.$store.dispatch('registrationStates/getAll');
        }
    };
</script>

<style scoped>

</style>
