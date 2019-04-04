<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <v-toolbar class="navbar-colored" dark v-if="show()">
        <v-toolbar-items>
            <v-btn class="hidden-xs-only" flat to="/">{{ $t('nav.overview') }}</v-btn>
            <v-btn class="hidden-sm-and-up" icon to="/">
                <v-icon>view_day</v-icon>
            </v-btn>
            <v-menu offset-y open-on-hover v-if="showAdmin()">
                <template v-slot:activator="{ on }">
                    <v-btn flat v-on="on">
                        Admin
                    </v-btn>
                </template>
                <v-list>
                    <v-list-tile>
                        <v-btn flat to="/users">{{ $tc('user', 2) }}</v-btn>
                    </v-list-tile>
                    <v-list-tile>
                        <v-btn flat to="/groups">{{ $tc('group', 2) }}</v-btn>
                    </v-list-tile>
                </v-list>
            </v-menu>
            <v-btn class="hidden-xs-only" flat to="/change-password">{{ $t('password.change') }}</v-btn>
            <v-btn class="hidden-sm-and-up" icon to="/change-password">
                <v-icon>security</v-icon>
            </v-btn>
        </v-toolbar-items>

        <v-spacer></v-spacer>

        <LocalChanger class="nav-item dropdown"/>
        <v-btn class="hidden-xs-only" flat to="/login">Logout</v-btn>
        <v-btn class="hidden-sm-and-up" icon to="/login">
            <v-icon>exit_to_app</v-icon>
        </v-btn>
    </v-toolbar>
</template>

<script>
    import LocalChanger from '@/components/LocalChanger';

    export default {
        name: 'Navigation',
        components: {LocalChanger},
        methods: {
            show() {
                return this.$route.name !== 'login';
            },
            showAdmin() {
                const user = JSON.parse(localStorage.getItem('user'));
                return !!(user && user.isAdmin === 1);
            }
        }
    };
</script>

<style scoped>
    .navbar-colored {
        background: linear-gradient(-90deg, hsl(235, 79%, 31%), hsl(5, 78%, 36%));
    }
</style>
