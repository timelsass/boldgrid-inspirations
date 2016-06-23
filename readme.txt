=== BoldGrid Inspirations ===
Contributors: imh_brad, joemoto, rramo012, timph
Tags: inspiration,customization,build,create,design
Requires at least: 4.3
Tested up to: 4.5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

BoldGrid Inspirations is an inspiration-driven plugin to assist with creating a fresh new website, or to customize an existing website.

== Description ==

BoldGrid Inspirations is an inspiration-driven plugin to assist with creating a fresh new website, or to customize an existing website.

The first phase is Inspiration; the guided tool creates your base website.  If you already have a website, then you can skip this step.

The second phase is Customization; tools to transform your website into your vision.

== Installation ==

1. Upload the entire boldgrid-inspirations folder to the /wp-content/plugins/ directory.

2. Activate the plugin through the Plugins menu in WordPress.

3. You will find the Inspirations menu in your WordPress Dashboard / admin panel.

== Changelog ==

= 1.1.8 In progress =
* Bug fix:		JIRA WPB-2041	Fixed BoldGrid theme update check in WordPress 4.6.
* Testing:		JIRA WPB-2046	Tested on WordPress 4.5.3.
* New feature:	JIRA WPB-599	Added options for plugin and theme auto-updates via WordPress autoupdater.
* Update:		JIRA WPB-2008	Deploy class updated to allow for is_generic flag.

= 1.1.7 =
* Bug fix:		JIRA WPB-2032	Fixed issue when activating key.  Added nonce to api key form.
* Rework:		JIRA WPB-2030	Updated the "I don't have an API key" section.
* New feature:	JIRA WPB-2029	Added TOS box to API key submission form.
* New feature:	JIRA WPB-1905	Added capability for auto-updates of boldgrid-inspirations by API response.
* Bug fix:		JIRA WPB-2002	Fixed theme update issue where upgrader says is up to date at times.
* Bug fix:		JIRA WPB-2006	Pdes and Homepage not installing correctly on Inpirations Theme Only installs.

= 1.1.6 =
* New feature:	JIRA WPB-1839	Users can now change their theme release channel.
* Security fix:	JIRA WPB-1977	Validate nonce for feedback form diagnostic data callback and form submit.
* Bug fix:		JIRA WPB-1955	Fatal error: Class 'Boldgrid_Staging_Plugin' not found.

= 1.1.5 =
* Bug fix:		JIRA WPB-1914	Staged image used on Active page not showing in cart.

= 1.1.4 =
* Bug fix:		JIRA WPB-1886	Fixed feedback notice being displayed too often (more than a week after submitting).
* New feature:	JIRA WPB-1183	Refresh the Library Tab after downloading an image.
* Update:		JIRA WPB-1865	Update style of 'Transactions' pages to better incorporate BoldGrid Staging's nav menu.
* Update:		JIRA WPB-1884	Passed WordPress 4.5.1 testing.
* Bug fix:		JIRA WPB-1855	Do not display feedback notice on update or setting pages.
* Bug fix:		JIRA WPB-1860	Fixed horizontal line through screenshot in step 2.
* Bug fix:		JIRA WPB-1863	Cart does not look for watermarked images used within staged pages.
* Bug fix:		JIRA WPB-1891	View / Download of images within receipts not working for images purchased via Staging.
* Bug fix:		JIRA WPB-1893	JS errors in console when viewing attachments.
* Bug fix:		JIRA WPB-1900	Attribution shows in menu when menu generated using wp_page_menu.

= 1.1.3 =
* Bug fix:		JIRA WPB-1824	Fixed order of plugin deactivation and uninstall in Start Over process.
* Bug fix:		JIRA WPB-1814	Fixed PHP notice in page and post editor for In Menu when there is a corrupted nav menu array.
* Bug fix:		JIRA WPB-1823	Fixed display of "Themes" H1 and the additional themes bar when choosing active or staging before installing a theme.
* Bug fix:		JIRA WPB-1840	Fixing thumbnail presentation in inspirations and add new theme.

= 1.1.2.3 =
* Update:				Sync version. See version 1.1.1.1.

= 1.1.2.2 =
* Bug fix:		JIRA WPB-1833	Fixed checking for previously downloaded assets in deployment when using multisite (wp-preview).

= 1.1.2.1 =
* Bug fix:		JIRA WPB-1817	BoldGrid Connect Search: Was not being added when changing a header image in the Customizer.
* Rework:		JIRA WPB-1541	Removed feedback form bug report diagnostic report items.
* Bug fix:		JIRA WPB-1816	Fixed update class interference with the Add Plugins page.

= 1.1.2 =
* Bug fix:		JIRA WPB-1809	Fixed undefined index "action" for some scenarios.  Optimized update class and addressed CodeSniffer items.
* Rework:		JIRA WPB-1541	Reworked admin feedback notice.
* Rework:		JIRA WPB-1751	Removed analysis processing and optional logging capabilities.  Added support for XHProf.
* Bug fix:		JIRA WPB-1805	Now adds theme update info on the Customizer Themes page.
* Rework:		JIRA WPB-1785	Enabled and reworked image caching for the preview server.
* Rework:		JIRA WPB-1751	Reworked analysis processing.
* Update:		JIRA WPB-1658	Storing more reliable install data through inspirations.
* Bug fix:		JIRA WPB-1787	When not using BoldGrid menu, cart does not dynamically update total page price.
* Update:		JIRA WPB-1754	Remove attribution page from search results.
* Bug fix:		JIRA WPB-1788	webkit css missing from 'new from gridblocks'.
* New feature:	JIRA WPB-1806	Add 'BoldGrid search' tab when replacing an image.

