<template>
    <v-container>
        <v-form
                ref="form"
                v-if="eventType"
                v-model="valid"
        >
            <v-text-field
                    :counter="titleRules.counter"
                    :label="$t('eventType.title')"
                    :readonly="display()"
                    :rules="titleRules.rules"
                    required
                    v-model="eventType.title"
            ></v-text-field>

            <v-text-field
                    :label="$t('eventType.color')"
                    :readonly="display()"
                    :rules="colorRules.rules"
                    required
                    v-model="eventType.color"
            ></v-text-field>
            <v-layout>
                <v-btn
                        :disabled="!valid"
                        @click="handleSubmit()"
                        color="success"
                        v-if="create() || edit()"
                >
                    {{$t('submit')}}
                </v-btn>
                <v-btn
                        @click="handleEdit()"
                        color="primary"
                        v-if="display()"
                >
                    {{$t('edit')}}
                </v-btn>
                <v-btn
                        @click="handleCancel()"
                        v-if="create() || edit()"
                >
                    {{$t('cancel')}}
                </v-btn>
                <v-btn
                        @click="handleDelete()"
                        color="error"
                        v-if="edit() || display()"
                >
                    {{$t('delete')}}
                </v-btn>
            </v-layout>
        </v-form>
    </v-container>
</template>

<script>
    export default {
        name: 'adminEventTypeDetail',
        data: function () {
            return {
                mode: 'display', // display, edit, create
                submitted: false,
                valid: true,
                nameRules: {
                    counter: 50,
                    rules:
                        [
                            v => !!v || `${this.$t('fieldIsRequired', {fieldname: this.$t('eventType.title')})}`,
                            v => (v && v.length <= 50) || `${this.$t('fieldValueLength', {
                                fieldname: this.$t('eventType.title'),
                                lenght: 50
                            })}`
                        ]
                },
                reasonRequiredRules: {
                    rules:
                        [
                            v => !!v || `${this.$t('fieldIsRequired', {fieldname: this.$t('eventType.color')})}`
                        ]
                }
            }
                ;
        },
        computed: {
            eventType() {
                return this.$store.state.eventType.data.eventType;
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
            handleSubmit: function () {
                this.submitted = true;
                if (this.mode === 'create') {
                    this.createNewEventType();
                } else if (this.mode === 'edit') {
                    this.changeEventType();
                } else {
                    this.submitted = false;
                }
            },
            handleEdit: function () {
                this.mode = 'edit';
            },
            createNewEventType: function () {
                let eventType = this.eventType;
                if (!eventType.title || !eventType.color) {
                    return;
                }
                this.$store.dispatch('eventType/create', {eventType});
            },
            changeEventType: function () {
                let eventType = this.eventType;
                this.$store.dispatch('eventType/update', {eventType});
            },
            handleCancel: function () {
                if (this.create()) {
                    this.$store.state.eventType.data.eventType = {};
                } else {
                    this.reloadEventType();
                    this.mode = 'display';
                }
                this.submitted = false;
            },
            handleDelete: function () {
                let eventType = this.eventType;
                this.$store.dispatch('eventType/delete', {eventType});
            },
            handleMutation: function (mutation) {
                switch (mutation.type) {
                    case 'eventType/requestFailure':
                        this.$store.dispatch('alert/error', 'Not Found', {root: true});
                        break;
                    case 'eventType/updateSuccess':
                        this.reloadEventType();
                        this.mode = 'display';
                        break;
                    case 'eventType/createSuccess':
                        this.$router.push('/eventTypes');
                        break;
                    case 'eventType/deleteSuccess':
                        this.$store.dispatch('eventTypes/getAll');
                        this.$router.push('/eventTypes');
                        break;
                }
            },
            reloadEventType: function () {
                const id = this.$route.params.id;
                this.$store.dispatch('eventType/get', {id});
            }
        },
        created() {
            this.$store.subscribe(mutation => {
                this.handleMutation(mutation);
            });
        },
        mounted() {
            const id = this.$route.params.id;
            // eslint-disable-next-line
            if (id == 0) {
                this.mode = 'create';
                this.$store.state.eventType.data.eventType = {};
            } else {
                this.$store.dispatch('eventType/get', {id});
            }
        }
    };
</script>

<style scoped>

</style>
