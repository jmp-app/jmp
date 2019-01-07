<template>
    <div>
        <input :placeholder="$t('search')" class="search my-3 form-control form-control-lg" type="search"
               v-model.trim="search">

        <div class="list-group mb-5">
            <GroupListItem :group="group" :key="group.id" v-for="group in groups"/>
        </div>
    </div>
</template>

<script>
    import GroupListItem from '@/components/GroupListItem';

    export default {
        name: 'Groups',
        components: {GroupListItem},
        data: function () {
            return {
                search: ''
            };
        },
        computed: {
            groups: function () {
                const filter = group => {
                    return this.searchString(group.name);
                };
                return this.$store.getters['groups/getGroupsFiltered'](filter);
            }
        },
        methods: {
            searchString: function (string) {
                return string.toLowerCase().includes(this.search.toLowerCase());
            }
        },
        mounted() {
            this.$store.dispatch('groups/getAll');
        }
    };
</script>
