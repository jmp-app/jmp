<template>
    <div>
        <form id="search">
            Search <input name="query" v-model="searchQuery">
        </form>
        <grid
                :columns="gridColumns"
                :data="gridData"
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
                gridColumns: ['name', 'power'],
                gridData: [
                    {name: 'Chuck Norris', power: Infinity},
                    {name: 'Bruce Lee', power: 9000},
                    {name: 'Jackie Chan', power: 7000},
                    {name: 'Jet Li', power: 8000}
                ]
            };
        }
    };
</script>

<style scoped>

</style>
