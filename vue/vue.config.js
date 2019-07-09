module.exports = {
    pluginOptions: {
        i18n: {
            locale: 'de',
            fallbackLocale: 'en',
            localeDir: 'locales',
            enableInSFC: false
        },
        pwa: {
            name: 'JMP',
            themeColor: '#a32014'
        }
    },
    chainWebpack: (config) => {
        config.resolve.symlinks(false);
    }
};
