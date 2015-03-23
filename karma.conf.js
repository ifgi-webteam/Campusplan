// Karma configuration
// Generated on Sun Mar 22 2015 23:07:28 GMT+0100 (CET)

module.exports = function(config) {
  config.set({

    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '',


    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: ['mocha', 'sinon-chai'],


    // list of files / patterns to load in the browser
    files: [
      'bower_components/angular/angular.js',      
      'bower_components/jquery/dist/jquery.min.js',
      'bower_components/bootstrap/dist/js/bootstrap.min.js',
      'bower_components/angular-route/angular-route.min.js',
      'bower_components/angular-i18n/angular-locale_de-de.js',
      'bower_components/leaflet/dist/leaflet.js',
      'bower_components/angular-leaflet-directive/dist/angular-leaflet-directive.min.js',
      'bower_components/angular-local-storage/dist/angular-local-storage.min.js',
      'bower_components/angular-busy/dist/angular-busy.min.js',
      'bower_components/angular-piwik/dist/angular-piwik.js',
      'bower_components/Wicket/wicket.js',
      'bower_components/angular-mocks/angular-mocks.js',
      'js/script.js',
      'js/campusplan-angular.js',
      'js/campusplan-angular-config.js',
      'js/campusplan-angular-controllers.js',
      'js/campusplan-angular-services.js',
      'js/campusplan-angular-filters.js',
      'test/*.mocha.js'
    ],


    // list of files to exclude
    exclude: [
    ],


    // preprocess matching files before serving them to the browser
    // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
    preprocessors: {
        'js/*.js': ['coverage']
    },

    coverageReporter: {
        type: 'text-summary',
        dir: 'coverage/'
    },


    // test results reporter to use
    // possible values: 'dots', 'progress'
    // available reporters: https://npmjs.org/browse/keyword/karma-reporter
    reporters: ['progress', 'coverage'],


    // web server port
    port: 9876,


    // enable / disable colors in the output (reporters and logs)
    colors: true,


    // level of logging
    // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
    logLevel: config.LOG_DEBUG,


    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,


    // start these browsers
    // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
    browsers: ['PhantomJS'],


    // Continuous Integration mode
    // if true, Karma captures browsers, runs the tests and exits
    singleRun: false
  });
};
