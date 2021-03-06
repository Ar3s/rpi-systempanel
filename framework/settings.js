exports.get_user_info = function(username, cb) {
	var json = require('./settings.json');
	var user = null;

	for (var i = 0; i < json.length && !user; i++)
	{
		if (json[i].username == username) {
			user = json[i];
		}
	}

	cb(user);
};

exports.check_login = function(username, password, cb) {
	exports.get_user_info(username, function(user) {
		if (user && user.password == password) {
			cb(true);
		} else {
			cb(false);
		}
	});
};

exports.toggleWidgetVisibility = function(username, widget_id, visibility, cb) {
	exports.get_user_info(username, function (user) {
		for (var i = 0; i < user.widgets.length; i++)
		{
			var widget = user.widgets[i];
			if (widget.id_html == widget_id) {
				widget.visible = visibility;
				user.widgets[i] = widget;
			}
		}

		exports.save_user(user, function () {
			cb(true);
		});
	});
};

exports.toggleWidgetState = function(username, widget_id, enabled, cb) {
	exports.get_user_info(username, function (user) {
		for (var i = 0; i < user.widgets.length; i++)
		{
			var widget = user.widgets[i];
			if (widget.id_html == widget_id) {
				widget.enabled = enabled;
				user.widgets[i] = widget;
			}
		}

		exports.save_user(user, function () {
			cb(true);
		});
	});
};

exports.save_user = function(user, cb) {
	var fs = require('fs');
	var json = require('./settings.json');

	for (var i = 0; i < json.length && !user; i++)
	{
		if (json[i].username == user.username) {
			json[i] = user;
		}
	}

	fs.writeFile('./settings.json', JSON.stringify(json, null, 4), function(err) {
		cb(!err);
	});
};

exports.create_user_widget = function(username, widget, cb) {
	exports.get_user_info(username, function (user) {
		user.widgets.push(widget);
		exports.save_user(user, function () {
			cb(true);
		});
	});
};

exports.save_user_widget = function(username, widget, cb) {
	exports.get_user_info(username, function (user) {
		for (var i = 0; i < user.widgets; i++) {
			var user_widget = user.widgets[i];
			if (user_widget.id_html == widget.id_html) {
				user_widget = widget;
				user.widgets[i] = user_widget;
			}
		}
		exports.save_user(user, function () {
			cb(true);
		});
	});
};

exports.delete_user_widget = function(username, widget, cb) {
	exports.get_user_info(username, function (user) {
		for (var i = 0; i < user.widgets; i++) {
			var user_widget = user.widgets[i];
			if (user_widget.id_html == widget.id_html) {
				user.widgets.splice(i,1);
			}
		}
		exports.save_user(user, function () {
			cb(true);
		});
	});
};