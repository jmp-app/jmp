<template>
    <div>
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="my-3">{{ $tc('user', 2) }}</h1>
            <div>
                <router-link :to="{ name: 'createUser' }" tag="button" class="btn btn-primary">
                    {{ $t('user.create') }}
                </router-link>
            </div>
        </div>

        <input type="search" class="search my-3 form-control form-control-lg" :placeholder="$t('search')" v-model.trim="search">

        <div class="list-group mb-5">
            <UserListItem :user="user" :key="user.id" v-for="user in users" />
        </div>
    </div>
</template>

<script>
    import UserListItem from '@/components/UserListItem';

    export default {
        name: 'Users',
        components: {UserListItem},
        data: function () {
            return {
                search: ''
            };
        },
        computed: {
            users: function() {
                const filter = user => {
                    return this.searchString(user.username) ||
                        this.searchString(user.lastname) ||
                        this.searchString(user.firstname);
                };
                return this.$store.getters['users/getUsersFiltered'](filter);
            }
        },
        methods: {
            searchString: function(string) {
                return string.toLowerCase().includes(this.search.toLowerCase());
            }
        },
        mounted() {
            this.$store.dispatch('users/getAll');
        }
    };
</script>
