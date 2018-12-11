<template>
    <div id="eventCards">
        <div :key="event.id" v-for="event in events['items']">
            <EventCard :event="event"/>
        </div>
    </div>
</template>

<script>
    import EventCard from '@/components/EventCard';

    export default {
        name: 'Overview',
        components: {EventCard},
        computed: {
            events() {
                return this.$store.state.events.all;
            }
        },
        data() {
            return {
                windowHeight: window.innerHeight
            };
        },
        methods: {
            getOffset: function () {
                return document.getElementById('eventCards').childElementCount;
            },
            loadDataUntilScreenIsFull: function () {
                let lastEventCard = document.getElementById('eventCards').lastChild;
                let rect = lastEventCard.getBoundingClientRect();
                if (rect.bottom <= (window.innerHeight || document.documentElement.clientHeight)) {
                    let offset = this.getOffset();
                    this.$store.dispatch('events/getNextEvents', { offset });
                }
            },
            initWindowHeightListener: function () {
                window.addEventListener('resize', () => {
                    this.windowHeight = window.innerHeight;
                });
            },
            initScrollListener: function () {
                window.addEventListener('scroll', () => {
                    let bottomOfWindow =
                        document.documentElement.scrollTop +
                        window.innerHeight === document.documentElement.offsetHeight;

                    if (bottomOfWindow) {
                        let offset = this.getOffset();
                        this.$store.dispatch('events/getNextEvents', {offset});
                    }
                });
            }
        },
        created() {
            this.$store.dispatch('events/getInitialOverview');
            this.$store.subscribe(mutation => {
                if (mutation.type === 'events/getInitialOverviewSuccess') {
                    this.$nextTick(() => this.loadDataUntilScreenIsFull());
                }
            });
        },
        mounted() {
            this.initWindowHeightListener();
            this.initScrollListener();
        },
        watch: {
            windowHeight: function () {
                this.loadDataUntilScreenIsFull();
            }
        }
    }
    ;
</script>

<style scoped>

</style>
