<template>
    <form @submit.prevent="submit">
        <h1 class="mt-3">{{ $t('group.edit') }}</h1>
        <div class="my-4 text-center h3" v-if="data.loading">Loading...</div>
        <div v-if="data.error">Error: {{ group.error }}</div>
        <GroupForm :group="data.group" v-if="data.group"></GroupForm>
        <div class="d-flex justify-content-end">
            <router-link :to="{ name: 'groups' }" class="btn btn-lg btn-outline-danger mx-1" tag="button">{{
                $t('cancel') }}
            </router-link>
            <button :disabled="!data.group" @click="deleteGroup" class="btn btn-lg btn-danger mx-1" type="button">{{
                $t('group.delete') }}
            </button>
            <button :disabled="!data.group" class="btn btn-lg btn-primary mx-1" type="submit">{{ $t('edit') }}</button>
        </div>
        <input :placeholder="$t('search')" class="search my-3 form-control form-control-lg" type="search"
               v-model.trim="search">

        <div class="list-group mb-5">
            <UserListItem :key="user.id" :user="user" v-for="user in users"/>
        </div>
    </form>
</template>

<script>
    import GroupForm from '@/components/GroupForm';
    import UserListItem from '@/components/UserListItem';

    export default {
        name: 'GroupEdit',
        components: {GroupForm, UserListItem},
        data: function () {
            return {
                search: ''
            };
        },
        // TODO: gruppe bearbeiten geht nicht, benutzer hinzufügen entfernen... und gruppe erstellen
        computed: {
            data() {
                return this.$store.state.group.data;
            },
            users: function () {
                const filter = user => {
                    return this.searchString(user.username) ||
                        this.searchString(user.lastname) ||
                        this.searchString(user.firstname);
                };
                return this.$store.getters['users/getUsersFiltered'](filter);
            }
        },
        methods: {
            submit: function () {
                this.$store.dispatch('group/update', this.data.group);
            },
            deleteGroup: function () {
                this.$store.dispatch('group/deleteGroup', this.data.group);
                this.$router.push({name: 'groups'});
            },
            searchString: function (string) {
                return string.toLowerCase().includes(this.search.toLowerCase());
            }
        },
        mounted() {
            const id = this.$route.params.id;
            this.$store.dispatch('group/get', {id});
            this.$store.dispatch('users/getAllOfGroup', {id});
        }
    };
</script>
