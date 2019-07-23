<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <div v-if="!loading">
            <v-card>
                <v-card-title primary-title>
                    <div>
                        <h3 class="headline mb-0">{{event.title}}</h3>
                    </div>
                </v-card-title>
                <v-card-text>
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
                </v-card-text>
            </v-card>
            <v-layout mb-4>
                <v-text-field
                        :label="$t('search')"
                        append-icon="search"
                        hide-details
                        single-line
                        v-model="searchQuery"
                ></v-text-field>
            </v-layout>
            <v-data-table
                    :headers="headers"
                    :items="registrations"
                    :pagination.sync="pagination"
                    :rows-per-page-items="[10,20,50,{'text':'$vuetify.dataIterator.rowsPerPageAll','value':-1}]"
                    :search="searchQuery"
            >
                <template v-slot:items="props">
                    <tr>
                        <td>{{ props.item.firstname }}</td>
                        <td>{{ props.item.lastname }}</td>
                        <td>{{ props.item.registration.registrationState.name }}</td>
                        <td>{{ props.item.registration.reason }}</td>
                    </tr>
                </template>
            </v-data-table>
        </div>
        <div class="text-xs-center" v-if="loading">
            <v-progress-circular
                    color="primary"
                    indeterminate
            ></v-progress-circular>
        </div>
    </div>
</template>

<script>
    import dateFormat from 'dateformat';

    export default {
        name: 'adminEventRegistrations',
        data: function () {
            return {
                expand: false,
                searchQuery: '',
                pagination: {
                    sortBy: 'registrationState.name',
                    rowsPerPage: 10
                },
                headers: [
                    {
                        text: this.$t('user.firstName'),
                        align: 'left',
                        sortable: true,
                        value: 'firstname'
                    },
                    {
                        text: this.$t('user.lastName'),
                        align: 'left',
                        sortable: true,
                        value: 'lastname'
                    },
                    {
                        text: this.$t('registration.state'),
                        align: 'left',
                        sortable: true,
                        value: 'registrationState.name'
                    },
                    {
                        text: this.$t('registration.reason'),
                        align: 'left',
                        sortable: true,
                        value: 'reason'
                    }
                ]
            };
        },
        computed: {
            event() {
                return this.$store.state.extendedRegistration.all.registration ? this.$store.state.extendedRegistration.all.registration.event : {};
            },
            registrations() {
                return this.$store.state.extendedRegistration.all.registration ? this.$store.state.extendedRegistration.all.registration.registrations : [];
            },
            loading() {
                return this.$store.state.extendedRegistration.all.loading;
            }
        },
        mounted() {
            const id = this.$route.params.id;
            this.$store.dispatch('extendedRegistration/getRegistrationsFromEvent', {id});
        },
        methods: {
            formatDateTime: function (dateTime) {
                return dateFormat(dateTime, 'dd.mm.yyyy hh:MM "Uhr"');
            }
        }
    };
</script>

<style scoped>

</style>
