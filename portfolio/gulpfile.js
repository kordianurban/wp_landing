var gulp = require('gulp');
var sass = require('gulp-sass');
var babel = require('gulp-babel');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var errorHandler = require('gulp-error-handle');
var gutil = require('gulp-util');

const logError = function(err) {
    gutil.log(err);
    // this.emit('end');
};

gulp.task('sass', function(){
    return gulp.src('assets/sass/dist.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({
            outputStyle: 'compressed',
            precision: 10,
            includePaths: ['.']
        }))
        .on('error', console.error.bind(console))
        // .pipe(errorHandler(logError))
        .pipe(autoprefixer({
            browsers: [
                'last 5 versions',
                'ie >= 9',
                'opera 12',
                'android 4'
            ],
            cascade: false
        }))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('assets/css/'))
});

// gulp.task('es', function(){
// 	return gulp.src('assets/es/scripts.js')
// 		.pipe(babel())
// 		.pipe(gulp.dest('assets/js/'))
// });

gulp.task('watch',function(){
    // gulp.watch('assets/es/*.js', ['es']);
    gulp.watch('assets/sass/*.scss', gulp.series('sass') );
});

gulp.task('default', function() {
    // gulp.start('es');
    gulp.start('sass');
});