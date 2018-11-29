<template>
    <div>
        <h1>Login</h1>
        <form>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="username">Benutzername</label>
                <div class="col-sm-9">
                    <input class="form-control" id="username" placeholder="Benutzername eingeben" type="text">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="password">Passwort</label>
                <div class="col-sm-9">
                    <input class="form-control" id="password" placeholder="Passwort eingeben" type="password">
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Anmelden</button>
        </form>
    </div>
</template>

<script>
    export default {
        name: 'Login'
    };
</script>

<style scoped>

</style>
