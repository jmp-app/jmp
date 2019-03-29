<template>
    <div class="locale-changer">
        <select class="custom-select" v-model="$i18n.locale" v-on:change="handleChange()">
            <option :key="`Lang${i}`" :value="lang" v-for="(lang, i) in langs">{{ lang }}</option>
        </select>
    </div>
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

</style>
