'use strict';

var path = require('path'),
	gutil = require('gulp-util'),
	notify = require('gulp-notify'),
	colors = gutil.colors,
	through = require('through2');

// Disable logger
notify.logLevel(0);

function log(msg, opt) {
	if (!opt) opt = {};

	var defaults = {
		type: 'good',
		color: null,
		space: true,
		file: false
	};

	Object.keys(defaults).forEach(function(key) {
		if(!(key in opt)) opt[key] = defaults[key];
	});

	if (opt.type == 'bad') {
		opt.color = 'cyan';
	}

	var start = (opt.type == 'good' ? colors.green('✔') : colors.red('✘')) + ' ',
		filename = opt.file ? path.basename(opt.file.relative) + (opt.space ? ' ' : '') : '';

	if (opt.color in colors) {
		filename = colors[opt.color](filename);
	}

	gutil.log(start + filename + msg);
};

function showNotification(message, error) {
	var opts = {
		title: 'Task ' + (error ? 'failed' : 'complete'),
		message: message,
		icon: path.resolve(__dirname, 'assets', 'fork.png')
	};

	if (error) {
		return notify.onError(opts)(error);
	}

	return notify(opts);
};

exports.notify = showNotification;

exports.log = log;

exports.pipeLog = function(msg, opt) {
	return through.obj(function(chunk, enc, callback) {
		if (!opt) opt = {};
		opt.file = chunk;

		log(msg, opt);

		this.push(chunk);
		callback();
	});
};

exports.lintReporter = function(file, stream) {
	var error,
		files = [];

	file.scsslint.issues.forEach(function(result) {
		var base = path.basename(file.path),
			position = colors.red(result.line) + ':' + colors.red(result.column),
			detail = colors.yellow(result.linter) + ': ' + result.reason;

		if (files.indexOf(base) === -1) {
			files.push(base);
		}

		log(':' + position + ' - ' + detail, {type: 'bad', space: false, file: file});
	});

	if (files.length) {
		gutil.beep();
		error = new gutil.PluginError('gulp-scss-lint', {
			message: 'SCSS lint failed for ' + files.join(', ')
		});

		showNotification(error.message, error);
		stream.emit('error', error);
	}
};

exports.sassReporter = function(error) {
	var message = colors.cyan(path.basename(error.file)) + ':' + colors.red(error.lineNumber) + ' ' + error.message;

	gutil.beep();
	log(message, {type: 'bad', space: false});
	showNotification(error.message, error);
	this.emit('end');
};

exports.genericReporter = function(error) {
	gutil.beep();

	log(error.message, {type: 'bad', space: false});
	showNotification(error.message, error);
	this.emit('end');
};
