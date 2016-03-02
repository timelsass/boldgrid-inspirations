/**
 * When the user clicks "show / hide" log, toggle the display of the log.
 */
jQuery('a#toggle_view_deploy_log').on(
		'click',
		function() {
			var deploy_log_plugin_card = jQuery('ul#deploy_log').closest(
					'.plugin-card');
			deploy_log_plugin_card.slideToggle('slow');
		});

/**
 * As new lines are added to the deploy_log, update the line count.
 */
function update_deploy_log_line_count() {
	var deploy_log = jQuery('ul#deploy_log');
	var deploy_log_line_count = jQuery('.deploy_log_line_count');
	var line_count = deploy_log.find('li').length;

	deploy_log_line_count.html(line_count);
}