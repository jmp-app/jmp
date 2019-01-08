<template>
    <form @submit.prevent="submit">
        <h1 class="mt-3">{{ $t('user.edit') }}</h1>
        <div v-if="data.loading" class="my-4 text-center h3">Loading...</div>
        <div v-if="data.error">Error: {{ user.error }}</div>
        <UserForm v-if="data.user" :user="data.user"></UserForm>
        <div class="d-flex justify-content-end">
            <router-link :to="{ name: 'users' }" tag="button" class="btn btn-lg btn-outline-danger mx-1">{{ $t('cancel') }}</router-link>
            <button type="button" class="btn btn-lg btn-danger mx-1" :disabled="!data.user" @click="deleteUser">{{ $t('user.delete') }}</button>
            <button type="submit" class="btn btn-lg btn-primary mx-1" :disabled="!data.user">{{ $t('edit') }}</button>
        </div>
    </form>
</template>

<script>
    import UserForm from '@/components/UserForm';

    export default {
        name: 'UserEdit',
        components: {UserForm},
        computed: {
            data() {
                return this.$store.state.user.data;
            }
        },
        methods: {
            submit: function() {
                let user = this.data.user;
                this.$store.dispatch('user/update', {user});
            },
            deleteUser: function() {
                let user = this.data.user;
                this.$store.dispatch('user/deleteUser', {user});
                this.$router.push({ name: 'users' });
            }
        },
        mounted() {
            const id = this.$route.params.id;
            this.$store.dispatch('user/get', {id});
        }
    };
</script>
