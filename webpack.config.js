var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/js/app.js')
    .addEntry('favicon', './assets/images/favicon.ico')
    .addEntry('cron-expresson', './assets/images/workflow.png')

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
