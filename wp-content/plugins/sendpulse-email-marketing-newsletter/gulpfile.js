var gulp = require('gulp');

gulp.task('css', function () {
    gulp.src(['assets/css/*.css', '!assets/css/*.min.css'])
        .pipe(require('gulp-cssnano')())
        .pipe(require('gulp-rename')({extname: '.min.css'}))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('js', function () {
    gulp.src(['assets/js/*.js', '!assets/js/*.min.js'])
        .pipe(require('gulp-minify')({
            ext: {
                min: '.min.js'
            }
        }))
        .pipe(gulp.dest('assets/js'));
});


gulp.task('pot', function () {
    return gulp.src(['inc/*.php', 'sendpulse-newsletter.php'])
        .pipe(require('gulp-wp-pot')({
            domain: 'sendpulse-email-marketing-newsletter',
            package: 'SendPulse Email Marketing Newsletter'
        }))
        .pipe(gulp.dest('languages/sendpulse-email-marketing-newsletter.pot'));
});

gulp.task('svn', function () {
    gulp.src(['**/*', '!node_modules', '!node_modules/**'], {base: "."})
        .pipe(gulp.dest('../../svn/sendpulse-email-marketing-newsletter/trunk'));

});

gulp.task('zip', function () {
    gulp.src(['../sendpulse-email-marketing-newsletter/**/*', '!node_modules', '!node_modules/**', '!tests', '!tests/**', '!.travis.yml', '!phpcs.ruleset.xml', '!phpunit.xml.dist', '!bin', '!bin/**'], {base: "../"})
        .pipe(require('gulp-zip')('sendpulse-email-marketing-newsletter.zip'))
        .pipe(gulp.dest('../../dist'));

});

gulp.task('watch', function () {
    gulp.watch('assets/css/*.css', ['css']);
    gulp.watch('assets/js/*.js', ['js']);
});

gulp.task('prod', ['css', 'js', 'pot']);