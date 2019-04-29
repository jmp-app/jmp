<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <div v-if="events">
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
                    :items="events"
                    :pagination.sync="pagination"
                    :rows-per-page-items="[10,20,50,{'text':'$vuetify.dataIterator.rowsPerPageAll','value':-1}]"
                    :search="searchQuery"
            >
                <template v-slot:items="props">
                    <tr @click="$router.push(`/events/${props.item.id}`)" class="clickable">
                        <td>{{ props.item.title }}</td>
                        <td>{{ formatDateTime(props.item.from) }}</td>
                        <td>{{ formatDateTime(props.item.to) }}</td>
                        <td>{{ props.item.eventType.title }}</td>
                    </tr>
                </template>
            </v-data-table>
        </div>
        <div class="text-xs-center" v-if="!events">
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
        name: 'adminEventOverview',
        data: function () {
            return {
                searchQuery: '',
                pagination: {
                    sortBy: 'from',
                    rowsPerPage: 10
                },
                headers: [
                    {
                        text: this.$t('event.title'),
                        align: 'left',
                        sortable: true,
                        value: 'title'
                    },
                    {
                        text: this.$t('event.from'),
                        align: 'left',
                        sortable: true,
                        value: 'from'
                    },
                    {
                        text: this.$t('event.to'),
                        align: 'left',
                        sortable: true,
                        value: 'to'
                    },
                    {
                        text: this.$t('eventType.title'),
                        align: 'left',
                        sortable: true,
                        value: 'eventType.title'
                    }
                ]
            };
        },
        computed: {
            events() {
                return this.$store.state.events.all.events;
            }
        },
        methods: {
            formatDateTime: function (dateTime) {
                return dateFormat(dateTime, 'dd.mm.yyyy hh:MM "Uhr"');
            }
        },
        mounted() {
            this.$store.dispatch('events/getAll');
        }
    };
</script>

<style scoped>

</style>
