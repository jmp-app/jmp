<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <h3>{{$t('group.detail.title')}}</h3>
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
                    <v-btn @click="handleCancel()" type="button" v-if="edit()">{{$t('cancel')}}
                    </v-btn>
                    <v-btn color="info" type="submit">{{$t('save')}}</v-btn>
                </div>
            </div>
            <div class="form-group row" v-if="display()">
                <div class="col-sm-9">
                    <v-btn @click="mode = 'edit'" type="button">{{$t('edit')}}</v-btn>
                </div>
            </div>
        </form>
        <hr/>
        <div v-if="group && !create()">
            <h4>{{ $t("group.detail.members") }}</h4>
            <input
                    :placeholder="$t('search')"
                    class="search my-3 form-control"
                    id="search"
                    name="query"
                    type="search"
                    v-model="searchQuery"
            >
            <v-data-table
                    :headers="headers"
                    :items="group.users"
                    :search="searchQuery"
            >
                <template v-slot:items="props">
                    <tr @click="$router.push(`/users/${props.item.id}`)" class="clickable">
                        <td>{{ props.item.username }}</td>
                        <td>{{ props.item.firstname }}</td>
                        <td>{{ props.item.lastname }}</td>
                    </tr>
                </template>
            </v-data-table>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'GroupDetail',
        data: function () {
            return {
                mode: 'display', // display, edit, create
                submitted: false,
                searchQuery: '',
                headers: [
                    {
                        text: this.$t('user.username'),
                        align: 'left',
                        sortable: true,
                        value: 'username'
                    },
                    {
                        text: this.$t('user.firstName'),
                        align: 'left',
                        sortable: true,
                        value: 'firstname'
                    },
                    {
                        text: this.$t('user.lastName'),
                        align: 'left',
                        sortable: true,
                        value: 'lastname'
                    }
                ],
                routerLinkTo: 'users'
            };
        },
        computed: {
            group() {
                return this.$store.state.group.data.group;
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
                        this.mode = 'display';
                        this.$router.replace(`/groups/${this.$store.state.group.data.group.id}`);
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
