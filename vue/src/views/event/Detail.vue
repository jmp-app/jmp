<template>
    <div>
        <p v-if="loading">Loading...</p>
        <p v-if="!event && !loading">Not Found</p>
        <div v-if="event">
            <EventDetail :event="event"></EventDetail>
            <hr class="mt-4 mb-4"/>
            <form>
                <div class="form-group">
                    <label for="regState">Registration-State</label>
                    <select class="form-control" id="regState">
                        <option>Registration state</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="reason">Reason</label>
                    <input class="form-control" id="reason" placeholder="Enter reason" type="text">
                </div>
                <button class="btn btn-danger" type="button">Submit</button>
            </form>
            <BottomNavigation></BottomNavigation>
        </div>
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
