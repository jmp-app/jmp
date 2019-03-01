<template>
    <form @submit.prevent="submit">
        <h1 class="mt-3">{{ $t('group.create') }}</h1>
        <div class="my-4 text-center h3" v-if="loading">Loading...</div>
        <GroupForm :group="group" v-if="group"></GroupForm>
        <div class="d-flex justify-content-end mb-4">
            <router-link :to="{ name: 'groups' }" class="btn btn-lg btn-outline-danger mx-1" tag="button">{{
                $t('cancel') }}
            </router-link>
            <button :disabled="!group" class="btn btn-lg btn-primary mx-1" type="submit">{{
                $t('create') }}
            </button>
        </div>
    </form>
</template>

<script>
    import GroupForm from '@/components/GroupForm';

    export default {
        name: 'GroupCreate',
        components: {GroupForm},
        computed: {
            group: function() {
                return this.$store.state.group.data.group;
            },
            error: function() {
                return this.$store.state.group.data.error;
            },
            loading: function() {
                return this.$store.state.group.data.loading;
            }
        },
        methods: {
            submit: function () {
                let group = this.group;
                this.$store.dispatch('group/create', {group});
            }
        },
        mounted() {
            this.$store.dispatch('group/reset');
        },
        created() {
            this.$store.subscribe(mutation => {
                if (mutation.type === 'group/createGroupSuccess') {
                    this.$router.push({ name: 'groups' });
                }
            });
        }
    };
</script>
