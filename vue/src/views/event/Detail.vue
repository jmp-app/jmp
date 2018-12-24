<template>
    <div>
        <div v-if="event">
            <EventDetail :event="event"></EventDetail>
        </div>
        <p v-if="loading">Loading...</p>
        <p v-if="!event && !loading">Not Found</p>
    </div>
</template>

<script>
    import EventDetail from '@/components/EventDetail';

    export default {
        name: 'Detail',
        components: {EventDetail},
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
