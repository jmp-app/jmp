<template>
    <div>
        <!--<div :key="event.id" v-for="event in events">-->
        <div :key="event.id.value" v-for="event in events">
            <EventCard :event="event"/>
        </div>
    </div>
</template>

<script>
    import EventCard from '@/components/EventCard';
    import axios from 'axios';

    export default {
        name: 'Overview',
        components: {EventCard},
        data: function () {
            return {
                events: []
            };
        },
        methods: {
            getInitialData: function () {
                for (var i = 0; i < 5; i++) {
                    axios.get(`https://randomuser.me/api/`)
                        .then(response => {
                            this.events.push(response.data.results[0]);
                        });
                }
            },
            scroll() {
                window.onscroll = () => {
                    let bottomOfWindow =
                        document.documentElement.scrollTop +
                        window.innerHeight === document.documentElement.offsetHeight;

                    if (bottomOfWindow) {
                        axios.get(`https://randomuser.me/api/`)
                            .then(response => {
                                this.events.push(response.data.results[0]);
                            });
                    }
                };
            }
        },
        beforeMount() {
            this.getInitialData();
        },
        mounted() {
            this.scroll();
        }
    };
</script>

<style scoped>

</style>
