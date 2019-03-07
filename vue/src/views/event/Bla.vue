<template>
    <div v-if="event">
        <!--<h3 v-if="display() && edit()">{{event.title}}</h3>-->
        {{event}}
        <form @submit.prevent="handleSubmit()">
            <!--Title-->
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="title">{{$t('event.title')}}</label>
                <div class="col-sm-9">
                    <input :class="{ 'is-invalid': isTitleValid()}"
                           :readonly="display()"
                           class="form-control"
                           id="title"
                           type="text"
                           v-model="event.title">
                    <div class="invalid-feedback" v-show="isTitleValid()">
                        {{ $t("required") }}
                    </div>
                </div>
            </div>
            <!--Description-->
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="description">{{$t('event.description')}}</label>
                <div class="col-sm-9">
                    <textarea :readonly="display()"
                              class="form-control"
                              id="description"
                              v-model="event.description">
                    </textarea>
                </div>
            </div>
            <!--From-->
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="from">{{$t('event.from')}}</label>
                <div class="col-sm-9">
                    <input :class="{ 'is-invalid': submitted && !event.from }"
                           :readonly="display()"
                           class="form-control"
                           id="from"
                           type="datetime-local"
                           v-model="event.from">
                    <div class="invalid-feedback" v-show="submitted && !event.from">
                        {{ $t("required") }}
                    </div>
                </div>
            </div>
            <!--To-->
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="to">{{$t('event.to')}}</label>
                <div class="col-sm-9">
                    <input :class="{ 'is-invalid': submitted && !event.to }"
                           :readonly="display()"
                           class="form-control"
                           id="to"
                           type="datetime-local"
                           v-model="event.to">
                    <div class="invalid-feedback" v-show="submitted && !event.to">
                        {{ $t("required") }}
                    </div>
                </div>
            </div>
            <!--Place-->
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="place">{{$t('event.place')}}</label>
                <div class="col-sm-9">
                    <input :readonly="display()"
                           class="form-control"
                           id="place"
                           type="text"
                           v-model="event.place">
                </div>
            </div>
            <!--EventType-->
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="eventType">{{$t('eventType.title')}}</label>
                <div class="col-sm-9">
                    <select :class="{ 'is-invalid': submitted && !event.eventType.id }"
                            :disabled="display()"
                            class="form-control"
                            id="eventType"
                            v-model="event.eventType.title">
                        <option :id="event.eventType.id">{{event.eventType.title}}</option>
                    </select>
                    <div class="invalid-feedback" v-show="submitted && !event.eventType.id">
                        {{ $t("required") }}
                    </div>
                </div>
            </div>
            <!--DefaultRegistrationState-->
            <div class="form-group row" v-if="!isIntegrated">
                <label class="col-sm-3 col-form-label"
                       for="defaultRegistrationState">{{$t('registration.state')}}</label>
                <div class="col-sm-9">
                    <select :class="{ 'is-invalid': submitted && !event.defaultRegistrationState.id }"
                            :disabled="display()"
                            class="form-control"
                            id="defaultRegistrationState"
                            v-model="event.defaultRegistrationState">
                        <option :key="regState.id" v-bind:value="regState" v-for="regState in registrationStates">
                            {{regState.name}}
                        </option>
                    </select>
                    <div class="invalid-feedback" v-show="submitted && !event.defaultRegistrationState.id">
                        {{ $t("required") }}
                    </div>
                </div>
            </div>
            <!--Buttons-->
            <div v-if="isAdmin()">
                <!--Cancel-->
                <div class="form-group row" v-if="create() || edit()">
                    <div class="col-sm-9">
                        <button @click="handleCancel()" class="btn mr-2" type="button" v-if="edit()">{{$t('cancel')}}
                        </button>
                        <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
                    </div>
                </div>
                <!--Edit-->
                <div class="form-group row" v-if="display()">
                    <div class="col-sm-9">
                        <button @click="mode = 'edit'" class="btn mr-2" type="button">{{$t('edit')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        name: 'Bla',
        props: {
            isIntegrated: {
                default: false,
                type: Boolean
            }
        },
        data: function () {
            return {
                mode: 'display', // display, edit, create
                submitted: false
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
            handleSubmit: function () {
                console.log('Submit');
            },
            handleCancel: function () {
                if (this.create()) {
                    this.$store.state.events.detail.event = {};
                } else {
                    this.renewUserFromDb();
                    this.mode = 'display';
                }
                this.submitted = false;
            },
            renewUserFromDb: function () {
                const eventId = this.$route.params.id;
                this.$store.dispatch('events/getEventById', {eventId});
            },
            isAdmin: function () {
                const user = JSON.parse(localStorage.getItem('user'));
                return !!(user && user.isAdmin === 1);
            },
            isTitleValid: function () {
                return this.submitted && !this.event.title;
            }
        },
        mounted() {
            if (!this.isIntegrated) {
                const eventId = this.$route.params.id;
                // eslint-disable-next-line
                if (eventId == 0) {
                    this.mode = 'create';
                    this.$store.dispatch('events/getEmptyEvent');
                } else {
                    this.$store.dispatch('events/getEventById', {eventId});
                }
                this.$store.dispatch('registrationStates/getAll');
            }
        }
    };
</script>

<style scoped>

</style>
