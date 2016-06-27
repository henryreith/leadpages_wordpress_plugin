var gulp  = require('gulp');
var del = require('del');
var zip = require('gulp-zip');
var shell = require('gulp-shell');
var composer = require('gulp-composer');
var runSequence = require('run-sequence');

gulp.task('setupTestEnv', shell.task([
    "bash bin/install-wp-tests.sh wordpress2 root root 127.0.0.1 latest"
]));


var releaseFolder = '/Users/brandonbraner/projects/releases/leadpages-wordpress-v2/beta2/leadpages';
var zipsFolder = '/Users/brandonbraner/projects/releases/leadpages-wordpress-v2/archive/';

gulp.task('removeallfiles',function(){
    return del([releaseFolder+'/**/*'], {force: true});
});

//compress whole folder and move it just incase

gulp.task('compressandmove', function(){
    var date = new Date();
    return gulp.src('.')
        .pipe(zip('archive '+date+'.zip'))
        .pipe(gulp.dest(zipsFolder));
});

gulp.task('runcomposer', function(){
    return composer("update --no-dev");
});

gulp.task('movetoreleases', function(){

    return gulp.src(['**/*'], {"base" : "."})
        .pipe(gulp.dest(releaseFolder));
});

gulp.task('removenode',function(){
    return del([releaseFolder+'/node_modules'], {force: true});
});

gulp.task('runcomposer2', function(){
    return composer("update");
});

gulp.task('deploy', function(){
    runSequence(
        'compressandmove',
        'removeallfiles',
        'runcomposer',
        'movetoreleases',
        'removenode',
        'runcomposer2'
    );
});
