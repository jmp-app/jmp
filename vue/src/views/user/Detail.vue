<template>
    <form @submit.prevent="handleSubmit" v-if="user">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="username">{{$t('user.username')}}</label>
            <div class="col-sm-9">
                <input :class="{ 'is-invalid': submitted && !user.username }" :readonly="display()" class="form-control"
                       id="username" type="text"
                       v-model="user.username">
                <div class="invalid-feedback" v-show="submitted && !user.username">
                    {{ $t("user.detail.usernameRequired") }}
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="firstName">{{$t('user.firstName')}}</label>
            <div class="col-sm-9">
                <input :readonly="display()" class="form-control" id="firstName" type="text" v-model="user.firstname">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="lastName">{{$t('user.lastName')}}</label>
            <div class="col-sm-9">
                <input :readonly="display()" class="form-control" id="lastName" type="text" v-model="user.lastname">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="email">{{$t('user.email')}}</label>
            <div class="col-sm-9">
                <input :readonly="display()" class="form-control" id="email" type="text" v-model="user.email">
            </div>
        </div>
        <div class="form-group row" v-if="create()">
            <label class="col-sm-3 col-form-label" for="password">{{$t('user.password')}}</label>
            <div class="col-sm-9">
                <input :class="{ 'is-invalid': submitted && (!user.password || isPasswordValid()) }"
                       :readonly="display()" class="form-control" id="password" type="password"
                       v-model="user.password">
                <div class="invalid-feedback" v-show="submitted && !user.password">
                    {{ $t("user.detail.passwordRequired") }}
                </div>
            </div>
        </div>
        <div class="form-group row" v-if="create()">
            <label class="col-sm-3 col-form-label" for="password2">{{$t('user.create.password2')}}</label>
            <div class="col-sm-9">
                <input :class="{ 'is-invalid': submitted && (!user.password2 || isPasswordValid())}"
                       :readonly="display()" class="form-control" id="password2"
                       type="password"
                       v-model="user.password2">
                <div class="invalid-feedback" v-show="submitted && !user.password2">
                    {{ $t("user.detail.passwordRequired") }}
                </div>
                <div class="invalid-feedback" v-show="submitted && isPasswordValid() && user.password2">
                    Passwörter müssen übereinstimmen
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-9">
                <div class="custom-control custom-checkbox">
                    <input :disabled="display()" class="custom-control-input" id="isAdmin" type="checkbox"
                           v-model="user.isAdmin">
                    <label class="custom-control-label" for="isAdmin">{{$t('user.isAdmin')}}</label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-9">
                <div class="custom-control custom-checkbox">
                    <input :disabled="display()" class="custom-control-input" id="passwordChange"
                           type="checkbox" v-model="user.passwordChange">
                    <label class="custom-control-label"
                           for="passwordChange">{{$t('user.detail.passwordChange')}}</label>
                </div>
            </div>
        </div>
        <div class="form-group row" v-if="create() || edit()">
            <div class="col-sm-9">
                <v-btn @click="handleCancel()" type="button" v-if="edit()">{{$t('cancel')}}</v-btn>
                <v-btn class="btn btn-primary" color="info" type="submit">{{$t('save')}}</v-btn>
            </div>
        </div>
        <div class="form-group row" v-if="display()">
            <div class="col-sm-9">
                <v-btn @click="mode = 'edit'" type="button">{{$t('edit')}}</v-btn>
            </div>
        </div>
    </form>
</template>

<script>
    export default {
        name: 'Detail',
        data: function () {
            return {
                mode: 'display', // display, edit, create
                submitted: false,
                userOnSubmit: {}
            };
        },
        computed: {
            user() {
                return this.$store.state.user.data.user;
            }
        },
        methods: {
            create: function () {
                return (this.mode === 'create');
            },
            edit: function () {
                return (this.mode === 'edit');
            },
            display: function () {
                return (this.create() === false && this.edit() === false);
            },
            isPasswordValid: function () {
                return this.user.password !== this.user.password2;
            },
            handleSubmit: function () {
                this.submitted = true;
                this.userOnSubmit = this.user;
                if (this.mode === 'create') {
                    this.createNewUser();
                } else if (this.mode === 'edit') {
                    this.changeUser();
                } else {
                    this.submitted = false;
                }
            },
            createNewUser: function () {
                let user = this.user;
                if (!user.username || !user.password || !user.password2) {
                    return;
                }
                if (user.password !== user.password2) {
                    return;
                }
                this.$store.dispatch('user/create', {user});
            },
            changeUser: function () {
                let user = this.user;
                this.$store.dispatch('user/update', {user});
            },
            handleCancel: function () {
                if (this.create()) {
                    this.$store.state.user.data.user = {};
                } else {
                    this.renewUserFromDb();
                    this.mode = 'display';
                }
                this.submitted = false;
            },
            handleMutation: function (mutation) {
                switch (mutation.type) {
                    case 'user/userRequestFailure':
                        this.$store.dispatch('alert/error', 'Not Found', {root: true});
                        break;
                    case 'user/updateUserSuccess':
                        this.renewUserFromDb();
                        this.mode = 'display';
                        break;
                    case 'user/createUserSuccess':
                        this.$router.push('/users');
                        break;
                }
            },
            renewUserFromDb: function () {
                const id = this.$route.params.id;
                this.$store.dispatch('user/get', {id});
            }
        },
        created() {
            this.$store.subscribe(mutation => {
                this.handleMutation(mutation);
            });
        },
        mounted() {
            const id = this.$route.params.id;
            // eslint-disable-next-line
            if (id == 0) {
                this.mode = 'create';
                this.$store.state.user.data.user = {};
            } else {
                this.$store.dispatch('user/get', {id});
            }
        }
    };
</script>

<style scoped>

</style>
