<template>
    <div>
        <div class="row my-3">
            <div class="col">
                <label for="username">{{ $t('user.username') }}</label>
                <input type="text" name="username" id="username" class="form-control" v-model="user.username">
            </div>
        </div>
        <div class="row my-3">
            <div class="col-6">
                <label for="firstname">{{ $t('user.firstName') }}</label>
                <input type="text" name="firstname" id="firstname" class="form-control" v-model="user.firstname">
            </div>
            <div class="col-6">
                <label for="lastname">{{ $t('user.lastName') }}</label>
                <input type="text" name="lastname" id="lastname" class="form-control" v-model="user.lastname">
            </div>
        </div>
        <div class="row my-3">
            <div class="col">
                <label for="email">{{ $t('user.email') }}</label>
                <input type="email" name="email" id="email" class="form-control" v-model="user.email">
            </div>
        </div>
        <div class="row my-3">
            <div class="col-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="admin" v-model="user.isAdmin">
                    <label class="form-check-label" for="admin">Admin</label>
                </div>
            </div>
            <div class="col-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="change-password" v-model="user.changePassword">
                    <label class="form-check-label" for="change-password">{{ $t('user.passwordChange') }}</label>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'UserForm',
        props: {
            user: {}
        }
    };
</script>
