grunt.loadNpmTasks('grunt-angular-modules-graph');
grunt.loadNpmTasks('grunt-graphviz');
 
grunt.initConfig({
  'modules-graph': {
    options: {
      // Task-specific options go here. 
    },
    your_target: {
      files: {
        'destination-file.dot': ['ng/*.js']
      }
    },
  },
});

