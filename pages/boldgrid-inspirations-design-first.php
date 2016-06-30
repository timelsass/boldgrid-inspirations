<?php
// Configure variables.
$lang = array(
	'Design' =>		__( 'Design', 'boldgrid-inspirations' ),
	'Content' =>	__( 'Content', 'boldgrid-inspirations' ),
	'CoinBudget' =>	__( 'Coin Budget', 'boldgrid-inspirations'),
	'Coins' =>		__( 'Coins', 'boldgrid-inspirations' ),
	'Category' =>   __( 'Category Filter', 'boldgrid-inspirations' ),
	'Pageset' =>	__( 'Pageset', 'boldgrid-inspirations' ),
	'Free' =>		__( 'Free', 'boldgrid-inspirations' ),
	'Desktop' =>    __( 'Enter desktop preview mode', 'boldgrid-inspirations' ),
	'Tablet' =>     __( 'Enter tablet preview mode', 'boldgrid-inspirations' ),
	'Mobile' =>     __( 'Enter mobile preview mode', 'boldgrid-inspirations' ),
);

?>
<div class="wrap">

	<div class="top-menu">
		<a class="active" data-step="design" ><?php echo $lang['Design'] ?></a>
		<a class="disabled" data-step="content" ><?php echo $lang['Content']; ?></a>
	</div>

	<div style="clear:both;"></div>

	<div id="screen-design">
		<div class="inspirations-mobile-toggle">
			<!-- Mobile Filter-->
				<div class="wp-filter">
					<ul class="filter-links">
						<li><a href="#" data-sort="show-all" class="current">Show All</a></li>
					</ul>
					<a class="drawer-toggle" href="#">Filter Themes</a>
					<div class="filter-drawer">
						<div class="buttons">
							<a class="apply-filters button button-secondary" href="#">Apply Filters<span></span></a>
							<a class="clear-filters button button-secondary" href="#">Clear</a>
						</div>
					</div>
				</div>
			<!-- End of Mobile Filter-->
		</div>
		<div class="left" id="categories">
			<strong><?php echo $lang['Category']; ?></strong>
		</div>
		<div class="theme-browser rendered right">
			<div class="themes wp-clearfix"></div>
		</div>
	</div>

	<div style="clear:both;"></div>

	<div id="screen-content" class="hidden" >
		<div class="inspirations-mobile-toggle">
			<!-- Mobile Filter-->
				<div class="wp-filter">
					<a class="drawer-toggle" href="#">Filter Themes</a>
				</div>
			<!-- End of Mobile Filter-->
		</div>
		<div class="left">
			<div class="content-menu-section">
				<div class="page-set-filter"><?php echo $lang['Pageset']; ?></div>
				<div id="pageset-options"></div>
			</div>
			<div class="content-menu-section">
				<div class="coin-filter"><?php echo $lang['CoinBudget']; ?> <span class="info-icon"></span></div>
				<div class="coin-option active">
					<input type="radio" name="coin-budget" data-coin="20" checked >
					<span class="pointer">0 - 20 <?php echo $lang['Coins']; ?></span>
				</div>
				<div class="coin-option">
					<input type="radio" name="coin-budget" data-coin="40">
					<span class="pointer">0 - 40 <?php echo $lang['Coins']; ?></span>
				</div>
				<div class="coin-option">
					<input type="radio" name="coin-budget" data-coin="60">
					<span class="pointer">0 - 60 <?php echo $lang['Coins']; ?></span>
				</div>
				<div class="coin-option">
					<input type="radio" name="coin-budget" data-coin="80">
					<span class="pointer">0 - 80 <?php echo $lang['Coins']; ?></span>
				</div>
				<div class="coin-option">
					<input type="radio" name="coin-budget" data-coin="0">
					<span class="pointer"><?php echo $lang['Free']; ?></span>
				</div>
			</div>
		</div>

		<div class="right">
			<div id="build-summary">
				<div style="float:left;">
					<span id="theme-title"></span>
					<span class ="summary-subheading">
						<span id="sub-category-title"></span> | <span id="build-cost">...</span> <?php echo $lang['Coins']; ?>
						<span class="devices">
							<button type="button" class="preview-desktop active" aria-pressed="true" data-device="desktop">
								<span class="screen-reader-text"><?php echo $lang['Desktop']; ?></span>
							</button>
							<button type="button" class="preview-tablet" aria-pressed="false" data-device="tablet">
								<span class="screen-reader-text"><?php echo $lang['Tablet']; ?></span>
							</button>
							<button type="button" class="preview-mobile" aria-pressed="false" data-device="mobile">
								<span class="screen-reader-text"><?php echo $lang['Mobile']; ?></span>
							</button>
						</span>
					</span>
				</div>
				<div style="float:right;">
					<button class="inspirations button button-secondary">Back</button>
					<button class="inspirations button button-primary">Install</button>
				</div>
			</div>

			<div style="clear:both;"></div>
			<iframe id="theme-preview"></iframe>
			<div class="loading-wrapper boldgrid-loading hidden"></div>
		</div>
	</div>

</div>

<form method="post" name="post_deploy" id="post_deploy" style="display: none;" action="admin.php?page=boldgrid-inspirations" >
	<input type="hidden" name="task"                           id="task"                           value="deploy" >
	<input type="hidden" name="_wpnonce"                       id="_wpnonce"                       value="0d14469600" >
	<input type="text"   name="boldgrid_cat_id"                id="boldgrid_cat_id"                value="-1" >
	<input type="text"   name="boldgrid_sub_cat_id"            id="boldgrid_sub_cat_id"            value="-1" >
	<input type="text"   name="boldgrid_theme_id"              id="boldgrid_theme_id"              value="-1" >
	<input type="text"   name="boldgrid_page_set_id"           id="boldgrid_page_set_id"           value="-1" >
	<input type="text"   name="boldgrid_api_key_hash"          id="boldgrid_api_key_hash"          value="87" >
	<input type="text"   name="boldgrid_new_path"              id="boldgrid_new_path"              value="0310254001464869383" >
	<input type="text"   name="boldgrid_pde"                   id="boldgrid_pde"                   value="" >
	<input type="text"   name="boldgrid_language_id"           id="boldgrid_language_id"           value="" >
	<input type="text"   name="boldgrid_build_profile_id"      id="boldgrid_build_profile_id"      value="" >
	<input type="text"   name="coin_budget"                    id="coin_budget"                    value="20" >
	<input type="text"   name="boldgrid_theme_version_type"    id="boldgrid_theme_version_type"    value="active" >
	<input type="text"   name="boldgrid_page_set_version_type" id="boldgrid_page_set_version_type" value="active" >
	<input type="text"   name="deploy-type"                                                        value="" >
	<input type="text"   name="pages"                                                              value="" >
	<input type="text"   name="staging"                                                            value="" >
	<input type="hidden" name="_wp_http_referer"                                                   value="/single-site/wp-admin/admin.php?page=boldgrid-inspirations&amp;boldgrid-tab=install" >
	<input type="hidden"                                       id="wp_language"                    value="en-US" >
	<input type="submit"                                                                           value="Deploy" >
</form>
