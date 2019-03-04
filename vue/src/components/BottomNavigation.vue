<template>
    <nav class="navbar fixed-bottom navbar-light bg-light">
        <form class="form-inline">
            <button class="btn btn-primary nav-item mr-2" type="button" v-if="isEventDetail()">Übersicht</button>
            <button class="btn btn-primary nav-item mr-2" type="button" v-if="isEventDetail()">Kontrollieren</button>
            <button
                    @click="$router.push('/users/0')"
                    class="btn btn-primary nav-item mr-2" type="button"
                    v-if="isUserOverview()"
            >
                {{$t('user.create')}}
            </button>
            <button
                    @click="$router.push('/groups/0')"
                    class="btn btn-primary nav-item mr-2" type="button"
                    v-if="isGroupOverview()"
            >
                {{$t('group.create')}}
            </button>
            <button
                    @click="$router.push('/groups/addMembers')"
                    class="btn btn-primary nav-item mr-2" type="button"
                    v-if="isGroupDetail()"
            >
                {{$t('group.detail.AddMembers')}}
            </button>
        </form>
    </nav>
</template>

<script>
    export default {
        name: 'BottomNavigation',
        methods: {
            isEventDetail: function () {
                return this.$route.name === 'eventDetail';
            },
            isUserOverview: function () {
                return this.$route.name === 'userOverview';
            },
            isGroupOverview: function () {
                return this.$route.name === 'groupOverview';
            },
            isGroupDetail: function () {
                return this.$route.name === 'groupDetail';
            }
        },
        mounted() {
            document.body.classList.add('bottomNavbar');
        },
        beforeDestroy() {
            document.body.classList.remove('bottomNavbar');
        }
    };
</script>

<style>
    .bottomNavbar {
        padding-bottom: 54px;
    }
</style>
