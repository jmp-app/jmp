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
            <router-link :to="{ name: 'user', params: { id: user.id, user: user } }" :key="user.id" v-for="user in filterUsers"
                         class="list-group-item list-group-item-action">
                {{ user.username }}<span class="font-weight-light"> {{ user.firstname }} {{ user.lastname }} </span>
                <span class="badge badge-danger" v-if="user.isAdmin">Admin</span>
            </router-link>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Users',
        data: function () {
            return {
                search: ''
            };
        },
        computed: {
            filterUsers: function() {
                const self = this;
                const users = this.$store.state.users.all.items;
                // filter users by username, last name and first name
                return users.filter(function(user) {
                    return self.searchString(user.username) ||
                        self.searchString(user.lastname) ||
                        self.searchString(user.firstname);
                });
            }
        },
        methods: {
            searchString: function(string) {
                return string.toLowerCase().includes(this.search.toLowerCase());
            }
        },
        created() {
            this.$store.dispatch('users/getAll');
        }
    };
</script>
