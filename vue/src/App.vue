<template>
    <div id="app">
        <header>
            <Navigation/>
        </header>
        <div class="container">
            <router-view/>
        </div>
        <div :class="`alert ${alert.type}`" v-if="alert.message">{{alert.message}}</div>
    </div>
</template>

<script>
    import Navigation from '@/components/navigation.vue';

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
        }
    };
</script>

<style>
</style>
