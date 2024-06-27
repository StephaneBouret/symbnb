const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('addEquipment', './assets/js/addEquipment.js')
    .addEntry('carousel', './assets/js/carousel.js')
    .addEntry('carouselComment', './assets/js/carouselComment.js')
    .addEntry('countDescription', './assets/js/countDescription.js')
    .addEntry('countHostDescription', './assets/js/countHostDescription.js')
    .addEntry('countTitle', './assets/js/countTitle.js')
    .addEntry('deleteAccount', './assets/js/deleteAccount.js')
    .addEntry('flatpickr', './assets/js/flatpickr.min.js')
    .addEntry('formHandling', './assets/js/formHandling.js')
    .addEntry('increment', './assets/js/increment.js')
    .addEntry('incrementCreateAd', './assets/js/incrementCreateAd.js')
    .addEntry('incrementEditFloor', './assets/js/incrementEditFloor.js')
    .addEntry('incrementGuestCreate', './assets/js/incrementGuestCreate.js')
    .addEntry('loadMoreComments', './assets/js/loadMoreComments.js')
    .addEntry('location', './assets/js/location.js')
    .addEntry('locationEdit', './assets/js/locationEdit.js')
    .addEntry('main', './assets/js/main.js')
    .addEntry('modifyEquipment', './assets/js/modifyEquipment.js')
    .addEntry('payment', './assets/js/payment.js')
    .addEntry('pictures', './assets/js/pictures.js')
    .addEntry('picturesEdit', './assets/js/picturesEdit.js')
    .addEntry('rating', './assets/js/rating.js')
    .addEntry('showMap', './assets/js/showMap.js')
    .addEntry('sortComments', './assets/js/sortComments.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    .configureImageRule({
        // tell Webpack it should consider inlining
        type: 'asset',
        //maxSize: 4 * 1024, // 4 kb - the default is 8kb
    })

    .configureFontRule({
        type: 'asset',
        //maxSize: 4 * 1024
    })

    .copyFiles({
        from: './assets/img',
        to: 'img/[path][name].[hash:8].[ext]'
    })

    .copyFiles({
        from: './assets/media',
        to: 'media/[path][name].[hash:8].[ext]'
    })

    .copyFiles({
        from: './assets/fonts',
        to: 'fonts/[path][name].[hash:8].[ext]'
    })

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // configure Babel
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
