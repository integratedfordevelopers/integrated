let Encore = require('@symfony/webpack-encore');

Encore.setOutputPath('./src/Bundle/IntegratedBundle/Resources/public')
    .setPublicPath('/bundles/integratedintegrated')
    .setManifestKeyPrefix('bundles/integratedintegrated')
    .addEntry('app', [
        './src/Bundle/ContentBundle/Resources/assets/sass/main.scss',
        './src/Bundle/CommentBundle/Resources/assets/css/comments.css',
        './src/Bundle/StorageBundle/Resources/assets/css/drag-drop.css',
        './src/Bundle/WorkflowBundle/Resources/assets/css/style.css',
    ])
    .addEntry('iframe', [
        './src/Bundle/BlockBundle/Resources/assets/css/iframe.css',
    ])
    .addEntry('tinymce', [
        './src/Bundle/FormTypeBundle/Resources/assets/css/tinymce.content.css',
        './src/Bundle/FormTypeBundle/Resources/assets/css/tinymce.editor.css',
    ])
    .cleanupOutputBeforeBuild()
    .enableSassLoader()
    .enableSourceMaps(false)
    .enableVersioning(false)
    .disableSingleRuntimeChunk()

module.exports = Encore.getWebpackConfig();
