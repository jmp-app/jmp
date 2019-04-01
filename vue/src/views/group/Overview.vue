<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <h3>{{$t('group.overview.title')}}</h3>
        <input
                :placeholder="$t('search')"
                class="search my-3 form-control"
                name="query"
                type="search"
                v-model="searchQuery"
        >
        <v-data-table
                :headers="headers"
                :items="groups"
                :search="searchQuery"
                v-if="groups"
        >
            <template v-slot:items="props">
                <tr @click="$router.push(`/groups/${props.item.id}`)">
                    <td>{{ props.item.name }}</td>
                </tr>
            </template>
        </v-data-table>
        <BottomNavigation v-if="isAdmin"></BottomNavigation>
    </div>
</template>

<script>
    import BottomNavigation from '@/components/BottomNavigation';

    export default {
        name: 'GroupOverview',
        components: {
            BottomNavigation
        },
        data: function () {
            return {
                searchQuery: '',
                headers: [
                    {
                        text: this.$t('group.name'),
                        align: 'left',
                        sortable: true,
                        value: 'name'
                    }
                ],
                isAdmin: false
            };
        },
        computed: {
            groups() {
                return this.$store.state.groups.all.items;
            }
        },
        mounted() {
            this.user = JSON.parse(window.localStorage.getItem('user'));
            this.isAdmin = this.user.isAdmin;
            this.$store.dispatch('groups/getAll');
        }
    };
</script>

<style scoped>

</style>
