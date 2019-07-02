<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <v-container>
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
                :items="registrationStates"
                :search="searchQuery"
                v-if="registrationStates"
        >
            <template v-slot:items="props">
                <tr @click="$router.push(`/registrationStates/${props.item.id}`)" class="clickable">
                    <td>{{ props.item.name }}</td>
                    <td>
                        <v-layout mt-4>
                            <v-checkbox
                                    :readonly="true"
                                    v-model="props.item.reasonRequired"
                            ></v-checkbox>
                        </v-layout>
                    </td>
                </tr>
            </template>
        </v-data-table>
    </v-container>
</template>

<script>

    export default {
        name: 'adminEventTypeOverview',
        data: function () {
            return {
                searchQuery: '',
                headers: [
                    {
                        text: this.$t('registrationState.name'),
                        align: 'left',
                        sortable: true,
                        value: 'name'
                    },
                    {
                        text: this.$t('registrationState.reasonRequired'),
                        align: 'left',
                        sortable: true,
                        value: 'reasonRequired'
                    }
                ],
                isAdmin: false
            };
        },
        computed: {
            registrationStates() {
                return this.$store.state.registrationStates.all.registrationStates;
            }
        },
        mounted() {
            this.user = JSON.parse(window.localStorage.getItem('user'));
            this.isAdmin = this.user.isAdmin;
            this.$store.dispatch('registrationStates/getAll');
        }
    };
</script>

<style scoped>

</style>
