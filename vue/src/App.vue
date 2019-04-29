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
        <BottomNav></BottomNav>
    </v-app>
</template>

<script>
    import Navigation from '@/components/Navigation';
    import BottomNav from '@/components/BottomNav';

    export default {
        name: 'app',
        computed: {
            alert() {
                return this.$store.state.alert;
            }
        },
        watch: {
            $route(to, from) {
                // clear alert on location change
                this.$store.dispatch('alert/clear');
            }
        },
        components: {
            BottomNav,
            Navigation
        },
        created() {
            this.$i18n.locale = window.localStorage.getItem('locale');
        }
    };
</script>
