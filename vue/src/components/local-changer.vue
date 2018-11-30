<template>
    <div class="locale-changer">
        <select class="custom-select" v-model="$i18n.locale">
            <option :key="`Lang${i}`" :value="lang" v-for="(lang, i) in langs">{{ lang }}</option>
        </select>
    </div>
</template>

<script>
    export default {
        name: 'local-changer',
        data() {
            return {langs: ['de', 'en']};
        }
    };
</script>

<style scoped>

</style>
