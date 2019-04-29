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
                :expand="expand"
                :headers="headers"
                :items="registrations"
                :pagination.sync="pagination"
                :rows-per-page-items="[10,20,50,{'text':'$vuetify.dataIterator.rowsPerPageAll','value':-1}]"
                :search="searchQuery"
                item-key="userId"
        >
            <template v-slot:items="props">
                <tr :class="{ clickable: props.item.reason }" @click="props.expanded = !props.expanded">
                    <td>{{ props.item.firstname }}</td>
                    <td>{{ props.item.lastname }}</td>
                    <td>{{ props.item.registrationState.name }}</td>
                </tr>
            </template>
            <template v-slot:expand="props">
                <v-card flat v-if="props.item.reason">
                    <v-card-text>{{ props.item.reason }}</v-card-text>
                </v-card>
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
                    }
                ],
                registrations: [
                    {
                        userId: 160,
                        lastname: 'Fritz',
                        firstname: 'Hans',
                        registrationState: {
                            id: 2,
                            name: 'Angemeldet',
                            reasonRequired: true
                        }
                    },
                    {
                        userId: 162,
                        lastname: 'White',
                        firstname: 'Walter',
                        reason: 'Sick',
                        registrationState: {
                            id: 1,
                            name: 'Deregistered',
                            reasonRequired: true
                        }
                    },
                    {
                        userId: 163,
                        lastname: 'White',
                        firstname: 'Walter',
                        reason: 'Sick',
                        registrationState: {
                            id: 1,
                            name: 'Deregistered',
                            reasonRequired: true
                        }
                    },
                    {
                        userId: 164,
                        lastname: 'White',
                        firstname: 'Walter',
                        reason: 'Sick',
                        registrationState: {
                            id: 1,
                            name: 'Deregistered',
                            reasonRequired: true
                        }
                    },
                    {
                        userId: 165,
                        lastname: 'White',
                        firstname: 'Walter',
                        reason: 'Sick',
                        registrationState: {
                            id: 1,
                            name: 'Deregistered',
                            reasonRequired: true
                        }
                    },
                    {
                        userId: 166,
                        lastname: 'White',
                        firstname: 'Walter',
                        reason: 'Sick',
                        registrationState: {
                            id: 1,
                            name: 'Deregistered',
                            reasonRequired: true
                        }
                    }
                ]
            };
        }
    };
</script>

<style scoped>

</style>
