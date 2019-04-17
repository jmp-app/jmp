<template>
    <div>
        <div class="pb-3" v-if="group && groupUsers && users">
            <h3>{{ $t('group.detail.AddMembers') }}</h3>
            <input
                    :placeholder="$t('search')"
                    class="search my-3 form-control"
                    name="query"
                    type="search"
                    v-model="searchQuery"
            >
            <div class="row">
                <div class="table-responsive grid col-sm-6 mb-3">
                    <h4>{{$t('group.addMembers.allUsers')}}</h4>
                    <table class="table table-striped">
                        <thead class="thead-dark">
                        <tr>
                            <th>{{$t('user.username')}}</th>
                            <th>{{$t('user.firstName')}}</th>
                            <th>{{$t('user.lastName')}}</th>
                        </tr>
                        </thead>
                        <tbody class="customTable">
                        <tr
                                :key="user.id"
                                @click="event => handleSelect(event)"
                                v-bind:id="user.id"
                                v-for="user in filteredData().usersNotInGroup"
                                v-on:dblclick="event => handleDbClickAdd(event)"
                        >
                            <td>{{user.username}}</td>
                            <td>{{user.firstname}}</td>
                            <td>{{user.lastname}}</td>
                        </tr>
                        </tbody>
                    </table>
                    <button @click="event => handleAddSelected(event)" class="btn btn-success">
                        {{$t('group.addMembers.addSelected')}}
                    </button>
                </div>
                <div class="table-responsive grid col-sm-6">
                    <h4>{{$t('group.detail.members')}}</h4>
                    <table class="table table-striped">
                        <thead class="thead-dark">
                        <tr>
                            <th>{{$t('user.username')}}</th>
                            <th>{{$t('user.firstName')}}</th>
                            <th>{{$t('user.lastName')}}</th>
                        </tr>
                        </thead>
                        <tbody class="customTable">
                        <tr
                                :key="user.id"
                                @click="event => handleSelect(event)"
                                v-bind:id="user.id"
                                v-for="user in filteredData().usersInGroup"
                                v-on:dblclick="event => handleDbClickRemove(event)"
                        >
                            <td>{{user.username}}</td>
                            <td>{{user.firstname}}</td>
                            <td>{{user.lastname}}</td>
                        </tr>
                        </tbody>
                    </table>
                    <button @click="event => handleRemoveSelected(event)" class="btn btn-danger">
                        {{$t('group.addMembers.removeSelected')}}
                    </button>
                </div>
            </div>
            <div style="text-align: right">
                <button @click="cancel()" class="btn mt-3 mr-2">{{$t('cancel')}}</button>
                <button @click="submit()" class="btn mt-3 btn-primary">{{$t('save')}}</button>
            </div>
        </div>
        <div class="text-xs-center" v-else>
            <v-progress-circular
                    color="primary"
                    indeterminate
            ></v-progress-circular>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'AddMembers',
        data: function () {
            return {
                unsubscribe: {},
                membersToAdd: [],
                membersToRemove: [],
                usersNotInGroup: [],
                usersInGroup: [],
                searchQuery: ''
            };
        },
        computed: {
            group() {
                return this.$store.state.group.data.group;
            },
            groupUsers() {
                return this.$store.state.group.data.group.users;
            },
            users() {
                return this.$store.state.users.all.items;
            }
        },
        methods: {
            handleSelect: function (event) {
                event.target.parentNode.classList.toggle('selected');
            },
            handleDbClickAdd: function (event) {
                for (const user of this.usersNotInGroup) {
                    if (user.id === parseInt(event.target.parentNode.id)) {
                        this.addUser(user);
                    }
                }
            },
            handleDbClickRemove: function (event) {
                for (const user of this.usersInGroup) {
                    if (user.id === parseInt(event.target.parentNode.id)) {
                        this.removeUser(user);
                    }
                }
            },
            handleAddSelected: function (event) {
                const selectedElements = event.target.parentNode.getElementsByTagName('tbody')[0].getElementsByClassName('selected');
                for (const element of selectedElements) {
                    this.addUser(this.getUserByUserIdFromArray(this.usersNotInGroup, element.id));
                }
            },
            handleRemoveSelected: function (event) {
                const selectedElements = event.target.parentNode.getElementsByTagName('tbody')[0].getElementsByClassName('selected');
                for (const element of selectedElements) {
                    this.removeUser(this.getUserByUserIdFromArray(this.usersInGroup, element.id));
                }
            },
            addUser: function (user) {
                this.membersToAdd.push(user);
                this.removeUserFromArray(this.usersNotInGroup, user);
                this.removeUserFromArray(this.membersToRemove, user);
                this.usersInGroup.push(user);
            },
            removeUser: function (user) {
                this.membersToRemove.push(user);
                this.removeUserFromArray(this.usersInGroup, user);
                this.removeUserFromArray(this.membersToAdd, user);
                this.usersNotInGroup.push(user);
            },
            removeUserFromArray: function (array, userToRemove) {
                for (const [i, user] of array.entries()) {
                    if (user === userToRemove) {
                        array.splice(i, 1);
                    }
                }
            },
            getUserByUserIdFromArray: function (array, userId) {
                for (const user of array) {
                    if (user.id === parseInt(userId)) {
                        return user;
                    }
                }
            },
            handleMutation: function (mutation) {
                switch (mutation.type) {
                    case 'group/getGroupSuccess':
                        this.usersInGroup = this.groupUsers;
                        this.$store.dispatch('users/getAll');
                        break;
                    case 'users/getAllSuccess':
                        this.handleGetAllSuccess();
                        break;
                }
            },
            handleGetAllSuccess: function () {
                for (let user of this.users) {
                    if (!this.checkIfUserIsInGroup(user.id)) {
                        this.usersNotInGroup.push(user);
                    }
                }
            },
            checkIfUserIsInGroup: function (userId) {
                return this.usersInGroup.some(function (el) {
                    return el.id === userId;
                });
            },
            cancel: function () {
                this.$router.replace(`/groups/${this.$route.params.id}`);
            },
            submit: function () {
                const groupId = this.$route.params.id;
                const userIdsToAdd = [];
                for (const user of this.membersToAdd) {
                    userIdsToAdd.push(user.id);
                }
                const userIdsToRemove = [];
                for (const user of this.membersToRemove) {
                    userIdsToRemove.push(user.id);
                }
                if (userIdsToAdd.length > 0) {
                    this.$store.dispatch('group/join', {groupId, userIdsToAdd});
                }
                if (userIdsToRemove.length > 0) {
                    this.$store.dispatch('group/leave', {groupId, userIdsToRemove});
                }
                this.$router.replace(`/groups/${this.$route.params.id}`);
            },
            filteredData: function () {
                let filterKey = this.searchQuery && this.searchQuery.toLowerCase();
                let usersInGroupF = this.usersInGroup;
                let usersNotInGroupF = this.usersNotInGroup;
                if (filterKey) {
                    usersInGroupF = usersInGroupF.filter(function (row) {
                        return Object.keys(row).some(function (key) {
                            return String(row[key]).toLowerCase().indexOf(filterKey) > -1;
                        });
                    });
                    usersNotInGroupF = usersNotInGroupF.filter(function (row) {
                        return Object.keys(row).some(function (key) {
                            return String(row[key]).toLowerCase().indexOf(filterKey) > -1;
                        });
                    });
                }
                return {'usersInGroup': usersInGroupF, 'usersNotInGroup': usersNotInGroupF};
            }
        },
        created() {
            this.unsubscribe = this.$store.subscribe(mutation => {
                this.handleMutation(mutation);
            });
        },
        mounted() {
            const id = this.$route.params.id;
            this.$store.dispatch('group/get', {id});
        },
        beforeDestroy() {
            this.unsubscribe();
        }
    };
</script>

<style scoped>
    .selected {
        background-color: lightskyblue !important;
    }

    .customTable {
        cursor: pointer;
    }
</style>