= 1.1.1.1 =
* Bug Fix:						Fixing logo display on login screen.

= 1.1.1 =
* Bug fix:						Fixed analysis include for preview server.
* Bug fix:						New From GridBlocks: Asset download issues.

= 1.1 =
* New feature:	JIRA WPB-1751	Added analysis processing and optional logging capabilities.
* Bug fix:		JIRA WPB-1781	Removed boldgrid_dismissed_admin_notices from Start Over cleanup.
* New feature:	JIRA WPB-1541	Added feedback notice.
* Bug fix:		JIRA WPB-1747	New From GridBlocks: For non BoldGrid themes, only load grid css.
* Bug fix:		JIRA WPB-1760	New From GridBlocks: Ensure page title shows on preview page.
* Update:		JIRA WPB-1779	New From GridBlocks: Update verbiage for 'Downloading GridBlocks'.

= 1.0.12.1 =
* Bug fix:		JIRA WPB-1710	Fixed missing device preview tabs on Add New Theme preview modal.
* Bug fix:		JIRA WPB-1710	Fixed notice dismissal checking.
* Bug fix:		JIRA WPB-1749	On start over, staging menus are not deleted.
* Bug fix:		JIRA WPB-1755	Gallery images not showing in cart.

= 1.0.12 =
* Bug fix:		JIRA WPB-1740	Fixed "In Menu" messages in editor when staging plugin is not active, and fixed saving menu selections.
* New feature:	JIRA WPB-1726	Added optional feedback for GridBlock Add Page.
* Removed Ft:	JIRA WPB-1710	Removed Inspirations Add Pages; replaced by GridBlocks.
* Misc:			JIRA WPB-1361	Added license file.
* New feature:					Don't assign footer contact widget if using base pagesets.
* Bug Fix:		JIRA WPB-1732	Fixing css issues on login screen (firefox).
* Bug Fix:		JIRA WPB-1687	Image search: Title, Caption, Alt Text and Description do not display on new pages.

= 1.0.11 =
* New feature:	JIRA WPB-1699	Added optional feedback for theme activation.
* New feature:  JIRA WPB-1690   Adding BoldGrid themes to All themes install menu.
* Bug fix:		JIRA WPB-1686	Limited items loaded in network admin pages.
* Improvement:	JIRA WPB-1604	Added a "Cancel" link to the "In Menu" section.
* Improvement:	JIRA WPB-1603	Display menu locations in the editor "In Menu" section.
* Bug fix:		JIRA WPB-1602	Corrected capitalization of "None" under "In menu" in the editor.
* Improvement:	JIRA WPB-1664	Gets api_key and site_hash from configs instead of get_option.
* Bug fix:		JIRA WPB-1597	Fixing indefined index error
* New feature:	JIRA WPB-1649	Added reporting of PHP version and mobile ratio.
* Bug fix:		JIRA WPB-1598	'Mine' count on 'all pages' is incorrect.
* Bug fix:		JIRA WPB-1647	JS error with easy-attachment-preview-size.js.
* Bug fix:		JIRA WPB-1651	When the BG menu is turned off, Appearance link should take you to themes.

= 1.0.10 =
* Bug fix:		JIRA WPB-1632	Fixed handling of subcategory_id in deploy_page_sets.
* New feature:	JIRA WPB-1510	Moved adhoc functions.php to class-boldgrid-inspirations-utility.php (class Boldgrid_Inspirations_Utility).
* Rework:		JIRA WPB-1553	Updated require and include statements for standards.
* Bug fix:		JIRA WPB-1563	Updated pages in which wp_iframe-media_upload.css is loaded.
* Bug fix:		JIRA WPB-1549	Resolve attribution page missing attribution for several images.png.

= 1.0.9.2 =
* Bug fix:						Add GridBlock Sets feature disabled.

= 1.0.9.1 =
* Bug fix:		JIRA WPB-1553	Fixed support for PHP 5.2 to deactivate plugin.
* Bug fix:						Prevent click of links in add_new_page_selection previews.
* Bug fix:		JIRA WPB-1554	Fixed undefined JavaScript variable pagenow for customizer link.

= 1.0.9 =
* Bug fix:		JIRA WPB-1554	Fixed theme link in network dashboard nav menu.
* Bug fix:		JIRA WPB-1590	Fixed JavaScript error for undefined screen info in network dashboard.
* Bug fix:		JIRA WPB-1535	Fixed theme deployment issues.
* New feature:	JIRA WPB-1584	Added an opt-out feedback payload delivery system.
* New feature:	JIRA WPB-1580	Added optional feedback for customizer_start.
* Bug fix:		JIRA WPB-1571	Removed plugin dependency admin notice when editing an attachment (image).
* New feature:	JIRA WPB-1579	Added feedback opt-out in BoldGrid Settings, hidden for now.
* Bug fix:  	JIRA WPB-1575	Addressed an issue causing mismatch color palettes on cached previews
* New feature:	JIRA WPB-1514	Add new pages offers page templates to choose from.

