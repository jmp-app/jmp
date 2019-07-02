<template>
    <v-container>
        <v-form
                ref="form"
                v-if="registrationState"
                v-model="valid"
        >
            <v-text-field
                    :counter="nameRules.counter"
                    :label="$t('registrationState.name')"
                    :readonly="display()"
                    :rules="nameRules.rules"
                    required
                    v-model="registrationState.name"
            ></v-text-field>
            <v-checkbox
                    :label="$t('registrationState.reasonRequired')"
                    :readonly="display()"
                    v-model="registrationState.reasonRequired"
            ></v-checkbox>
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
                            v => !!v || `${this.$t('fieldIsRequired', {fieldname: this.$t('registrationState.name')})}`,
                            v => (v && v.length <= 50) || `${this.$t('fieldValueLength', {
                                fieldname: this.$t('registrationState.name'),
                                lenght: 50
                            })}`
                        ]
                }
            }
                ;
        },
        computed: {
            registrationState() {
                return this.$store.state.registrationState.data.registrationState;
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
                let registrationState = this.registrationState;
                if (!registrationState.name || !registrationState.reasonRequired) {
                    return;
                }
                this.$store.dispatch('registrationState/create', {registrationState});
            },
            changeEventType: function () {
                let registrationState = this.registrationState;
                this.$store.dispatch('registrationState/update', {registrationState});
            },
            handleCancel: function () {
                if (this.create()) {
                    this.$store.state.registrationState.data.registrationState = {};
                } else {
                    this.reloadEventType();
                    this.mode = 'display';
                }
                this.submitted = false;
            },
            handleDelete: function () {
                let registrationState = this.registrationState;
                this.$store.dispatch('registrationState/delete', {registrationState});
            },
            handleMutation: function (mutation) {
                switch (mutation.type) {
                    case 'registrationState/requestFailure':
                        this.$store.dispatch('alert/error', 'Not Found', {root: true});
                        break;
                    case 'registrationState/updateSuccess':
                        this.reloadEventType();
                        this.mode = 'display';
                        break;
                    case 'registrationState/createSuccess':
                        this.$router.push('/registrationStates');
                        break;
                    case 'registrationState/deleteSuccess':
                        this.$store.dispatch('registrationStates/getAll');
                        this.$router.push('/registrationStates');
                        break;
                }
            },
            reloadEventType: function () {
                const id = this.$route.params.id;
                this.$store.dispatch('registrationState/get', {id});
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
                this.$store.state.registrationState.data.registrationState = {};
            } else {
                this.$store.dispatch('registrationState/get', {id});
            }
        }
    };
</script>

<style scoped>

</style>
