/**
 * Created by KennyDaren on 22.07.17.
 */

var app = {
	shortenerState: 0,
	lastUrl: null,
	init: function () {
		$.nette.init();
		this.initShortener();
		this.initGraphs()
	},
	initShortener: function () {
		var $shortener = $('[data-shortener-url]');
		var $form = $shortener.closest('form');
		var $input = $shortener.find('input[type="text"]');
		var $status = $shortener.find('[data-shortener-status]');
		var $button = $shortener.find('input[type="submit"]');
		var $copy = $shortener.find('[data-shortener-copy]');

		if ($copy.length > 0) {
			var clipboard = new Clipboard($copy[0]);
			clipboard.on('success', function () {
				$status.html('Copied!')
			});

			clipboard.on('error', function () {
				$status.html('Press Ctrl/Cmd + C!')
			});
		}

		$form.on('submit', function (e) {
			e.preventDefault();

			if (!app.isURL($input.val())) {
				$status.html('URL is not valid :(');
				$input.select();
				return;
			}

			$.nette.ajax({
				url: $shortener.attr('data-shortener-url').replace('__REPLACE__', $input.val()),
				success: function (payload) {
					if (typeof payload.status !== "undefined") {
						$status.html(payload.status);
					}
					if (typeof payload.url !== "undefined" && payload.url.length > 0) {
						$input.val(payload.url);
						clipboard.text = function () {
							return payload.url
						};
						$button.hide();
						$copy.show();
						app.lastUrl = payload.url;
						app.shortenerState = 1;
						$input.select();
					}
				}
			});
		});

		$input.on('change keydown paste', function (e) {
			if (app.shortenerState === 1 && app.lastUrl !== $input.val()) {
				$button.show();
				$copy.hide();
				app.shortenerState = 0;
				app.lastUrl = null;
			}
		});
	},
	initGraphs: function () {
		$('[data-graph]').each(function (index,item) {
			var id = $(item).attr('id');
			var type = $(item).attr('data-graph');
			var data = $(item).attr('data-graph-data');


			if (type === 'bars') {
				var xkey = $(item).attr('data-graph-xkey');
				var ykeys = $(item).attr('data-graph-ykeys');
				var labels = $(item).attr('data-graph-labels');

				Morris.Bar({
					element: id,
					data: JSON.parse(data),
					xkey: xkey,
					ykeys: JSON.parse(ykeys),
					labels: JSON.parse(labels)
				});
			}else if (type ==='donut'){
				Morris.Donut({
					element: id,
					data: JSON.parse(data)
				});
			}

		});
	},
	isURL: function (str) {
		var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
			'((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|' + // domain name
			'((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
			'(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
			'(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
			'(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
		return pattern.test(str);
	}
};

$(function () {
	app.init();

	$("#menu-toggle").on('click', function (e) {
		e.preventDefault();
		$("#wrapper").toggleClass("toggled");
	});
});
