<template>
    <div>
        <h1 v-if="event">{{event.title}}</h1>
        <p v-if="loading">Loading...</p>
        <p v-if="!event && !loading">Not Found</p>
    </div>
</template>

<script>
    export default {
        name: 'Detail',
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
