<template>
    <v-card
            :color="event.eventType.color"
            :hover="true"
            :style="{ color: getColor() }"
            :to="{ name: 'eventDetail', params:{ id: event.id, title: event.title } }"
            style="text-decoration: none"
    >
        <v-card-title>
            <span class="title">{{event.title}}</span>
        </v-card-title>
        <v-divider style="margin: 0;"></v-divider>
        <v-card-text>
                <span class="text">
                    {{getDateTime()}} <br>
                    {{event.place}} <br>
                    {{event.description}}
                </span>
        </v-card-text>
    </v-card>
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
                    string = dateFormat(this.event.from, 'dd.mm.yyyy HH:MM "- "');
                    string += dateFormat(this.event.to, 'HH:MM "Uhr"');
                    return string;
                }
                string = dateFormat(this.event.from, 'dd.mm.yyyy HH:MM "Uhr - "');
                string += dateFormat(this.event.to, 'dd.mm.yyyy HH:MM "Uhr"');
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
