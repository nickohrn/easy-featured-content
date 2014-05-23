jQuery(document).ready(function($) {
	$(document).on('click', '.is-featured-content-toggle', function(event) {
		event.preventDefault();

		var $link = $(this),
			is_requesting = 'yes' === $link.attr('data-is-requesting');

		if(!is_requesting) {
			$link.attr('data-is-requesting', 'yes');

			$.ajax({
				data: { 'is-ajax': 'yes' },
				dataType: 'json',
				success: function(data, status) {
					$link.attr('href', data.link);
					$link.text(data.text);
					$link.removeAttr('data-is-requesting');
				},
				url: $link.attr('href')
			});
		}
	});
});
