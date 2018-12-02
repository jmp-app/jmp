<template>
    <div>
        <h1 class="mt-3">{{ $t('user.edit') }}</h1>
        <UserForm :user="user"></UserForm>
        <div class="d-flex justify-content-end">
            <router-link :to="{ name: 'users' }" tag="button" class="btn btn-lg btn-outline-danger mr-2">{{ $t('cancel') }}</router-link>
            <button type="button" class="btn btn-lg btn-success">{{ $t('edit') }}</button>
        </div>
    </div>
</template>

<script>
    import UserForm from '@/components/UserForm';

    export default {
        name: 'UserEdit',
        components: {UserForm},
        data: function () {
            return {
                user: {}
            };
        },
        methods: {
            getInitialData: function () {
                if (this.$route.params.user) {
                    this.user = this.$route.params.user;
                } else {
                    // const id = this.$route.id;
                    // TODO: Get user from /api/v1/users/{id}
                }
            }
        },
        beforeMount() {
            this.getInitialData();
        }
    };
</script>
