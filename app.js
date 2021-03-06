var express = require('express');
var app = express();
var stylus = require('stylus');
var nib = require('nib');
var http = require('http');
var path = require('path');
var socket_io = require('socket.io');

var server = http.createServer(app).listen(1338, function () {
	console.log('System Panel server listening on port 1338');
});

var io = socket_io.listen(server, {
	log : false
});

function compile(str, path) {
	return stylus(str).set('filename', path).use(nib());
}
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'jade');
app.use(stylus.middleware( { src: path.join(__dirname,'public'), compile: compile } ));
app.use(express.static(path.join(__dirname,'public')));
app.use(express.favicon());
app.use(express.urlencoded());
app.use(express.json());
app.use(express.methodOverride());
app.use(express.errorHandler());
app.use(express.cookieParser());
app.use(express.session({ secret: '1234567890ABCDEF' }));
app.use(app.router);
app.use('/tmp', express.static(__dirname + '/tmp'));

app.get('/', function(req, res, next) {
	require('./index.js').page(req, res, app, next);
});

app.get('/login', function(req, res, next) {
	require('./login.js').page(req, res, app, next);
});

app.post('/login', function(req, res, next) {
	require('./login.js').page(req, res, app, next);
});

app.get('/logout', function (req, res, next) {
	require('./logout.js').page(req, res, app, next);
});

app.get('/settings', function(req, res, next) {
	require('./settings.js').page(req, res, app, next);
});

app.post('/settings', function(req, res, next) {
	require('./settings.js').page(req, res, app, next);
});

app.get('/widgetcreate', function (req, res, next) {
	require('./widgetcreate.js').page(req, res, app, next);
});

function getUserWidget(data, cb) {
	var settings = require('./framework/settings.js');

	settings.get_user_info(data.username, function(user) {
		if (user != null) {
			var user_widget = null;

			for (var c = 0; c < user.widgets.length && !user_widget; c++) {
				if (user.widgets[c].id == data.widget_id) {
					user_widget = user.widgets[c];
				}
			}

			if (user_widget) {
				var loaded_widget = require(user_widget.widget);
				loaded_widget.manage_post(data.post_params, function(result, output) {
					if (result) {
						cb(user_widget, 200, output);
					} else {
						loaded_widget.data(function(widget_data) {
							if (widget_data != null) {
								if (data.json) {
									cb(user_widget, 200, 'application/json', widget_data);
								} else {
									app.set('views', path.join(__dirname ,'node_modules', user_widget.widget ,'views'));
									app.render(user_widget.widget.replace('spw-', ''), {
										data : widget_data,
										user_widget : user_widget,
										username : data.username
									}, function(err, output) {
										app.set('views', path.join(__dirname,'views'));
										if (!err) {
											cb(user_widget, 200, output);
										} else {
											cb(null, 500, '');
										}
									});
								}
							} else {
								cb(user_widget, 500, '');
							}
						});
					}
				});
			}
		} else {
			cb(null, 500, '');
		}
	});
}

function emitUserWidget(socket, data, cb) {
	getUserWidget(data, function(user_widget, statusCode, output) {
		socket.emit(data.event_name, {
			output : output,
			user_widget : user_widget,
			statusCode : statusCode
		});

		if (cb) {
			cb(user_widget);
		}
	});
}

function widgetUpdatingCallback(socket, data, old_user_widget) {
	if (!socket.disconnected) {
		emitUserWidget(socket, data, function (user_widget) {
			if (user_widget == null) {
				user_widget = old_user_widget != null ? old_user_widget : {
					widget : {
						updatetime : 1000
					}
				};
			}

			if (user_widget.widget.updatetime > 0 && !socket.disconnected) {
				setTimeout(function() {
					if (!socket.disconnected) {
						widgetUpdatingCallback(socket, data, user_widget);
					}
				}, user_widget.widget.updatetime);
			}
		});
	}
}

io.sockets.on('connection', function(socket) {
	socket.on('request_updating', function(data) {
		data.event_name = 'updated_data_' + data.widget_id;
		widgetUpdatingCallback(socket, data, null);
	});

	socket.on('post_data', function (data) {
		data.event_name = 'post_data_' + data.widget_id;
		emitUserWidget(socket, data, function (user_widget) { });
	});

	socket.on('request_first_data', function(data) {
		data.event_name = 'first_use_data';
		emitUserWidget(socket, data, function (user_widget) { });
	});
});
