<template>
    <div>
        <h1 class="mt-3">{{ $t('user.create') }}</h1>
        <UserForm :user="user"></UserForm>
        <div class="d-flex justify-content-end">
            <router-link :to="{ name: 'users' }" tag="button" class="btn btn-lg btn-outline-danger mr-2">{{ $t('cancel') }}</router-link>
            <button type="button" class="btn btn-lg btn-success">{{ $t('create') }}</button>
        </div>
    </div>
</template>

<script>
    import UserForm from '@/components/UserForm';

    export default {
        name: 'UserCreate',
        components: {UserForm},
        data: function () {
            return {
                user: {
                    username: '',
                    lastname: '',
                    firstname: '',
                    email: '',
                    isAdmin: false,
                    changePassword: false
                }
            };
        }
    };
</script>
