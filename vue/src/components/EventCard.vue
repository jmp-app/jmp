<template>
    <router-link :to="{ name: 'eventDetail', params:{ id: event.id, title: event.title } }" class="eventCard">
        <div :style="{ backgroundColor: event.eventType.color, color: getColor() }" class="card mb-3">
            <div class="card-header">
                <h5>{{event.title}}</h5>
            </div>
            <div class="card-body">
                <p class="card-text">
                    {{getDateTime()}} <br>
                    {{event.place}} <br>
                    {{event.description}}
                </p>
            </div>
        </div>
    </router-link>
</template>

<script>
    import fontColorContrast from 'font-color-contrast';
    import dateFormat from 'dateformat';

    export default {
        name: 'EventCard',
        props: {
            event: {}
        },
        methods: {
            getColor: function () {
                return fontColorContrast(this.event.eventType.color);
            },
            getDateTime: function () {
                const from = this.event.from;
                const to = this.event.to;
                let string = '';
                if (dateFormat(from, 'shortDate') === dateFormat(to, 'shortDate')) {
                    string = dateFormat(this.event.from, 'dd.mm.yyyy hh:MM "- "');
                    string += dateFormat(this.event.to, 'hh:MM "Uhr"');
                    return string;
                }
                string = dateFormat(this.event.from, 'dd.mm.yyyy hh:MM "Uhr - "');
                string += dateFormat(this.event.to, 'dd.mm.yyyy hh:MM "Uhr"');
                return string;
            }
        }
    };
</script>

<style scoped>
.eventCard {
    text-decoration: none;
}
</style>
