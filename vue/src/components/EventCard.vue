<template>
    <div :style="{ backgroundColor: event.color, color: getColor() }" class="card mb-3">
        <div class="card-header">
            <h5>{{event.title}}</h5>
        </div>
        <div class="card-body">
            <p class="card-text">
                Bla
                Bla
                Bla
            </p>
        </div>
    </div>
</template>

<script>
    import fontColorContrast from 'font-color-contrast';

    export default {
        name: 'EventCard',
        props: {
            event: {}
        },
        methods: {
            getColor: function () {
                return fontColorContrast(this.event.color);
            }
        }
    };
</script>

<style scoped>

</style>
