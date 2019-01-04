<template>
    <div class="d-flex justify-content-center">
        <div class="card" id="change-password">
            <form class="card-body">
                <input type="password" name="new-password" class="form-control" placeholder="New Password">
                <input type="password" name="new-password-repeat" class="form-control" placeholder="Repeat Password">
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ChangePassword'
    };
</script>

<style scoped>
    #change-password {
        max-width: 35rem;
    }
</style>
