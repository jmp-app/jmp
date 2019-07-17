<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
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
                :items="event"
                :pagination.sync="pagination"
                :rows-per-page-items="[10,20,50,{'text':'$vuetify.dataIterator.rowsPerPageAll','value':-1}]"
                :search="searchQuery"
        >
            <template v-slot:items="props">
                <tr>
                    <td>{{ props.item.registration.firstname }}</td>
                    <td>{{ props.item.registration.lastname }}</td>
                    <td>{{ props.item.registration.registrationState.name }}</td>
                </tr>
            </template>
        </v-data-table>
    </div>
</template>

<script>
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
                return this.$store.state.extendedRegistration.all.registration.event;
            }
        },
        mounted() {
            const id = this.$route.params.id;
            this.$store.dispatch('extendedRegistration/getRegistrationsFromEvent', {id});
        }
    };
</script>

<style scoped>

</style>
