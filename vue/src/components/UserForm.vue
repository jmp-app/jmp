<template>
    <div>
        <div class="row my-3">
            <div class="col">
                <label for="username">{{ $t('user.username') }}</label>
                <input type="text" name="username" id="username" class="form-control" v-model="user.username" required>
            </div>
        </div>
        <div class="row my-3">
            <div class="col-6">
                <label for="firstname">{{ $t('user.firstName') }}</label>
                <input class="form-control" id="firstname" name="firstname" type="text" v-model="user.firstname">
            </div>
            <div class="col-6">
                <label for="lastname">{{ $t('user.lastName') }}</label>
                <input class="form-control" id="lastname" name="lastname" type="text" v-model="user.lastname">
            </div>
        </div>
        <div class="row my-3">
            <div class="col">
                <label for="email">{{ $t('user.email') }}</label>
                <input class="form-control" id="email" name="email" type="email" v-model="user.email">
            </div>
        </div>
        <div class="row my-3">
            <div class="col">
                <label for="password">{{ $t('user.password') }}</label>
                <input class="form-control" id="password" name="text" type="password" v-model="user.password">
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
                    <input class="form-check-input" id="passwordChange" type="checkbox" v-model="user.passwordChange">
                    <label class="form-check-label" for="passwordChange">{{ $t('user.passwordChange') }}</label>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'UserForm',
        props: {
            user: {
                // type: Object,
                // required: true
            }
        }
    };
</script>
