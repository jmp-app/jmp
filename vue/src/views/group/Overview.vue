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
                :columnTitles="gridColumnTitles"
                :columns="gridColumns"
                :data="groups"
                :filter-key="searchQuery"
                :routerLinkTo="routerLinkTo">
        </grid>
        <BottomNavigation v-if="isAdmin"></BottomNavigation>
    </div>
</template>

<script>
    import Grid from '@/components/Grid.vue';
    import BottomNavigation from '@/components/BottomNavigation';

    export default {
        name: 'GroupOverview',
        components: {
            BottomNavigation,
            Grid
        },
        data: function () {
            return {
                searchQuery: '',
                gridColumns: ['name'],
                gridColumnTitles: {
                    'name': this.$t('group.name')
                },
                routerLinkTo: 'groups',
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
