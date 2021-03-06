module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        less: {
          development: {
            options: {
              paths: ["assets"]
            },
            files: {
              "assets/style.css": "less/style.less"
            }
          },
          production: {
            options: {
              paths: ["assets"],
              compress: true,
              cleancss: true
            },
            files: {
              "assets/combined.css": "less/style.less"
            }
          }
        },
    });


    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.registerTask('default', ['less']);
};