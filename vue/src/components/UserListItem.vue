<template>
    <router-link :to="{ name: 'user', params: { id: user.id } }" class="list-group-item list-group-item-action">
        {{ user.username }}<span class="font-weight-light"> {{ user.firstname }} {{ user.lastname }} </span>
        <span class="badge badge-danger" v-if="user.isAdmin">Admin</span>
    </router-link>
</template>

<script>
    export default {
        name: 'UserListItem',
        props: {
            user: {
                type: Object,
                required: true
            }
        }
    };
</script>
