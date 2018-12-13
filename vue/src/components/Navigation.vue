<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark navbar-colored" v-if="show()">
        <button aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation"
                class="navbar-toggler"
                data-target="#navbarToggler" data-toggle="collapse" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarToggler">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                <router-link tag="li" class="nav-item" to="/" v-bind:class="{active: $route.name === 'home'}">
                    <a class="nav-link">Home</a>
                </router-link>
                <router-link tag="li" class="nav-item" to="/about" v-bind:class="{active: $route.name === 'about'}">
                    <a class="nav-link">About</a>
                </router-link>
                <li class="nav-item dropdown" v-if="showAdmin()">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Admin
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <router-link class="dropdown-item" to="/users" v-bind:class="{active: $route.name === 'users'}">Users</router-link>
                    </div>
                </li>
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
            },
            showAdmin() {
                const user = JSON.parse(localStorage.getItem('user'));
                return !!(user && user.isAdmin);
            }
        }
    };
</script>

<style scoped>
    .navbar-colored {
        background: linear-gradient(-90deg, hsl(235, 79%, 31%), hsl(5, 78%, 36%));
    }
</style>
