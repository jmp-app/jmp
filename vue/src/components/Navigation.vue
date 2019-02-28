<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark navbar-colored mb-3" v-if="show()">
        <button aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation"
                class="navbar-toggler"
                data-target="#navbarToggler" data-toggle="collapse" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarToggler">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                <router-link tag="li" class="nav-item" to="/" v-bind:class="{active: $route.name === 'eventOverview'}">
                    <a class="nav-link">{{ $t('nav.overview') }}</a>
                </router-link>
                <router-link class="nav-item" tag="li" to="/change-password"
                             v-bind:class="{active: $route.name === 'changePassword'}">
                    <a class="nav-link">{{ $t('password.change') }}</a>
                </router-link>
                <router-link class="nav-item" tag="li" to="/login">
                    <a class="nav-link">Logout</a>
                </router-link>
                <li class="nav-item dropdown" v-if="showAdmin()">
                    <a aria-expanded="false" aria-haspopup="true" class="nav-link dropdown-toggle"
                       data-toggle="dropdown" href="#" id="navbarDropdown" role="button"
                       v-bind:class="{active: $route.name === 'userOverview' || $route.name === 'groups'}">
                        Admin
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <router-link class="dropdown-item" to="/users">
                            {{ $tc('user', 2) }}
                        </router-link>
                        <router-link class="dropdown-item" to="/groups">
                            {{ $tc('group', 2) }}
                        </router-link>
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
