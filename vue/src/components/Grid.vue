<template>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
            <tr>
                <th :class="{ active: sortKey === key }"
                    :key="key"
                    @click="sortBy(key)"
                    v-for="key in columns">
                    {{ columnTitles[key] }}
                    <span :class="sortOrders[key] > 0 ? 'asc' : 'dsc'" class="arrow"></span>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr :key="index" @click="selected(entry)" v-for="(entry, index) in filteredData">
                <td :key="key" v-for="key in columns">
                    {{entry[key]}}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        name: 'Grid',
        props: {
            data: Array,
            columns: Array,
            columnTitles: Object,
            routerLinkTo: String,
            filterKey: String
        },
        data: function () {
            let sortOrders = {};
            this.columns.forEach(function (key) {
                sortOrders[key] = 1;
            });
            return {
                sortKey: '',
                sortOrders: sortOrders
            };
        },
        computed: {
            filteredData: function () {
                let sortKey = this.sortKey;
                let filterKey = this.filterKey && this.filterKey.toLowerCase();
                let order = this.sortOrders[sortKey] || 1;
                let data = this.data;
                if (filterKey) {
                    data = data.filter(function (row) {
                        return Object.keys(row).some(function (key) {
                            return String(row[key]).toLowerCase().indexOf(filterKey) > -1;
                        });
                    });
                }
                if (sortKey) {
                    data = data.slice().sort(function (a, b) {
                        a = a[sortKey];
                        b = b[sortKey];
                        return (a === b ? 0 : a > b ? 1 : -1) * order;
                    });
                }
                return data;
            }
        },
        filters: {
            capitalize: function (str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }
        },
        methods: {
            sortBy: function (key) {
                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1;
            },
            selected: function (item) {
                if (this.routerLinkTo) {
                    this.$router.push({path: `/${this.routerLinkTo}/${item.id}`});
                }
            }
        }
    };
</script>

<style scoped>
    .arrow {
        display: inline-block;
        vertical-align: middle;
        width: 0;
        height: 0;
        margin-left: 5px;
        opacity: 0.66;
    }

    .arrow.asc {
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-bottom: 4px solid #fff;
    }

    .arrow.dsc {
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 4px solid #fff;
    }
</style>
