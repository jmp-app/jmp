<template>
    <form @submit.prevent="handleSubmit" v-if="eventType">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="title">{{$t('eventType.title')}}</label>
            <div class="col-sm-9">
                <input :class="{ 'is-invalid': submitted && !eventType.title }" :readonly="display()"
                       class="form-control"
                       id="title" type="text"
                       v-model="eventType.title">
                <div class="invalid-feedback" v-show="submitted && !eventType.title">
                    {{ $t("eventType.detail.titleRequired") }}
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="color">{{$t('eventType.color')}}</label>
            <div class="col-sm-9">
                <input :class="{ 'is-invalid': submitted && !eventType.color }" :readonly="display()"
                       class="form-control" id="color" type="text"
                       v-model="eventType.color">
                <div class="invalid-feedback" v-show="submitted && !eventType.color">
                    {{ $t("eventType.detail.colorRequired") }}
                </div>
                <chrome-picker v-model="eventType.color"></chrome-picker>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-9">
                <v-btn @click="handleCancel()" type="button" v-if="edit()">{{$t('cancel')}}</v-btn>
                <v-btn @click="handleEdit()" type="button" v-if="display()">{{$t('edit')}}</v-btn>
                <v-btn color="info" type="submit" v-if="edit() || create()">{{$t('save')}}</v-btn>
                <v-btn @click="handleDelete()" color="error" type="button" v-if="edit() || display()">{{$t('delete')}}
                </v-btn>
            </div>
        </div>
    </form>
</template>

<script>
    export default {
        name: 'adminEventTypeDetail',
        data: function () {
            return {
                mode: 'display', // display, edit, create
                submitted: false,
                eventTypeOnSubmit: {}
            };
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
                this.eventTypeOnSubmit = this.eventType;
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
