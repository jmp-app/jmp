<template>
    <!--<div :style="{ backgroundColor: event.color, color: getColor() }" class="card mb-3">-->
    <div class="card mb-3">
        <div class="card-header">
            <!--<h5>{{event.title}}</h5>-->
            <h5>{{event.name.first}}</h5>
        </div>
        <div class="card-body">
            <p class="card-text">
                {{getDateTime()}} <br>
                <!--{{event.location}}-->
                {{event.location.city}}
            </p>
        </div>
    </div>
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
                return fontColorContrast(this.event.color);
            },
            getDateTime: function () {
                // const from = this.event.from;
                const from = this.event.dob.date;
                // const to = this.event.to;
                const to = this.event.registered.date;
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

</style>
