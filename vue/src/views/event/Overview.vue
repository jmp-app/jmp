<template>
    <v-container fluid>
        <v-layout row v-if="isAdmin() && events" wrap>
            <v-flex md6 sm6 xs12>
                <v-checkbox
                        :label="$t('event.overview.showAll')"
                        v-model="showAll"
                ></v-checkbox>
            </v-flex>
            <v-flex md6 sm6 xs12>
                <v-checkbox
                        :label="$t('event.overview.showElapsed')"
                        v-model="showElapsed"
                ></v-checkbox>
            </v-flex>
        </v-layout>
        <v-container fluid grid-list-md>
            <v-layout id="eventCards" row wrap>
                <v-flex :key="event.id" v-for="event in events" xs12>
                    <EventCard :event="event"/>
                </v-flex>
            </v-layout>
        </v-container>
        <div class="text-xs-center" v-if="loading">
            <v-progress-circular
                    color="primary"
                    indeterminate
            ></v-progress-circular>
        </div>
    </v-container>
</template>

<script>
    import EventCard from '@/components/EventCard';

    export default {
        name: 'Overview',
        components: {EventCard},
        computed: {
            events() {
                return this.$store.state.events.overview.events;
            },
            loading() {
                return this.$store.state.events.overview.loading;
            }
        },
        data() {
            return {
                windowHeight: window.innerHeight,
                showAll: false,
                showElapsed: false,
                unsubscribe() {
                }
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
                if (lastEventCard) {
                    let rect = lastEventCard.getBoundingClientRect();
                    if (rect.bottom <= (window.innerHeight || document.documentElement.clientHeight)) {
                        let offset = this.getOffset();
                        let showAll = this.showAll;
                        let showElapsed = this.showElapsed;
                        this.$store.dispatch('events/getNextEvents', {offset, showAll, showElapsed});
                    }
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
            /**
             * Handle scroll events
             */
            handleScroll: function () {
                let bottomOfWindow =
                    document.documentElement.scrollTop +
                    window.innerHeight === document.documentElement.offsetHeight;

                if (bottomOfWindow) {
                    let offset = this.getOffset();
                    let showAll = this.showAll;
                    let showElapsed = this.showElapsed;
                    this.$store.dispatch('events/getNextEvents', {offset, showAll, showElapsed});
                }
            },
            /**
             * Handle mutations
             * @param mutation
             */
            handleMutation: function (mutation) {
                if (mutation.type === 'events/getInitialOverviewSuccess') {
                    this.$nextTick(() => this.loadDataUntilScreenIsFull());
                }
            },
            isAdmin: function () {
                const user = JSON.parse(localStorage.getItem('user'));
                return !!(user && user.isAdmin);
            },
            init: function () {
                let showAll = this.showAll;
                let showElapsed = this.showElapsed;
                this.$store.dispatch('events/getInitialOverview', {showAll, showElapsed});
            }
        },
        created() {
            this.init();
            this.unsubscribe = this.$store.subscribe(mutation => {
                this.handleMutation(mutation);
            });
            this.initWindowHeightListener();
            this.initScrollListener();
        },
        watch: {
            windowHeight: function () {
                this.loadDataUntilScreenIsFull();
            },
            showAll: function () {
                this.init();
            },
            showElapsed: function () {
                this.init();
            }
        },
        beforeDestroy() {
            window.removeEventListener('scroll', this.handleScroll);
            window.removeEventListener('resize', this.handleResize);
            this.unsubscribe();
        }
    }
    ;
</script>

<style scoped>

</style>
