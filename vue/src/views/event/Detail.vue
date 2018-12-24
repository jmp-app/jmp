<template>
    <div>
        <div v-if="event">
            <EventDetail :event="event"></EventDetail>
        </div>
        <p v-if="loading">Loading...</p>
        <p v-if="!event && !loading">Not Found</p>
        <BottomNavigation></BottomNavigation>
    </div>
</template>

<script>
    import EventDetail from '@/components/EventDetail';
    import BottomNavigation from '@/components/BottomNavigation';

    export default {
        name: 'Detail',
        components: {BottomNavigation, EventDetail},
        computed: {
            event() {
                return this.$store.state.events.detail.event;
            },
            loading() {
                return this.$store.state.events.detail.loading;
            }
        },
        beforeCreate() {
            let eventId = this.$route.params.id;
            this.$store.dispatch('events/getEventById', {eventId});
        }
    };
</script>

<style scoped>

</style>
