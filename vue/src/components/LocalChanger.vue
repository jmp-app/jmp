<template>
    <v-select
            :items="langs"
            class="localChanger"
            v-model="$i18n.locale"
            v-on:change="handleChange()"
    ></v-select>
</template>

<script>
    export default {
        name: 'LocalChanger',
        data() {
            return {langs: ['de', 'en']};
        },
        methods: {
            handleChange: function () {
                window.localStorage.setItem('locale', this.$i18n.locale);
                this.$vuetify.lang.current = this.$i18n.locale;
            }
        },
        created() {
            this.$i18n.locale = window.localStorage.getItem('locale');
            if (!this.$i18n.locale) {
                this.$i18n.locale = process.env.VUE_APP_I18N_LOCALE;
            }
            this.$vuetify.lang.current = this.$i18n.locale;
        }
    };
</script>

<style scoped>
    .localChanger {
        max-width: 55px;
    }
</style>
