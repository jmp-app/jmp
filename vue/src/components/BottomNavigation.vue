<template>
    <nav class="navbar fixed-bottom navbar-light bg-light">
        <form class="form-inline">
            <button class="btn btn-primary nav-item mr-2" type="button">Übersicht</button>
            <button class="btn btn-primary nav-item mr-2" type="button">Kontrollieren</button>
        </form>
    </nav>
</template>

<script>
    export default {
        name: 'BottomNavigation',
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
