<template>
    <div>
        <input
                :placeholder="$t('search')"
                class="search my-3 form-control form-control-lg"
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
    </div>
</template>

<script>
    import Grid from '@/components/Grid.vue';

    export default {
        name: 'Overview',
        components: {
            Grid
        },
        data: function () {
            return {
                searchQuery: '',
                gridColumns: ['username', 'firstname', 'lastname', 'email'],
                gridColumnTitles: {
                    'username': this.$t('user.username'),
                    'firstname': this.$t('user.firstName'),
                    'lastname': this.$t('user.lastName'),
                    'email': this.$t('user.email')
                },
                routerLinkTo: 'users'
            }
                ;
        },
        computed: {
            users() {
                return this.$store.state.users.all.items;
            }
        },
        mounted() {
            this.$store.dispatch('users/getAll');
        }
    };
</script>

<style scoped>

</style>
