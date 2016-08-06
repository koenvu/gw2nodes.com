window.ignoreNext = false;

var channel = pusher.subscribe('map');

channel.bind('App\\Events\\Version', function (data) {
	var $notifications = $('.Notifications');

	var box = '<div class="Notifications__entry">' +
                '<span class="Notifications__entry--close">&times;</span>' +
                '<span class="Notifications__entry--title">New version!</span>' +
                '<p>A new version of the site has been released.</p>' +
                '<div class="Notifications__entry__buttons">' +
                    '<a href="#" data-trigger="refresh-page">refresh</a>' +
                '</div>' +
            '</div>';

	$notifications.html(box + $notifications.html());
});

channel.bind('App\\Events\\NodeCreated', function (data) {
	if (data.node.server == server) {
		if (ignoreNext) {
			ignoreNext = false;
			return;
		}

		var $notifications = $('.Notifications');

		var box = '<div class="Notifications__entry">' +
					'<span class="Notifications__entry--close">&times;</span>' +
			        '<span class="Notifications__entry--title">New node!</span>' +
			        '<p>A new ' + data.container.name + ' node was added to ' + data.map.name + '.</p>' +
			        '<div class="Notifications__entry__buttons">' +
			            '<a href="#" data-trigger="view-node" data-node="' + data.node.id + '">view</a>' +
			        '</div>' +
			    '</div>';

		$notifications.html(box + $notifications.html());
	}
});

$('body').on('click', '.Notifications__entry--close', function (e) {
	var $this = $(this);
	$this.closest('.Notifications__entry').remove();
});

$('body').on('click', '[data-trigger="view-node"]', function (e) {
	e.preventDefault();

	var $this = $(this);
	$this.closest('.Notifications__entry').remove();
	focusNode = $this.data('node');
	mapRefresh();
});

$('body').on('click', '[data-trigger="refresh-page"]', function (e) {
	e.preventDefault();
	window.location.reload();
});

