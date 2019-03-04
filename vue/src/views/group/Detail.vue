<template>
    <div>
        <form @submit.prevent="handleSubmit" v-if="group">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="name">{{$t('group.name')}}</label>
                <div class="col-sm-9">
                    <input :class="{ 'is-invalid': submitted && !group.name }" :readonly="display()"
                           class="form-control"
                           id="name" type="text"
                           v-model="group.name">
                    <div class="invalid-feedback" v-show="submitted && !group.name">
                        {{ $t("group.detail.nameRequired") }}
                    </div>
                </div>
            </div>
            <div class="form-group row" v-if="create() || edit()">
                <div class="col-sm-9">
                    <button @click="handleCancel()" class="btn mr-2" type="button" v-if="edit()">{{$t('cancel')}}
                    </button>
                    <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
                </div>
            </div>
            <div class="form-group row" v-if="display()">
                <div class="col-sm-9">
                    <button @click="mode = 'edit'" class="btn mr-2" type="button">{{$t('edit')}}</button>
                </div>
            </div>
        </form>
        <hr/>
        <div v-if="group && users">
            <h4>{{ $t("group.detail.members") }}</h4>
            <input
                    :placeholder="$t('search')"
                    class="search my-3 form-control"
                    id="search"
                    name="query"
                    type="search"
                    v-model="searchQuery"
            >
            <grid
                    :columnTitles="gridColumnTitles"
                    :columns="gridColumns"
                    :data="users"
                    :filter-key="searchQuery"
                    :routerLinkTo="routerLinkTo">
            </grid>
        </div>
        <BottomNavigation/>
    </div>
</template>

<script>
    import Grid from '@/components/Grid.vue';
    import BottomNavigation from '@/components/BottomNavigation';

    export default {
        name: 'GroupDetail',
        components: {
            Grid,
            BottomNavigation
        },
        data: function () {
            return {
                mode: 'display', // display, edit, create
                submitted: false,
                groupOnSubmit: {},
                searchQuery: '',
                gridColumns: ['username', 'firstname', 'lastname'],
                gridColumnTitles: {
                    'username': this.$t('user.username'),
                    'firstname': this.$t('user.firstName'),
                    'lastname': this.$t('user.lastName')
                },
                routerLinkTo: 'users'
            };
        },
        computed: {
            group() {
                return this.$store.state.group.data.group;
            },
            users() {
                return this.$store.state.group.data.group.users;
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
            handleSubmit: function () {
                this.submitted = true;
                this.groupOnSubmit = this.group;
                if (this.mode === 'create') {
                    this.createNewGroup();
                } else if (this.mode === 'edit') {
                    this.changeGroup();
                } else {
                    this.submitted = false;
                }
            },
            createNewGroup: function () {
                let group = this.group;
                if (!group.name) {
                    return;
                }
                this.$store.dispatch('group/create', {group});
            },
            changeGroup: function () {
                let group = this.group;
                this.$store.dispatch('group/update', {group});
            },
            handleCancel: function () {
                if (this.create()) {
                    this.$store.state.group.data.group = {};
                } else {
                    this.renewGroupFromDb();
                    this.mode = 'display';
                }
                this.submitted = false;
            },
            handleMutation: function (mutation) {
                switch (mutation.type) {
                    case 'group/groupRequestFailure':
                        this.$store.dispatch('alert/error', 'Not Found', {root: true});
                        break;
                    case 'group/updateGroupSuccess':
                        this.renewGroupFromDb();
                        this.mode = 'display';
                        break;
                    case 'group/createGroupSuccess':
                        this.$router.push('/groups');
                        break;
                }
            },
            renewGroupFromDb: function () {
                const id = this.$route.params.id;
                this.$store.dispatch('group/get', {id});
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
                this.$store.state.group.data.group = {};
            } else {
                this.$store.dispatch('group/get', {id});
            }
        }
    };
</script>

<style scoped>

</style>
