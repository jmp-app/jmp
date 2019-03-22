<template>
    <v-app>
        <div id="app">
            <header>
                <Navigation/>
            </header>
            <div class="container">
                <router-view/>
                <div :class="`alert ${alert.type}`" v-if="alert.message">{{alert.message}}</div>
            </div>
        </div>
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

<style>
</style>
