module.exports = function( grunt ) {

	'use strict';

	// Project configuration
	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),

		// Configure the SASS task
		sass: {
			dist: {
				files: {
					"assets/css/main.css": "assets/scss/main.scss", // Target: destination file : source file
				},
				options: {
					style: "expanded",
					sourcemap: "auto", // Generate source maps for debugging
				},
			},
		},

		// Watch task to automatically run SASS compilation on changes
		watch: {
			css: {
				files: ["assets/scss/**/*.scss"],
				tasks: ["sass"], // Run the SASS task when any .scss file changes
				options: {
					spawn: false, // Speed up by preventing a new process from spawning
				},
			},
		},

		addtextdomain: {
			options: {
				textdomain: "wp-popup-plugin",
			},
			update_all_domains: {
				options: {
					updateDomains: true,
				},
				src: [
					"*.php",
					"**/*.php",
					"!.git/**/*",
					"!bin/**/*",
					"!node_modules/**/*",
					"!tests/**/*",
				],
			},
		},

		wp_readme_to_markdown: {
			your_target: {
				files: {
					"README.md": "readme.txt",
				},
			},
		},

		makepot: {
			target: {
				options: {
					domainPath: "/languages",
					exclude: [".git/*", "bin/*", "node_modules/*", "tests/*"],
					mainFile: "wp-popup-plugin.php",
					potFilename: "wp-popup-plugin.pot",
					potHeaders: {
						poedit: true,
						"x-poedit-keywordslist": true,
					},
					type: "wp-plugin",
					updateTimestamp: true,
				},
			},
		},
	});

	grunt.loadNpmTasks("grunt-wp-i18n");
	grunt.loadNpmTasks("grunt-wp-readme-to-markdown");
	grunt.loadNpmTasks("grunt-contrib-sass");
	grunt.loadNpmTasks("grunt-contrib-watch");
	grunt.registerTask("default", ["i18n", "readme", "sass", "watch"]);
	grunt.registerTask("i18n", ["addtextdomain", "makepot"]);
	grunt.registerTask("readme", ["wp_readme_to_markdown"]);

	grunt.util.linefeed = '\n';

};
