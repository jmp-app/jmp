<template>
    <form>
        <div class="form-group">
            <label for="regState">Registration-State</label>
            <select @change="handleChange()" class="form-control" id="regState" v-model="selected">
                <option :key="regState.id" v-bind:value="regState" v-for="regState in registrationStates">
                    {{regState.name}}
                </option>
            </select>
        </div>
        <div class="form-group" v-if="selected.reasonRequired">
            <label for="reason">Reason</label>
            <input class="form-control" id="reason" placeholder="Enter reason" type="text" v-model="reason">
        </div>
        <button @click="handleSubmit()" class="btn btn-danger" type="button" v-show="showButton">Submit</button>
    </form>
</template>

<script>
    export default {
        name: 'RegistrationForm',
        data: function () {
            return {
                selected: this.$store.state.registration.detail.registration.registrationState,
                reason: this.$store.state.registration.detail.registration.reason,
                showButton: false
            };
        },
        computed: {
            registrationStates() {
                return this.$store.state.registrationStates.all.registrationStates;
            },
            registration() {
                return this.$store.state.registration.detail.registration;
            }
        },
        methods: {
            handleChange: function () {
                console.log('Changed');
                this.showButton = true;
            },
            handleSubmit: function () {
                console.log(`Submit: ${this.selected.name}`);
                if (this.selected.reasonRequired) {
                    console.log(this.reason);
                }
            }
        },
        beforeMount() {
            this.$store.dispatch('registrationStates/getAll');
        }
    };
</script>

<style scoped>

</style>
