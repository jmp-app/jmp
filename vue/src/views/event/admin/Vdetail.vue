<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <v-form
                lazy-validation
                ref="form"
                v-if="event"
                v-model="valid"
        >
            <v-text-field
                    :counter="50"
                    :label="$t('event.title')"
                    :readonly="display()"
                    :rules="rules.title"
                    required
                    v-model="event.title"
            ></v-text-field><!-- Title -->

            <v-textarea
                    :label="$t('event.description')"
                    :readonly="display()"
                    v-model="event.description"
            ></v-textarea><!-- Description -->

            <v-layout justify-space-between row wrap>
                <v-flex class="input-with-icon" md5 xs12>
                    <v-menu
                            :close-on-content-click="false"
                            :nudge-right="40"
                            full-width
                            lazy
                            min-width="290px"
                            offset-y
                            transition="scale-transition"
                            v-model="menu1">
                        <template v-slot:activator="{ on }">
                            <v-text-field
                                    :label="$t('event.from')"
                                    :rules="rules.from"
                                    @blur="from.parsedDate = parseDate(from.date)"
                                    prepend-icon="event"
                                    readonly
                                    required
                                    v-model="from.parsedDate"
                                    v-on="on"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                                @input="handleInputMenu1()"
                                v-if="menu1 && !display()"
                                v-model="from.date"
                        ></v-date-picker>
                    </v-menu>
                </v-flex><!-- From Date -->
                <v-flex class="input-with-icon" md5 xs12>
                    <v-menu
                            :close-on-content-click="false"
                            :nudge-right="40"
                            :return-value.sync="from.time"
                            full-width
                            lazy
                            min-width="290px"
                            offset-y
                            ref="menu2"
                            transition="scale-transition"
                            v-model="menu2">
                        <template v-slot:activator="{ on }">
                            <v-text-field
                                    :label="$t('event.from')"
                                    :rules="rules.from"
                                    prepend-icon="access_time"
                                    readonly
                                    required
                                    v-model="from.time"
                                    v-on="on"
                            ></v-text-field>
                        </template>
                        <v-time-picker
                                @click:minute="$refs.menu2.save(from.time)"
                                format="24hr"
                                full-width
                                v-if="menu2 && !display()"
                                v-model="from.time"
                        ></v-time-picker>
                    </v-menu>
                </v-flex><!-- From Time -->
            </v-layout><!-- From -->

            <v-layout justify-space-between row wrap>
                <v-flex class="input-with-icon" md5 xs12>
                    <v-menu
                            :close-on-content-click="false"
                            :nudge-right="40"
                            full-width
                            lazy
                            min-width="290px"
                            offset-y
                            transition="scale-transition"
                            v-model="menu3">
                        <template v-slot:activator="{ on }">
                            <v-text-field
                                    :label="$t('event.to')"
                                    :rules="rules.to"
                                    @blur="to.parsedDate = parseDate(to.date)"
                                    prepend-icon="event"
                                    readonly
                                    required
                                    v-model="to.parsedDate"
                                    v-on="on"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                                @input="handleInputMenu3()"
                                v-if="menu3  && !display()"
                                v-model="to.date"
                        ></v-date-picker>
                    </v-menu>
                </v-flex><!-- To Date -->
                <v-flex class="input-with-icon" md5 xs12>
                    <v-menu
                            :close-on-content-click="false"
                            :nudge-right="40"
                            :return-value.sync="to.time"
                            full-width
                            lazy
                            min-width="290px"
                            offset-y
                            ref="menu4"
                            transition="scale-transition"
                            v-model="menu4">
                        <template v-slot:activator="{ on }">
                            <v-text-field
                                    :label="$t('event.to')"
                                    :rules="rules.to"
                                    prepend-icon="access_time"
                                    readonly
                                    required
                                    v-model="to.time"
                                    v-on="on"
                            ></v-text-field>
                        </template>
                        <v-time-picker
                                @click:minute="$refs.menu4.save(to.time)"
                                format="24hr"
                                full-width
                                v-if="menu4 && !display()"
                                v-model="to.time"
                        ></v-time-picker>
                    </v-menu>
                </v-flex><!-- To Time -->
            </v-layout><!-- To -->

            <v-text-field
                    :counter="50"
                    :label="$t('event.place')"
                    :readonly="display()"
                    :rules="rules.place"
                    v-model="event.place"
            ></v-text-field><!-- Place -->

            <v-select
                    :items="eventTypes"
                    :label="$t('eventType.title')"
                    :readonly="display()"
                    :rules="rules.eventType"
                    item-text="title"
                    required
                    return-object
                    v-model="event.eventType"
            ></v-select><!-- Event Type -->

            <v-select
                    :items="registrationStates"
                    :label="$t('registration.state')"
                    :rules="rules.defaultRegistrationState"
                    item-text="name"
                    required
                    return-object
                    :readonly="display()"
                    v-model="event.defaultRegistrationState"
            ></v-select><!-- Default Registration State -->

            <!-- Buttons -->
            <div>
                <v-btn
                        :disabled="!valid"
                        @click="validate"
                        color="success"
                        v-if="create() || edit()"
                >
                    {{$t('submit')}}
                </v-btn>
                <v-btn
                        @click="prepareForEdit()"
                        color="primary"
                        v-if="display()"
                >
                    {{$t('edit')}}
                </v-btn>
                <v-btn
                        @click="reset"
                        color="error"
                        v-if="create() || edit()"
                >
                    {{$t('cancel')}}
                </v-btn>
            </div>

        </v-form>
        <div class="text-xs-center">
            <v-progress-circular
                    color="primary"
                    indeterminate
                    v-if="!event"
            ></v-progress-circular>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Vdetail',
        data: function () {
            return {
                mode: 'display', // display, edit, create
                valid: true,
                menu1: false,
                menu2: false,
                menu3: false,
                menu4: false,
                from: {},
                to: {},
                rules: {
                    title: [
                        v => !!v || `${this.$t('fieldIsRequired', {fieldname: this.$t('event.title')})}`,
                        v => (v && v.length <= 50) || `${this.$t('fieldValueLength', {
                            fieldname: this.$t('event.title'),
                            lenght: 50
                        })}`
                    ],
                    place: [
                        v => (!v || v.length <= 50) || `${this.$t('fieldValueLength', {
                            fieldname: this.$t('event.place'),
                            lenght: 50
                        })}`
                    ],
                    from: [
                        v => !!v || `${this.$t('fieldIsRequired', {fieldname: this.$t('event.from')})}`
                    ],
                    to: [
                        v => !!v || `${this.$t('fieldIsRequired', {fieldname: this.$t('event.to')})}`
                    ],
                    eventType: [
                        v => (!!v && !!v.id) || `${this.$t('fieldIsRequired', {fieldname: this.$t('eventType.title')})}`
                    ],
                    defaultRegistrationState: [
                        v => (!!v && !!v.id) || `${this.$t('fieldIsRequired', {fieldname: this.$t('registration.state')})}`
                    ]
                }
            };
        },
        computed: {
            event() {
                return this.$store.state.events.detail.event;
            },
            registrationStates() {
                return this.$store.state.registrationStates.all.registrationStates;
            },
            eventTypes() {
                return this.$store.state.eventTypes.all.eventTypes;
            }
        },
        methods: {
            create: function () {
                return (this.mode === 'create');
            },
            edit: function () {
                return (this.mode === 'edit');
            },
            display: function () {
                return (this.create() === false && this.edit() === false);
            },
            prepareForEdit: function () {
                this.mode = 'edit';
                this.closeAllMenus();
            },
            closeAllMenus: function () {
                this.menu1 = false;
                this.menu2 = false;
                this.menu3 = false;
                this.menu4 = false;
            },
            validate: function () {
                if (this.$refs.form.validate()) {
                    console.log('Submit');
                }
            },
            reset() {
                this.$refs.form.reset();
                this.getEvent();
                if (this.edit()) {
                    this.mode = 'display';
                }
                this.closeAllMenus();
            },
            getEvent: function () {
                const eventId = this.$route.params.id;
                // eslint-disable-next-line
                if (eventId == 0) {
                    this.mode = 'create';
                    this.$store.dispatch('events/getEmptyEvent');
                } else {
                    this.$store.dispatch('events/getEventById', {eventId});
                }
            },
            parseDate: function (date) {
                if (!date) return null;

                const [year, month, day] = date.split('-');
                return `${day}.${month}.${year}`;
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
            },
            handleInputMenu1: function () {
                this.menu1 = false;
                this.from.parsedDate = this.parseDate(this.from.date);
            },
            handleInputMenu3: function () {
                this.menu3 = false;
                this.to.parsedDate = this.parseDate(this.to.date);
            }
        },
        mounted() {
            this.getEvent();
            this.$store.dispatch('registrationStates/getAll');
            this.$store.dispatch('eventTypes/getAll');
        },
        watch: {
            event() {
                if (this.event && this.event.from) {
                    this.from.date = this.cutDate(this.event.from);
                    this.from.parsedDate = this.parseDate(this.from.date);
                    this.from.time = this.cutTime(this.event.from);
                }
                if (this.event && this.event.to) {
                    this.to.date = this.cutDate(this.event.to);
                    this.to.parsedDate = this.parseDate(this.to.date);
                    this.to.time = this.cutTime(this.event.to);
                }
            }
        }
    };
</script>

<style scoped>
    .input-with-icon {
        margin-left: 16px;
        margin-right: 16px;
    }
</style>
