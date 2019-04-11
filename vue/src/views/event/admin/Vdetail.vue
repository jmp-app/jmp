<template>
    <div>
        <v-form
                lazy-validation
                ref="form"
                v-if="event"
                v-model="valid"
        >
            <v-text-field
                    :counter="50"
                    :label="$t('event.title')"
                    :readonly="display()"
                    :rules="titleRules"
                    required
                    v-model="event.title"
            ></v-text-field>

            <!-- Buttons -->
            <div>
                <v-btn
                        :disabled="!valid"
                        @click="validate"
                        color="success"
                        v-if="edit()"
                >
                    Submit
                </v-btn>
                <v-btn
                        @click="mode = 'edit'"
                        color="primary"
                        v-if="display()"
                >
                    Edit
                </v-btn>
                <v-btn
                        @click="reset"
                        color="error"
                        v-if="create() || edit()"
                >
                    Reset
                </v-btn>
            </div>

        </v-form>
        <div class="text-xs-center">
            <v-progress-circular
                    color="primary"
                    indeterminate
                    v-if="!event"
            ></v-progress-circular>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Vdetail',
        data: function () {
            return {
                mode: 'display', // display, edit, create
                valid: true,
                titleRules: [
                    v => !!v || `${this.$t('fieldIsRequired', {fieldname: this.$t('event.title')})}`,
                    v => (v && v.length <= 50) || `${this.$t('fieldValueLength', {
                        fieldname: this.$t('event.title'),
                        lenght: 50
                    })}`
                ]
            };
        },
        computed: {
            event() {
                return this.$store.state.events.detail.event;
            },
            registrationStates() {
                return this.$store.state.registrationStates.all.registrationStates;
            }
        },
        methods: {
            create: function () {
                return (this.mode === 'create');
            },
            edit: function () {
                return (this.mode === 'edit');
            },
            display: function () {
                return (this.create() === false && this.edit() === false);
            },
            validate: function () {
                if (this.$refs.form.validate()) {
                    console.log('Submit');
                }
            },
            reset() {
                this.$refs.form.reset();
                this.getEvent();
                if (this.edit()) {
                    this.mode = 'display';
                }
            },
            getEvent: function () {
                const eventId = this.$route.params.id;
                // eslint-disable-next-line
                if (eventId == 0) {
                    this.mode = 'create';
                    this.$store.dispatch('events/getEmptyEvent');
                } else {
                    this.$store.dispatch('events/getEventById', {eventId});
                }
            }
        },
        mounted() {
            this.getEvent();
            this.$store.dispatch('registrationStates/getAll');
        }
    };
</script>

<style scoped>

</style>
