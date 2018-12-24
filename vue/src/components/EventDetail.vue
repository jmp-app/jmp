<template>
    <div>
        <h1>{{event.title}}</h1>
        <table class="table table-responsive">
            <tbody>
            <tr>
                <th>{{ $t("eventType.title") }}</th>
                <td>{{event.eventType.title}}</td>
            </tr>
            <tr>
                <th>{{ $t("event.description") }}</th>
                <td>{{event.description}}</td>
            </tr>
            <tr>
                <th>{{ $t("event.fromTo") }}</th>
                <td>{{getDateTime()}}</td>
            </tr>
            <tr>
                <th>{{ $t("event.place") }}</th>
                <td>{{event.place}}</td>
            </tr>
            <tr>
                <th>{{ $t("event.groups") }}</th>
                <td>
                        <span :key="group.id" class="badge badge-pill badge-primary" v-for="group in event.groups">
                            {{group.name}}
                        </span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import dateFormat from 'dateformat';

    export default {
        name: 'EventDetail',
        props: {
            event: {}
        },
        methods: {
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
    tr {
        border-bottom: 1px solid #dee2e6;
    }
</style>
