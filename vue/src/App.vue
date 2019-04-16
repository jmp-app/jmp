<template>
    <v-app>
        <header>
            <Navigation/>
        </header>
        <v-content>
            <v-container>
                <router-view/>
                <div :class="`alert ${alert.type}`" v-if="alert.message">{{alert.message}}</div>
            </v-container>
        </v-content>
        <v-bottom-nav
                :value="!isView('login')"
                app
                fixed
        >
            <v-btn
                    @click="navigateBack()"
                    active-class
                    style="text-decoration: none; color: black !important;"
            >
                <span>Back</span>
                <v-icon>arrow_back</v-icon>
            </v-btn><!-- Back -->
            <v-btn
                    active-class
                    style="text-decoration: none; color: black !important;"
                    to="/events/0"
                    v-if="isView('adminEventOverview')"
            >
                <span>{{$t('create')}}</span>
                <v-icon>add</v-icon>
            </v-btn><!-- Create Event -->
            <v-btn
                    active-class
                    style="text-decoration: none; color: black !important;"
                    to="/users/0"
                    v-if="isView('userOverview')"
            >
                <span>{{$t('create')}}</span>
                <v-icon>add</v-icon>
            </v-btn><!-- Create User -->
            <v-btn
                    active-class
                    style="text-decoration: none; color: black !important;"
                    to="/groups/0"
                    v-if="isView('groupOverview')"
            >
                <span>{{$t('create')}}</span>
                <v-icon>add</v-icon>
            </v-btn><!-- Create Group -->
            <v-btn
                    :to="`/groups/${$route.params.id}/addMembers`"
                    active-class
                    style="text-decoration: none; color: black !important;"
                    v-if="isView('groupDetail')"
            >
                <span>{{$t('group.detail.AddMembers')}}</span>
                <v-icon>build</v-icon>
            </v-btn><!-- Edit Members from Group -->
        </v-bottom-nav>
    </v-app>
</template>

<script>
    import Navigation from '@/components/Navigation';

    export default {
        name: 'app',
        computed: {
            alert() {
                return this.$store.state.alert;
            }
        },
        methods: {
            navigateBack: function () {
                if (window.history.length > 1) {
                    this.$router.go(-1);
                }
            },
            isView: function (viewName) {
                return this.$route.name === viewName;
            }
        },
        watch: {
            $route(to, from) {
                // clear alert on location change
                this.$store.dispatch('alert/clear');
            }
        },
        components: {
            Navigation
        },
        created() {
            this.$i18n.locale = window.localStorage.getItem('locale');
        }
    };
</script>
