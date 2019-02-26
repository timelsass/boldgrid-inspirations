/*
 * Reach out and hit the front end of the site to make sure all after theme switch hooks are fired.
 *
 * For this call, we do not want to fire any crons, this may trigger the framework resetting twice.
 * # We are sending this via POST because wp-cron.php aborts if $_POST has data.
 * # We are sending doing_wp_cron because the cron will not fire if that $_GET var exists.
 */
jQuery.ajax({
	type: "POST",
	url: IMHWPB.configs.site_url + "?doing_wp_cron=fire-after-theme-switch-hooks",
	data: { dummy_post_data: "Dummy post data" },
});