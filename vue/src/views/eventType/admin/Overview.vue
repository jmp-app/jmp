<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <h3>{{$t('eventType.overview.title')}}</h3>
        <input
                :placeholder="$t('search')"
                class="search my-3 form-control"
                name="query"
                type="search"
                v-model="searchQuery"
        >
        <v-data-table
                :headers="headers"
                :items="eventTypes"
                :search="searchQuery"
                v-if="eventTypes"
        >
            <template v-slot:items="props">
                <tr @click="$router.push(`/eventTypes/${props.item.id}`)" class="clickable">
                    <td>{{ props.item.title }}</td>
                    <td>
                        <v-btn :color="props.item.color" depressed>{{ props.item.color }}</v-btn>
                    </td>
                </tr>
            </template>
        </v-data-table>
    </div>
</template>

<script>

    export default {
        name: 'adminEventTypeOverview',
        data: function () {
            return {
                searchQuery: '',
                headers: [
                    {
                        text: this.$t('eventType.title'),
                        align: 'left',
                        sortable: true,
                        value: 'title'
                    },
                    {
                        text: this.$t('eventType.color.preview'),
                        align: 'left',
                        sortable: true,
                        value: 'color'
                    }
                ],
                isAdmin: false
            };
        },
        computed: {
            eventTypes() {
                return this.$store.state.eventTypes.all.eventTypes;
            }
        },
        mounted() {
            this.user = JSON.parse(window.localStorage.getItem('user'));
            this.isAdmin = this.user.isAdmin;
            this.$store.dispatch('eventTypes/getAll');
        }
    };
</script>

<style scoped>

</style>
