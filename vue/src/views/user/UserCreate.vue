<template>
    <form @submit.prevent="submit">
        <h1 class="mt-3">{{ $t('user.create') }}</h1>
        <UserForm v-if="data.user" :user="data.user"></UserForm>
        <div v-if="data.error">Error: {{ data.error }}</div>
        <div class="d-flex justify-content-end">
            <router-link :to="{ name: 'users' }" tag="button" class="btn btn-lg btn-outline-danger mr-2">{{ $t('cancel') }}</router-link>
            <button type="submit" class="btn btn-lg btn-primary" :disabled="!data.user">{{ $t('create') }}</button>
        </div>
    </form>
</template>

<script>
    import UserForm from '@/components/UserForm';

    export default {
        name: 'UserCreate',
        components: {UserForm},
        computed: {
            data() {
                return this.$store.state.user.data;
            }
        },
        methods: {
            submit: function() {
                this.$store.dispatch('user/create', this.data.user);
            }
        },
        mounted() {
            console.log('test2');
            this.$store.dispatch('user/reset');
        }
    };
</script>
