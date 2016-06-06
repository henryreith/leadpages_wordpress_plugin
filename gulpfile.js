var gulp  = require('gulp');
var shell = require('gulp-shell');

gulp.task('setupTestEnv', shell.task([
    "bash bin/install-wp-tests.sh wordpress2 root root 127.0.0.1 latest"
]));