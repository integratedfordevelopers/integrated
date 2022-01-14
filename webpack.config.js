let Encore = require('@symfony/webpack-encore');

Encore.setOutputPath('./src/Resources/public')
    .setPublicPath('/')
    .setManifestKeyPrefix('bundles/integrated')
    .addEntry('app', [
        './src/Bundle/ContentBundle/Resources/public/sass/main.scss',
    ])
    .cleanupOutputBeforeBuild()
    .enableSassLoader()
    .enableSourceMaps(false)
    .enableVersioning(false)
    .disableSingleRuntimeChunk()

module.exports = Encore.getWebpackConfig();
