<template>
    <router-link :to="{ name: 'group', params: { id: group.id } }" class="list-group-item list-group-item-action">
        {{ group.name }}
    </router-link>
</template>

<script>
    export default {
        name: 'GroupListItem',
        props: {
            group: {
                type: Object,
                required: true
            }
        }
    };
</script>
