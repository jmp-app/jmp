<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <v-dialog max-width="600px" v-model="dialog">
        <template v-slot:activator="{ on }">
            <div class="text-xs-center">
                <v-chip
                        :color="event.eventType.color"
                        class="hidden-xs-only"
                        small
                        v-on="on"
                >{{event.title}}
                </v-chip>
                <v-chip
                        :color="event.eventType.color"
                        class="hidden-sm-and-up ma-0 pa-0"
                        small
                        v-on="on">
                </v-chip>
            </div>
        </template>
        <v-card>
            <v-card-title
                    class="headline grey lighten-2"
                    primary-title
            >{{ event.title }}
            </v-card-title>
            <v-card-text>
                <RegistrationForm
                        :defaultRegistrationState="event.defaultRegistrationState"
                        :event="event"
                        :user="user"
                        v-if="registration"
                ></RegistrationForm>
                <div class="text-xs-center" v-if="!registration">
                    <v-progress-circular
                            color="primary"
                            indeterminate
                    ></v-progress-circular>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                        @click="dialog = false"
                        color="primary"
                        flat
                >{{ $t('close')}}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import RegistrationForm from './RegistrationForm';
    import EventDetail from './EventDetail';

    export default {
        name: 'CalendarMonthlyEvent',
        components: {RegistrationForm, EventDetail},
        props: ['event', 'user'],
        data: () => ({
            dialog: false
        }),
        mounted() {
            let eventId = this.event.id;
            let userId = this.user.id;

            this.$store.dispatch('registration/getRegistrationByEventIdAndUserId', {eventId, userId});
        },
        computed: {
            registration() {
                return this.$store.state.registration.detail.registration;
            }
        }
    };
</script>

<style scoped>

</style>
