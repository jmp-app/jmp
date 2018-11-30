<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark navbar-colored mb-3" v-if="show()">
        <button aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation"
                class="navbar-toggler"
                data-target="#navbarToggler" data-toggle="collapse" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarToggler">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                <router-link class="nav-item nav-link" to="/" v-bind:class="{active: $route.name === 'eventOverview'}">
                    Overview
                </router-link>
                <router-link class="nav-item nav-link" to="/sample-home"
                             v-bind:class="{active: $route.name === 'home'}">
                    Home
                </router-link>
                <router-link class="nav-item nav-link" to="/sample-about"
                             v-bind:class="{active: $route.name === 'about'}">
                    About
                </router-link>
            </ul>
            <div class="my-2 my-lg-0">
                <LocalChanger class="nav-item dropdown"/>
            </div>
        </div>
    </nav>
</template>

<script>
    import LocalChanger from '@/components/local-changer';

    export default {
        name: 'Navigation',
        components: {LocalChanger},
        methods: {
            show() {
                return this.$route.name !== 'login';
            }
        }
    };
</script>

<style scoped>
    .navbar-colored {
        background: linear-gradient(-90deg, hsl(235, 79%, 31%), hsl(5, 78%, 36%));
    }
</style>
