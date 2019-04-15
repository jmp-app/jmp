<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <v-data-table
                :headers="headers"
                :items="events"
                :search="searchQuery"
                v-if="events"
        >
            <template v-slot:items="props">
                <tr @click="$router.push(`/events/${props.item.id}`)">
                    <td>{{ props.item.title }}</td>
                </tr>
            </template>
        </v-data-table>
    </div>
</template>

<script>
    export default {
        name: 'AdminEventOverview',
        data: function () {
            return {
                searchQuery: '',
                headers: [
                    {
                        text: this.$t('event.title'),
                        align: 'left',
                        sortable: true,
                        value: 'title'
                    }
                ]
            };
        },
        computed: {
            events() {
                return this.$store.state.events.all.events;
            }
        },
        mounted() {
            this.$store.dispatch('events/getAll');
        }
    };
</script>

<style scoped>

</style>
