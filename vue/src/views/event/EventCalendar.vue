<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <v-container fluid id="container">
        <v-layout row v-if="isAdmin()" wrap>
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
        <v-layout wrap>
            <v-flex class="mb-3" xs12>
                <v-sheet :height="calendarHeight">
                    <v-calendar
                            :now="today"
                            :type="type"
                            :value="today"
                            color="primary"
                            ref="calendar"
                    >
                        <template v-slot:day="{ date }">
                            <template v-for="event in events[date]">
                                <CalendarMonthlyEvent :event="event" :key="event.id"
                                                      :user="user"></CalendarMonthlyEvent>
                            </template>
                        </template>
                    </v-calendar>
                </v-sheet>
            </v-flex>
            <v-flex class="text-xs-center">
                <v-select
                        :items="typeOptions"
                        label="Type"
                        v-model="type"
                ></v-select>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import CalendarMonthlyEvent from '../../components/CalendarMonthlyEvent';

    export default {
        name: 'EventCalendar',
        components: {CalendarMonthlyEvent},
        data: () => ({
            today: new Date().toISOString().slice(0, 10),
            type: 'month',
            typeOptions: [
                {text: 'Day', value: 'day'},
                {text: '4 Day', value: '4day'},
                {text: 'Week', value: 'week'},
                {text: 'Month', value: 'month'}
            ],
            showAll: false,
            showElapsed: false,
            user: JSON.parse(window.localStorage.getItem('user'))
        }),
        mounted() {
            this.reload();
        },
        computed: {
            events() {
                const map = {};
                if (this.$store.state.events.all.events) {
                    this.$store.state.events.all.events.forEach(e => (map[this.cutDate(e.from)] = map[this.cutDate(e.from)] || []).push(this.convertEvent(e)));
                }
                return map;
            },
            calendarHeight() {
                return this.$refs.container ? this.$refs.container.clientHeight + 'px' : '500px';
            }
        },
        methods: {
            convertEvent(e) {
                e.date = this.cutDate(e.from);
                e.to = this.cutDate(e.to);
                e.open = false;
                return e;
            },

            open(event) {
                alert(event.title);
            },
            isAdmin: function () {
                const user = JSON.parse(localStorage.getItem('user'));
                return !!(user && user.isAdmin);
            },
            reload: function () {
                let showAll = this.showAll;
                let showElapsed = this.showElapsed;
                this.$store.dispatch('events/getAll', {showAll, showElapsed});
            },
            cutDate: function (dateTime) {
                const [year, month, dayTime] = dateTime.split('-');
                const [day] = dayTime.split('T');
                return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            },
            cutTime: function (dateTime) {
                // eslint-disable-next-line
                const [date, time] = dateTime.split('T');
                return time;
            }
        },
        watch: {
            showAll: function () {
                this.reload();
            },
            showElapsed: function () {
                this.reload();
            }
        }
    };
</script>

<style lang="stylus" scoped>
</style>
