<template>
    <div>
        <input
                :placeholder="$t('search')"
                class="search my-3 form-control"
                name="query"
                type="search"
                v-model="searchQuery"
        >
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
