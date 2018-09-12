'use strict';

const path = require('path');
const gutil = require('gulp-util');
const notify = require('gulp-notify');
const colors = gutil.colors;
const through = require('through2');

// Disable logger
notify.logLevel(0);

const log = (msg, opt) => {
  if (!opt) {
    opt = {};
  }

  const defaults = {
    type: 'good',
    color: null,
    space: true,
    file: false
  };

  Object.keys(defaults).forEach((key) => {
    if(!(key in opt)) {
      opt[key] = defaults[key];
    }
  });

  if (opt.type === 'bad') {
    opt.color = 'cyan';
  }

  const start = (opt.type === 'good' ? colors.green('✔') : colors.red('✘')) + ' ';

  let filename = opt.file ? path.basename(opt.file.relative) + (opt.space ? ' ' : '') : '';
  if (opt.color in colors) {
    filename = colors[opt.color](filename);
  }

  gutil.log(start + filename + msg);
};

const showNotification = (message, error) => {
  const opts = {
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

exports.pipeLog = (msg, opt) => {
  return through.obj(function (chunk, enc, callback) {
    if (!opt) {
      opt = {};
    }

    opt.file = chunk;
    log(msg, opt);

    this.push(chunk);
    callback();
  });
};

exports.lintReporter = (file, stream) => {
  let error,
    files = [];

  file.scsslint.issues.forEach((result) => {
    const base = path.basename(file.path);
    const position = colors.red(result.line) + ':' + colors.red(result.column);
    const detail = colors.yellow(result.linter) + ': ' + result.reason;

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

exports.sassReporter = function (error) {
  const message = colors.cyan(path.basename(error.file)) + ':' + colors.red(error.lineNumber) + ' ' + error.message;

  gutil.beep();
  log(message, {type: 'bad', space: false});
  showNotification(error.message, error);
  this.emit('end');
};

exports.genericReporter = function (error) {
  gutil.beep();

  log(error.message, {type: 'bad', space: false});
  showNotification(error.message, error);
  this.emit('end');
};