= 1.0.8.1 =
* Bug fix:		JIRA WPB-1553	Fixed PHP version check condition (<5.3).

= 1.0.8 =
* Bug fix:		JIRA WPB-1561	Fixed missing get_plugin_data on update calls.
* New feature:	JIRA WPB-1511	Added dependency plugin notice on editor pages.
* Bug fix:		JIRA WPB-1553	Added support for __DIR__ in PHP <=5.2.
* Bug fix:		JIRA WPB-1371	JSON encoded image data for media download requests.
* New feature:  JIRA WPB-1332   Swapping loading GIF to CSS loading image.
* New feature:	JIRA WPB-1072	Storing static pages on install
* New feature:	JIRA WPB-1539	When deleting a page, remove it from any applicable menus as well.
* New feature	JIRA WPB-1542	Manage menu assignment within editor.
* New feature	JIRA WPB-1555	Add wp-image-## class to images during deployment.
* New feature	JIRA WPB-1557	Add wp-image-## class to images when adding gridblocks.
* Bug fix:		JIRA WPB-1506	Theme naming missing in preview.
* Bug fix:		JIRA WPB-1443	Extra page listed under 'Mine'.
* Bug fix:		JIRA WPB-1560	Install options not available on preview server

= 1.0.7 =
* Rework:		JIRA WPB-1533	Ensured activation data is sent after first login.

= 1.0.7 =
* Rework:		JIRA WPB-1533	Ensured activation data is sent after first login.

= 1.0.6 =
* Rework:		JIRA WPB-1411	Added more output to the deploy log.

= 1.0.5 =
* Bug fix:		JIRA WPB-1462	Fixed position of dependency plugins admin notice.  Also limited to Dashboard and plugins page.
* Bug fix:		JIRA WPB-1290	Fixing issues with galleries leaving empty spaces
* Bug fix:		JIRA WPB-1471	Made deployment plugin installation respect release channel.
* Rework:		JIRA WPB-1452	Remove unneeded call to 'boldgrid_activate_framework' during deployment.
* Bug fix:		JIRA WPB-946	Fixed margin bug on step 2 additional themes.
* Bug fix:		JIRA WPB-1384	Increase width of select input on image search modal.
* Bug fix:		JIRA WPB-1508	BoldGrid Image search box size is inconsistent.

= 1.0.4 =
* Bug fix:		JIRA WPB-1442	Fixing inspiration border styles for wordpress 4.4
* Bug fix:		JIRA WPB-1461	Updating login button styles for wordpress 4.4
* Bug fix:		JIRA WPB-1411	Added initialization and checks for empty image queues in deployment.
* Bug fix:		JIRA WPB-1406	Attribution page still showing in 'All Pages'.
* Bug fix:		JIRA WPB-1451	Active images are showing in Staging attribution page.
* Bug fix:		JIRA WPB-1466   Tabs on tutorials page too small at 1035px - 1482px.

= 1.0.3 =
* New feature:	JIRA WPB-1363	Updated readme.txt for WordPress standards.
* New feature:	JIRA WPB-1389	When starting over theme mods are saved with a flag to recompile sass
* Bug fix:		JIRA WPB-1420	Content of Attribution page is overwriting page saves.

= 1.0.2 =
* Bug fix:		JIRA WPB-1395	Adjusted theme update data; now gets theme uri from theme style.css, download url from api data.
* Rework		JIRA WPB-1374	Updated activation timestamp to use GMT/UTC.
* Bug fix:		JIRA WPB-1377	Reseller option is now set on first call to either the front end or wp_login.
* Bug fix:						Adjusted handling for image purchases when errors occur.
* Bug fix:		JIRA WPB-1365	Purchase link on editing a page goes to wrong link.
* Bug fix:		JIRA WPB-1368	Inspirations step 0 text refers to nonexisting help tabs.
* Rework:		JIRA WPB-1378	Adjusted formatting of footer in Dashboard.
* Rework:		JIRA WPB-1369	Update minus signs on 'Transaction History'.
* New feature:	JIRA WPB-1379	On the transactions page, show the reseller that processed the credits.
* Bug fix:						Count of 'All' pages inaccurate on 'All pages'.
* Bug fix:		JIRA WPB-1367	Updated link for 'Lost your BoldGrid Connect Key?'.

= 1.0.1 =
* Bug fix:		JIRA WPB-1374	Updated activation timestamp to include timezone in UTC.
* Bug fix:						Attribution page shows style tags.
* Bug fix:						Strict Standards fix for wp_kses_allowed_html.
* Bug fix:						Fixed incorrect link.

= 1.0 =
* Initial public release.

== Upgrade Notice ==

= 1.0.2 =
Users should upgrade to version 1.0.2 to ensure proper BoldGrid theme updates.
