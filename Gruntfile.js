/* global module:true */
/* global require:true */
var sass = require('node-sass');
var compass = require('compass-importer');
// Utiliser le module personnalisé envify pour spécifier les variables d'environnement
var envify = require('envify/custom')

module.exports = function (grunt) {

    grunt.initConfig({
        clean: {
            cache: {
                src:  "var/cache/grunt"
            },
            js: {
                src: "web/assets/js"
            },
            css: {
                src: "web/assets/css"
            }
        },

        sass: {
            dist: {
                files: {
                    'var/cache/grunt/sass_compiled/main.css': 'public/assets/sass/main.scss',
                    'var/cache/grunt/sass_compiled/print_sheet_style.css': 'public/assets/sass/print/print_sheet_style.scss'
                }
            },
            options: {
                outputStyle: 'expanded',
                precision: 5,
                implementation: sass,
                importer: compass
            }
        },
        concat: {
            js_main: {
                options: {
                    separator: ';'
                },
                nonull: true,
                src: [
                    'public/assets/js/fos_js_routes.js',
                    'node_modules/jquery/dist/jquery.js',
                    'node_modules/bootstrap/js/dist/alert.js',
                    'node_modules/bootstrap/js/dist/util.js',
                    'node_modules/bootstrap/js/dist/button.js',
                    'node_modules/bootstrap/js/dist/modal.js',
                    'node_modules/bootstrap/js/dist/collapse.js',
                    'node_modules/bootstrap/js/dist/dropdown.js',
                    'node_modules/bootstrap/js/dist/tab.js',
                    'node_modules/jquery.panzoom/dist/jquery.panzoom.js',
                    'node_modules/barcoder/lib/barcoder.js',
                    'node_modules/moment/moment.js',
                    'node_modules/pikaday-time/pikaday.js',
                    'node_modules/floatthead/dist/jquery.floatThead.js',
                    'node_modules/jquery.scrollto/jquery.scrollTo.js',
                    'node_modules/typeahead.js/dist/bloodhound.js',
                    'node_modules/typeahead.js/dist/typeahead.jquery.js',
                    'node_modules/jquery-loading-overlay/src/loading-overlay.js',
                    'node_modules/datatables.net-bs4/node_modules/datatables.net/js/jquery.dataTables.js',
                    'node_modules/simplemde/dist/simplemde.min.js',
                    'node_modules/js-cookie/src/js.cookie.js',
                    'node_modules/bootstrap-notify/bootstrap-notify.min.js',
                    'node_modules/select2/dist/js/select2.js',
                    'node_modules/select2/dist/js/i18n/fr.js',
                    'public/assets/js/form_label_submit.js',
                    'public/assets/js/float_thead.js',
                    'public/assets/js/keepSessionAlive.js',
                    'public/assets/js/polyfill_autoFocus.js',
                    'public/assets/js/overlay.js',
                    'public/assets/js/UI.js',
                    'public/assets/js/appSearch.js',
                    'node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js',
                    'public/assets/js/datatable.js',
                    'public/assets/js/Modal.js',
                    'public/assets/js/ModalEvents.js',
                    'public/assets/js/ModalHelpers.js',
                    'public/assets/js/confirmable.js',
                    'public/assets/js/simplemde.js',
                    'public/assets/js/clearForm.js',
                    'public/assets/js/form_change_confirm_exit.js',
                    'public/assets/js/removeAccents.js',
                    'public/assets/js/copyTableToClipboard.js',
                    'public/assets/js/LockPageDuringAjaxCall.js',
                    'public/assets/js/tab.js',
                    'public/assets/js/favorite.js',
                    'public/assets/js/AjaxSubmitForm.js',
                    'public/assets/js/Select2Input.js',
                    'public/assets/js/AutoComplete.js',
                    'public/assets/js/SiteSelect2.js',
                    'node_modules/keymaster/keymaster.js',
                    'node_modules/bootstrap-toggle/js/bootstrap-toggle.js',
                    'src/Decitre/Bundle/CoreBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/RepriseBundle/Resources/assets/js/add_ean.js',
                    'src/Decitre/Bundle/RepriseBundle/Resources/assets/js/clients.js',
                    'src/Decitre/Bundle/RepriseBundle/Resources/assets/js/ga_events.js',
                    'src/Decitre/Bundle/StockBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/ServiceAchatBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/LogistiqueBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/B2bBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/ServiceClientsBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/ServiceComptabiliteBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/UserQueryBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/NumerisationBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/MarketingBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/MagasinBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/ProductBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/ClientBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/AppBundle/Resources/assets/js/*'
                ],
                dest: 'app/cache/grunt/main.js'
            },
            css_main: {
                nonull: true,
                src: [
                    'node_modules/pikaday-time/css/pikaday.css',
                    'node_modules/simplemde/dist/simplemde.min.css',
                    'node_modules/bootstrap-toggle/css/bootstrap2-toggle.min.css',
                    'node_modules/select2/dist/css/select2.css',
                    'node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css',
                    'app/cache/grunt/sass_compiled/main.css',
                ],
                dest: 'app/cache/grunt/main.css'
            },
            css_print_sheet_style: {
                nonull: true,
                src: [
                    'app/cache/grunt/sass_compiled/print_sheet_style.css',
                ],
                dest: 'app/cache/grunt/print_sheet_style.css'
            }
        },
        terser: {
            options: {
                output: {
                    beautify: false,
                    quote_keys: true,
                    comments: false
                },
                compress: {
                    passes: 2,
                },
            },
            main: {
                files: [{
                    '<%= concat.js_main.dest %>': ['<%= concat.js_main.dest %>']
                },
                    {
                        expand: true,
                        cwd: 'app/cache/grunt/vue/',
                        src: ['*.js'],
                        dest: 'app/cache/grunt/vue/'
                }
                ]
            }
        },
        cssmin: {
            options: {
                keepSpecialComments: 0,
                noAdvanced: true
            },
            main: {
                files: {
                    'app/cache/grunt/main.css': ['app/cache/grunt/main.css'],
                    'app/cache/grunt/print_sheet_style.css' : ['app/cache/grunt/print_sheet_style.css']
                }
            }
        },

        hash: {
            options: {
                hashLength: 8,
                hashFunction: function (source, encoding) {
                    return require('crypto').createHash('sha1').update(source, encoding).digest('hex');
                }
            },
            js_main: {
                src: 'app/cache/grunt/main.js',
                dest: 'web/assets/js/'
            },
            css_main: {
                src: 'app/cache/grunt/main.css',
                dest: 'web/assets/css/'
            },
            css_print_sheet_style: {
                src: 'app/cache/grunt/print_sheet_style.css',
                dest: 'web/assets/css/'
            }
        },

        rev: {
            options: {
                algorithm: 'sha1',
                length: 8
            },
            js_main: {
                files: [{
                    src: 'app/cache/grunt/main.js'
                },
                    {
                        src: 'app/cache/grunt/vue/*.js'
                }]
            },
            css_main: {
                files: [{
                    src: 'app/cache/grunt/main.css'
                }]
            }
        },

        copy: {
            web_assets_js: {
                files: [
                    { expand: true, flatten: true, src: ['app/cache/grunt/*.main.js'], dest: 'web/assets/js/'},
                    { expand: true, flatten: true, src: ['app/cache/grunt/vue/*.js'], dest: 'web/assets/js/vue/'},
                ]
            },
            web_assets_css: {
                files: [
                    { expand: true, flatten: true, src: ['app/cache/grunt/*.main.css'], dest: 'web/assets/css/'},
                    { expand: true, flatten: true, src: ['app/cache/grunt/print_sheet_style.css'], dest: 'web/assets/css/'},
                ]
            },
            images: {
                files: [
                    {
                        expand: true,
                        cwd: 'public/assets/images/',
                        src: ['**/*.{png,jpg}'],
                        dest: 'web/assets/images/'
                }
                ]
            },
            web_assets_fonts: {
                files: [
                    { expand: true, flatten: true, src: ['public/assets/fonts/*'], dest: 'web/assets/fonts/'},
                ]
            }
        },

        watch: {
            js: {
                files: ['public/assets/js/**',
                    'src/Decitre/Bundle/CoreBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/RepriseBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/ServiceAchatBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/StockBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/LogistiqueBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/B2bBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/ServiceClientsBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/ServiceComptabiliteBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/UserQueryBundle/Resources/assets/js/*',
                    'src/Decitre/Bundle/NumerisationBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/MarketingBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/MagasinBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/ClientBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/AppBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/ProductBundle/Resources/assets/js/**'
                    ],
                tasks: ['dev-js']
            },
            sass: {
                files: [
                    'public/assets/sass/**',
                    'src/Decitre/Bundle/RepriseBundle/Resources/assets/sass/**',
                    'src/Decitre/Bundle/CoreBundle/Resources/assets/sass/**',
                    'src/Decitre/Bundle/ServiceAchatBundle/Resources/assets/sass/**',
                    'src/Decitre/Bundle/B2bBundle/Resources/assets/sass/**',
                    'src/Decitre/Bundle/LogistiqueBundle/Resources/assets/sass/**',
                    'src/Decitre/Bundle/ServiceClientsBundle/Resources/assets/sass/**',
                    'src/Decitre/Bundle/UserQueryBundle/Resources/assets/sass/**',
                    'src/Decitre/Bundle/ServiceComptabiliteBundle/Resources/assets/sass/**',
                    'src/Decitre/Bundle/NumerisationBundle/Resources/assets/js/**',
                    'src/Decitre/Bundle/MarketingBundle/Resources/assets/sass/**',
                    'src/Decitre/Bundle/MagasinBundle/Resources/assets/sass/**',
                    'src/Decitre/Bundle/AppBundle/Resources/assets/sass/**',
                    'src/Decitre/Bundle/ClientBundle/Resources/assets/sass/**',
                    'src/Decitre/Bundle/ProductBundle/Resources/assets/sass/**',
                    'src/Decitre/Bundle/StockBundle/Resources/assets/sass/**'
                ],
                tasks: ['dev-css']
            },
            fonts: {
                files: [
                    'public/assets/icons/*.svg'
                ],
                tasks: ['dev-fonts', 'dev-css']
            }
        },
        webfont: {
            icons: {
                src: 'public/assets/icons/*.svg',
                dest: 'web/assets/fonts',
                destCss: 'public/assets/sass/generated',
                options: {
                    templateOptions: {
                        baseClass: 'dct-icon',
                        classPrefix: 'dct-icon-',
                        mixinPrefix: 'dct-icon-'
                    },
                    relativeFontPath: '/assets/fonts/',
                    htmlDemo: false,
                    engine: 'node',
                    stylesheet: 'scss'
                }
            }
        },
        browserify: {
            dist: {
                files: [{
                    expand: true,
                    cwd: 'public/assets/js/vue/',
                    src:['*.js'],
                    dest:'app/cache/grunt/vue'
                }],
                options: {
                    watch: true,
                    keepalive : true,
                    configure: b => b
                        .transform('vueify')
                        .transform(
                            { global: true },
                            envify({ NODE_ENV: 'production' })
                        )
                        .bundle()
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-browserify');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-terser');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-text-replace');
    grunt.loadNpmTasks('grunt-webfont');
    grunt.loadNpmTasks('grunt-hash');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-file-rev');

    grunt.registerTask('dev-js', [
        'clean:cache',
        'clean:js',
        'concat',
        'browserify',
        'rev:js_main',
        'copy:web_assets_js'
    ]);
    grunt.registerTask('dev-css', [
        'clean:cache',
        'clean:css',
        'sass',
        'concat',
        'rev:css_main',
        'copy:web_assets_css'
    ]);
    grunt.registerTask('dev-fonts', [
        'webfont',
        'copy:web_assets_fonts'
    ]);

    grunt.registerTask('default-dev', [
        'clean',
        'webfont',
        'sass',
        'concat',
        'browserify',
        'rev',
        'copy'
    ]);


    grunt.registerTask('default', [
        'clean',
        'webfont',
        'sass',
        'concat',
        'browserify',
        'terser',
        'cssmin',
        'rev',
        'copy'
    ]);
};
