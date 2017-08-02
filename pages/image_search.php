<?php
// Prevent direct calls.
require BOLDGRID_BASE_DIR . '/pages/templates/restrict-direct-access.php';

$boldgrid_admin_notices = new Boldgrid_Inspirations_Admin_Notices();

$boldgrid_connection_issue_exists = $boldgrid_admin_notices->boldgrid_connection_issue_exists();

include BOLDGRID_BASE_DIR . '/pages/templates/image_search_results.php';
include BOLDGRID_BASE_DIR . '/pages/templates/attachment_details.php';
?>

<div class='media-frame imhwpb-media-frame mode-select wp-core-ui'>
	<div class="attachments-browser">
	<?php
	if ( ! $boldgrid_connection_issue_exists ) {
		?>
		<div class="media-toolbar">
			<div class="media-toolbar-secondary">
				<div>
					<strong>License filter:</strong><br /> <input type="checkbox"
						name='attribution' id='attribution' value='true' checked>
					Attribution
				</div>
			</div>
			<div class="media-toolbar-primary search-form">
				<form id='image_search'>
					<label class="screen-reader-text" for="media-search-input">Search Media</label>
					<input class="search" id="media-search-input" placeholder="Search" type="search" autofocus="autofocus">
					<input type='submit' class='button button-primary' value='Search'  disabled />
					<input type='hidden' name='free' id='free' value="true" />
					<input type='hidden' name='paid' id='paid' value='true' />
					<input type='hidden' name='palette' id='palette' value='all' />
				</form>
			</div>
		</div>
		<?php
	}
	?>
		<ul id="search_results"
			class="attachments ui-sortable ui-sortable-disabled media-image-search-results"
			tabindex="-1">

			<?php
			// Print a message for connection failure.
			$notice_template_file = BOLDGRID_BASE_DIR .
			'/pages/templates/boldgrid-connection-issue.php';

			if ( $boldgrid_connection_issue_exists &&
			! in_array( $notice_template_file, get_included_files(), true ) ) {
				include $notice_template_file;
			} else {
				/*
				 * Display a notice about possible explicit photos, only if the notice has not already
				 * been dismissed.
				 */
				if ( ! $boldgrid_admin_notices->has_been_dismissed( 'bgcs_license_info' ) ) {
				?>
				<div class="error notice is-dismissible boldgrid-admin-notice" data-admin-notice-id="bgcs_license_info">

					<ul class="fa-ul">
						<li>
							<p>
								<i class="fa-li fa fa-boldgrid" aria-hidden="true"></i>
								<?php _e( 'Indicates a purchasable image that comes with a license.', 'boldgrid-inspirations' ); ?>
							</p>
						</li>
						<li>
							<p>
								<i class="fa-li fa fa-globe" aria-hidden="true"></i>
								<?php
									printf(
										wp_kses(
											__( 'Indicates an image marked by the publisher as <a href="%1$s" target="_blank">Creative Commons</a>, but it is not a guarantee it is legally Creative Commons.  Those images may be subject to other copyrights.  You, as the website owner, are responsible for content on your site.', 'boldgrid-inspirations' ),
											array( 'a' => array( 'href' => array(), 'target' => array() ) )
										),
										'https://creativecommons.org/about/'
									);
								?>
							</p>
						</li>
					</ul>

					<hr />

					<p>
						<?php _e( 'While we\'ve tried our best to filter out any explicit images in search results, we cannot guarantee the content of all images in your search results.', 'boldgrid-inspirations' ); ?>
					</p>
				</div>
				<?php
				}
			}
			?>

			<input type='hidden' id='currently_searching' value='0' />
		</ul>
		<div id='attachment_details' class="media-sidebar visible"></div>
	</div>
</div>
