const mix = require('laravel-mix');

mix.setPublicPath('dist');

mix.styles([
    'assets/css/normalize.css',
    'assets/css/header.css',
    'assets/css/footer.css',
    'assets/css/animate.css',
    'assets/css/areas.css',
    'assets/css/articels.css',
    'assets/css/article-name.css',
    'assets/css/city.css',
    'assets/css/contact.css',
    'assets/css/factory-det.css',
    'assets/css/factorys.css',
    'assets/css/flat-det.css',
    'assets/css/flats.css',
    'assets/css/hiring.css',
    'assets/css/home.css',
    'assets/css/motawren.css',
    'assets/css/motawren-det.css',
    'assets/css/projects.css',
    'assets/css/rtl.css',
    'assets/css/services.css',
    'assets/css/we.css'
], 'dist/css/all.css');

mix.scripts([
    'assets/js/jquery.js',
    'assets/js/about-us.js',
    'assets/js/areas.js',
    'assets/js/articels.js',
    'assets/js/bootstrap.min.js',
    'assets/js/city.js',
    'assets/js/compound.js',
    'assets/js/contact.js',
    'assets/js/counter.js',
    'assets/js/developers.js',
    'assets/js/factories.js',
    'assets/js/factory-det.js',
    'assets/js/flats.js',
    'assets/js/hiring.js',
    'assets/js/home.js',
    'assets/js/job-application.js',
    'assets/js/motawren-det.js',
    'assets/js/projects.js',
    'assets/js/script.js',
    'assets/js/services.js',
    'assets/js/single.js',
    'assets/js/single-properties.js',
    'assets/js/wow.js'
], 'dist/js/all.js');

mix.version();
