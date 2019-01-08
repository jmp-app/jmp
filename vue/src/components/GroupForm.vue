<template>
    <div>
        <div class="row my-3">
            <div class="col">
                <label for="name">{{ $t('group.name') }}</label>
                <input class="form-control" id="name" name="name" required type="text" v-model="group.name">
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'GroupForm',
        props: {
            group: {
                type: Object,
                required: true
            }
        }
    };
</script>
