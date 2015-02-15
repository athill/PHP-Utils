var gulp = require('gulp'),
	less = require('gulp-less'),
	watch = require('gulp-watch');
 
gulp.task('less', function () {
console.log('less');
  gulp.src('less/*.less')
    .pipe(less())
    .pipe(gulp.dest('./css'));
});

gulp.task('default', ['less']);

var watcher = gulp.task('watch', function () {
    gulp.watch('less/*.less', ['less']);
});

watcher.on('change', function (event) {
   console.log('Event type: ' + event.type); // added, changed, or deleted
   console.log('Event path: ' + event.path); // The path of the modified file
});

