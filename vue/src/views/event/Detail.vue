<template>
    <div>
        <p v-if="loading">Loading...</p>
        <p v-if="!event && !loading">{{ $t("noDataFound") }}</p>
        <div v-if="event">
            <Bla :isIntegrated="true"></Bla>
            <hr class="mt-4 mb-4"/>
            <RegistrationForm v-if="registration"></RegistrationForm>
            <BottomNavigation v-if="isAdmin()"></BottomNavigation>
        </div>
    </div>
</template>

<script>
    import BottomNavigation from '@/components/BottomNavigation';
    import RegistrationForm from '@/components/RegistrationForm';
    import Bla from '@/views/event/Bla.vue';

    export default {
        name: 'Detail',
        components: {RegistrationForm, BottomNavigation, Bla},
        data: function () {
            return {
                user: {}
            };
        },
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
        methods: {
            isAdmin: function () {
                const user = JSON.parse(localStorage.getItem('user'));
                return !!(user && user.isAdmin === 1);
            }
        },
        created() {
            this.user = JSON.parse(window.localStorage.getItem('user'));
            let eventId = this.$route.params.id;
            let userId = this.user.id;

            this.$store.dispatch('events/getEventById', {eventId});

            this.$store.dispatch('registration/getRegistrationByEventIdAndUserId', {eventId, userId});
        }
    };
</script>

<style scoped>

</style>
