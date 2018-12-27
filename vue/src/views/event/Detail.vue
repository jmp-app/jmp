<template>
    <div>
        <p v-if="loading">Loading...</p>
        <p v-if="!event && !loading">Not Found</p>
        <div v-if="event">
            <EventDetail :event="event"></EventDetail>
            <hr class="mt-4 mb-4"/>
            <RegistrationForm v-if="registration"></RegistrationForm>
            <BottomNavigation></BottomNavigation>
        </div>
    </div>
</template>

<script>
    import EventDetail from '@/components/EventDetail';
    import BottomNavigation from '@/components/BottomNavigation';
    import RegistrationForm from '@/components/RegistrationForm';

    export default {
        name: 'Detail',
        components: {RegistrationForm, BottomNavigation, EventDetail},
        computed: {
            event() {
                return this.$store.state.events.detail.event;
            },
            loading() {
                return this.$store.state.events.detail.loading;
            },
            registration() {
                return this.$store.state.registration.detail.registration;
            }
        },
        beforeCreate() {
            let eventId = this.$route.params.id;
            this.$store.dispatch('events/getEventById', {eventId});

            let userId = this.$store.state.authentication.user.id;
            this.$store.dispatch('registration/getRegistrationByEventIdAndUserId', {eventId, userId});
        }
    };
</script>

<style scoped>

</style>
