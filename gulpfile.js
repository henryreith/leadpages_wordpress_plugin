var gulp = require('gulp');
var del = require('del');
var composer = require('gulp-composer');
var runSequence = require('run-sequence');

var releaseFolder = '/Users/brandonbraner/projects/releases/leadpages-wordpress-v2/beta/leadpages';

gulp.task('removeallfiles',function(){
    return del([releaseFolder+'/**/*'], {force: true});
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
        'removeallfiles',
        'runcomposer',
        'movetoreleases',
        'removenode',
        'runcomposer2'
    );
});
