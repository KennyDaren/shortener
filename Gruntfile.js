module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		less: {
			development: {
				options: {
					paths: ['www/css']
				},
				files: {
					'www/css/custom.css': 'www/css/custom.less'
				}
			},
			production: {
				options: {
					paths: ['www/css']
				},
				files: {
					'www/css/custom.css': 'www/css/custom.less'
				}
			}
		}
	});

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-less');

	// Default task(s).
	grunt.registerTask('default', ['less']);

};