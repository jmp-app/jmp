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
            /**
             * Counts the number of EventCards displayed on the Screen
             * @returns Number of displayed EventCards
             */
            getOffset: function () {
                return document.getElementById('eventCards').childElementCount;
            },
            /**
             * If the window is not filled completely, dispatch more Events
             */
            loadDataUntilScreenIsFull: function () {
                let lastEventCard = document.getElementById('eventCards').lastChild;
                let rect = lastEventCard.getBoundingClientRect();
                if (rect.bottom <= (window.innerHeight || document.documentElement.clientHeight)) {
                    let offset = this.getOffset();
                    this.$store.dispatch('events/getNextEvents', { offset });
                }
            },
            /**
             * Add resize listener to window which updates variable windowHeight
             */
            initWindowHeightListener: function () {
                window.addEventListener('resize', this.handleResize);
            },
            handleResize: function () {
                this.windowHeight = window.innerHeight;
            },
            /**
             * Add scroll listener to window which dispatches new Events if the bottom is arrived
             */
            initScrollListener: function () {
                window.addEventListener('scroll', this.handleScroll);
            },
            handleScroll: function () {
                let bottomOfWindow =
                    document.documentElement.scrollTop +
                    window.innerHeight === document.documentElement.offsetHeight;

                if (bottomOfWindow) {
                    let offset = this.getOffset();
                    this.$store.dispatch('events/getNextEvents', {offset});
                }
            },
            handleMutation: function (mutation) {
                if (mutation.type === 'events/getInitialOverviewSuccess') {
                    this.$nextTick(() => this.loadDataUntilScreenIsFull());
                }
            }
        },
        created() {
            this.$store.dispatch('events/getInitialOverview');
            this.$store.subscribe(mutation => {
                this.handleMutation(mutation);
            });
            this.initWindowHeightListener();
            this.initScrollListener();
        },
        watch: {
            windowHeight: function () {
                this.loadDataUntilScreenIsFull();
            }
        },
        beforeDestroy() {
            window.removeEventListener('scroll', this.handleScroll);
        }
    }
    ;
</script>

<style scoped>

</style>
