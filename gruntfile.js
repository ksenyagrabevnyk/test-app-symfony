'use strict';
module.exports = function (grunt) {

    // Load grunt tasks automatically
    require('load-grunt-tasks')(grunt);

    // Show grunt task time
    require('time-grunt')(grunt);

    // Grunt configuration
    grunt.initConfig({
        // Compile less to css
        less: {
            development: {
                options: {
                    compress: true,
                    optimization: 2
                },
                files: {
                    "web/includes/less/result.css": "web/includes/less/style.less"
                }
            }
        },
        // Watch for changes in live edit
        watch: {
            styles: {
                files: ['web/includes/less/*.less', 'web/includes/inspinia/less/*.less'],
                tasks: ['less'],
                options: {
                    nospawn: true,
                    livereload: '<%= connect.options.livereload %>'
                }
            }
        },
        // If you want to turn on uglify you will need write your angular code with string-injection based syntax
        // For example this is normal syntax: function exampleCtrl ($scope, $rootScope, $location, $http){}
        // And string-injection based syntax is: ['$scope', '$rootScope', '$location', '$http', function exampleCtrl ($scope, $rootScope, $location, $http){}]
        uglify: {
            options: {
                mangle: false
            }
        },
        concat: {
            options: {
                stripBanners: true
            },
            css: {
                src: [
                    'web/includes/css/font-awesome.min.css',
                    'web/includes/css/bootstrap.min.css',
                    'web/includes/css/animate.css',
                    'web/includes/less/result.css',
                ],
                dest: 'web/includes/css/style.min.css'
            },
            js: {
                src: [
                    'web/includes/inspinia/js/jquery-2.1.1.js',
                    'web/includes/inspinia/js/bootstrap.min.js',
                    'web/includes/inspinia/js/plugins/metisMenu/jquery.metisMenu.js',
                    'web/includes/inspinia/js/plugins/slimscroll/jquery.slimscroll.min.js',
                    'web/includes/inspinia/js/inspinia.js',
                    'web/includes/inspinia/js/plugins/pace/pace.min.js',
                    'web/includes/inspinia/js/plugins/flot/jquery.flot.js',
                    'web/includes/inspinia/js/plugins/flot/jquery.flot.tooltip.min.js',
                    'web/includes/inspinia/js/plugins/flot/jquery.flot.resize.js',
                    'web/includes/inspinia/js/plugins/chartJs/Chart.min.js',
                    'web/includes/inspinia/js/jquery-ui-1.10.4.min.js',
                ],
                dest: 'web/includes/js/scripts.min.js'
            }
        }
    });

    // Build version for production
    grunt.registerTask('run', [
        'less',
        'concat',
        'uglify',
    ]);
};
