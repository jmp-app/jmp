<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <input
                :placeholder="$t('search')"
                class="search my-3 form-control"
                name="query"
                type="search"
                v-model="searchQuery"
        >
        <v-data-table
                :headers="headers"
                :items="users"
                :search="searchQuery"
        >
            <template v-slot:items="props">
                <td>{{ props.item.username }}</td>
                <td>{{ props.item.firstname }}</td>
                <td>{{ props.item.lastname }}</td>
            </template>
        </v-data-table>
        <grid
                :columns="gridColumns"
                :columnTitles="gridColumnTitles"
                :routerLinkTo="routerLinkTo"
                :data="users"
                :filter-key="searchQuery">
        </grid>
        <BottomNavigation v-if="isAdmin"></BottomNavigation>
    </div>
</template>

<script>
    import Grid from '@/components/Grid.vue';
    import BottomNavigation from '@/components/BottomNavigation';

    export default {
        name: 'Overview',
        components: {
            BottomNavigation,
            Grid
        },
        data: function () {
            return {
                searchQuery: '',
                gridColumns: ['username', 'firstname', 'lastname'],
                headers: [
                    {
                        text: this.$t('user.username'),
                        align: 'left',
                        sortable: true,
                        value: 'username'
                    },
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
                    }
                ],
                gridColumnTitles: {
                    'username': this.$t('user.username'),
                    'firstname': this.$t('user.firstName'),
                    'lastname': this.$t('user.lastName')
                },
                routerLinkTo: 'users',
                isAdmin: false
            };
        },
        computed: {
            users() {
                return this.$store.state.users.all.items;
            }
        },
        mounted() {
            this.user = JSON.parse(window.localStorage.getItem('user'));
            this.isAdmin = this.user.isAdmin;
            this.$store.dispatch('users/getAll');
        }
    };
</script>

<style scoped>

</style>
