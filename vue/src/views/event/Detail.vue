<template>
    <div>
        <div class="text-xs-center" v-if="loading">
            <v-progress-circular
                    color="primary"
                    indeterminate
            ></v-progress-circular>
        </div>
        <p v-if="!event && !loading">{{ $t("noDataFound") }}</p>
        <div v-if="event">
            <EventDetail :event="event"></EventDetail>
            <hr class="mt-4 mb-4"/>
            <RegistrationForm :defaultRegistrationState="event.defaultRegistrationState"
                              v-if="registration"></RegistrationForm>
            <div class="text-xs-center" v-if="!registration">
                <v-progress-circular
                        color="primary"
                        indeterminate
                ></v-progress-circular>
            </div>
        </div>
    </div>
</template>

<script>
    import EventDetail from '@/components/EventDetail';
    import RegistrationForm from '@/components/RegistrationForm';

    export default {
        name: 'Detail',
        components: {RegistrationForm, EventDetail},
        data: function () {
            return {
                user: {},
                isAdmin: false
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
        created() {
            this.user = JSON.parse(window.localStorage.getItem('user'));
            this.isAdmin = this.user.isAdmin;
            let eventId = this.$route.params.id;
            let userId = this.user.id;

            this.$store.dispatch('events/getEventById', {eventId});

            this.$store.dispatch('registration/getRegistrationByEventIdAndUserId', {eventId, userId});
        }
    };
</script>

<style scoped>

</style>
